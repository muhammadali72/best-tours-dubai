<?php
function sitemapPageGroups(array $pages):array{
 $groups=['pages'=>[],'tours'=>[],'attractions'=>[],'guides'=>[]];$seen=[];
 foreach($pages as $page){
  if($page['status']!=='published'||!empty($page['deletedAt'])||($page['seo']['noindex']??false)||(!empty($page['publishedAt'])&&strtotime($page['publishedAt'])>time()))continue;
  $path=publicPath($page);
  if(isset($seen[$path])||str_contains($path,'?')||in_array($path,['/booking.php','/search.php','/thank-you','/dashboard']))continue;
  $group=['page'=>'pages','tour'=>'tours','attraction'=>'attractions','guide'=>'guides'][$page['type']]??null;
  if(!$group)continue;$seen[$path]=true;$groups[$group][]=$page;
 }
 return $groups;
}
function sitemapModified(array $page):?string{
 foreach(['modifiedAt','publishedAt'] as $key)if(!empty($page[$key])){$timestamp=strtotime($page[$key]);if($timestamp!==false&&$timestamp<=time())return gmdate('c',$timestamp);}
 return null;
}
function serveSeoSitemap(string $path,array $pages):void{
 header('Content-Type: application/xml; charset=UTF-8');header('Cache-Control: no-cache');header('X-LiteSpeed-Cache-Control: no-cache');
 $groups=sitemapPageGroups($pages);
 echo '<?xml version="1.0" encoding="UTF-8"?>';
 if($path==='/sitemap-index.xml'){
  echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
  foreach($groups as $type=>$items){if(!$items)continue;echo '<sitemap><loc>'.e(SITE_ORIGIN.'/sitemaps/'.$type.'.xml').'</loc>'; $dates=array_values(array_filter(array_map('sitemapModified',$items)));if($dates)echo '<lastmod>'.e(max($dates)).'</lastmod>';echo '</sitemap>';}
  echo '</sitemapindex>';exit;
 }
 $items=$path==='/sitemap.xml'?array_merge(...array_values($groups)):($groups[basename($path,'.xml')]??[]);
 echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
 foreach($items as $page){echo '<url><loc>'.e(SITE_ORIGIN.publicPath($page)).'</loc>';$date=sitemapModified($page);if($date)echo '<lastmod>'.e($date).'</lastmod>';
  $image=$page['images'][0]['src']??'';if($image&&validImage($image)){echo '<image:image><image:loc>'.e(str_starts_with($image,'https://')?$image:SITE_ORIGIN.$image).'</image:loc></image:image>';}
  echo '</url>';
 }
 echo '</urlset>';exit;
}

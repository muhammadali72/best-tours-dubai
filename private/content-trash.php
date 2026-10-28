<?php
function contentTrash(array $request):void{
 $path=$request['path']??null;
 if(!is_string($path)||strlen($path)>1000)throw new Exception('Choose a valid content item.');
 db()->exec('BEGIN IMMEDIATE');
 try{
  $page=null;foreach(allContent(true)['pages'] as $candidate)if($candidate['path']===$path){$page=$candidate;break;}
  if(!$page){db()->exec('ROLLBACK');jsonResponse(['error'=>'Content not found.'],404);}
  if(!in_array($page['type'],['tour','attraction','guide'],true))throw new Exception('Only tours, attractions and guides can be moved to Trash.');
  if($request['action']==='delete')$page['deletedAt']=$page['deletedAt']??gmdate('c');
  else unset($page['deletedAt']);
  save('page:'.$path,$page);db()->exec('COMMIT');
 }catch(Throwable $error){db()->exec('ROLLBACK');throw $error;}
 jsonResponse(['ok'=>true,'page'=>$page]);
}

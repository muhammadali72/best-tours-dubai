<?php
function updateBookingStatus(string $key,string $status):array{
 db()->exec('BEGIN IMMEDIATE');
 try{
  $raw=query('SELECT value FROM cms WHERE key=?',[$key])->fetchColumn();
  if(!$raw){db()->exec('ROLLBACK');return ['ok'=>false,'error'=>'Request not found.','httpStatus'=>404];}
  $b=json_decode($raw,true,512,JSON_THROW_ON_ERROR);
  $changed=$b['status']!==$status;
  if(!$changed){
   $events=$b['statusNotifications']??[];$last=$events?end($events):null;
   $alreadySent=$last&&$last['status']===$status&&($last['transport']??'')==='smtp'&&$last['delivery']==='sent';
   $inProgress=$last&&$last['status']===$status&&$last['delivery']==='queued'&&strtotime($last['createdAt'])>time()-300;
   if(!in_array($status,['confirmed','cancelled'],true)||$alreadySent||$inProgress){db()->exec('COMMIT');return ['ok'=>true,'changed'=>false,'customerNotification'=>'not_needed'];}
  }
  $b['status']=$status;$id=bin2hex(random_bytes(12));$send=in_array($status,['confirmed','cancelled'],true);
  if($send){$b['statusNotifications']??=[];$b['statusNotifications'][]=['id'=>$id,'status'=>$status,'email'=>$b['customerEmail'],'createdAt'=>gmdate('c'),'delivery'=>'queued','transport'=>'smtp'];}
  save($key,$b);db()->exec('COMMIT');
 }catch(Throwable $error){db()->exec('ROLLBACK');throw $error;}
 if(!$send)return ['ok'=>true,'changed'=>true,'customerNotification'=>'not_needed'];
 $s=allContent()['settings'];$date=DateTime::createFromFormat('!Y-m-d',$b['travelDate']);
 $intro=$status==='confirmed'?'Your booking has been confirmed. We look forward to welcoming you!':'Your booking request has been cancelled.';
 $details=['Reference: '.$b['reference'],'Experience: '.$b['title'],'Preferred date: '.($date?$date->format('j F Y'):$b['travelDate']),'Package: '.$b['packageName'],'Guests: '.$b['adults'].' adults, '.$b['children'].' children'];
 if($b['total']!==null)$details[]='Booking estimate: AED '.number_format((float)$b['total'],2);
 $next=$status==='confirmed'?'Our team will share any pickup, meeting point and experience details with you. Please keep your booking reference handy.':'If you have questions or would like to arrange another date or experience, reply to this email or contact our team.';
 $text='Hi '.$b['customerName'].",\n\n".$intro."\n\n".implode("\n",$details)."\n\n".$next."\n\n".$s['name']."\nPhone / WhatsApp: ".$s['phone']."\nEmail: ".BOOKING_EMAIL."\n".SITE_ORIGIN;
 $sent=false;
 try{if(filter_var($b['customerEmail'],FILTER_VALIDATE_EMAIL))$sent=notify('Booking '.$status.' - '.$b['reference'],$text,BOOKING_EMAIL,$b['customerEmail']);}catch(Throwable $error){error_log('Booking status email could not be accepted by the mail server.');}
 db()->exec('BEGIN IMMEDIATE');
 try{
  $raw=query('SELECT value FROM cms WHERE key=?',[$key])->fetchColumn();
  if($raw){$latest=json_decode($raw,true,512,JSON_THROW_ON_ERROR);foreach($latest['statusNotifications'] as &$notification)if($notification['id']===$id){$notification['delivery']=$sent?'sent':'failed';$notification['processedAt']=gmdate('c');}unset($notification);save($key,$latest);}
  db()->exec('COMMIT');
 }catch(Throwable $error){db()->exec('ROLLBACK');throw $error;}
 return ['ok'=>true,'changed'=>$changed,'customerNotification'=>$sent?'sent':'failed'];
}

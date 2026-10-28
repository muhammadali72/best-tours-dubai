<?php
function sendBusinessMail(string $subject,string $body,string $reply,string $to):bool{
 try{
  $config=json_decode(file_get_contents(__DIR__.'/storage/mail-config.json'),true,512,JSON_THROW_ON_ERROR);
  if(!filter_var($to,FILTER_VALIDATE_EMAIL))return false;
  require_once __DIR__.'/phpmailer/Exception.php';
  require_once __DIR__.'/phpmailer/PHPMailer.php';
  require_once __DIR__.'/phpmailer/SMTP.php';
  $mail=new \PHPMailer\PHPMailer\PHPMailer(true);
  $mail->isSMTP();$mail->Host=$config['host'];$mail->Port=(int)$config['port'];
  $mail->SMTPAuth=true;$mail->Username=$config['username'];$mail->Password=$config['password'];
  $mail->SMTPSecure=\PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
  $mail->Timeout=20;$mail->Timelimit=30;$mail->SMTPDebug=0;
  $mail->CharSet='UTF-8';$mail->Encoding='base64';
  $mail->setFrom($config['username'],'Best Tours Dubai');$mail->addAddress($to);
  if(filter_var($reply,FILTER_VALIDATE_EMAIL))$mail->addReplyTo($reply);
  $mail->Subject=$subject;$mail->Body=$body;
  return $mail->send();
 }catch(Throwable $error){error_log('Best Tours Dubai authenticated SMTP notification failed.');return false;}
}

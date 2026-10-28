<?php
declare(strict_types=1);
function initializePasswordAccounts(PDO $db):void{
 $db->exec('CREATE TABLE IF NOT EXISTS admin_accounts(email TEXT PRIMARY KEY,password_hash TEXT NOT NULL,version INTEGER NOT NULL DEFAULT 1,must_change INTEGER NOT NULL DEFAULT 1)');
 $seed=is_file(__DIR__.'/initial-admin-accounts.json')?json_decode(file_get_contents(__DIR__.'/initial-admin-accounts.json'),true,512,JSON_THROW_ON_ERROR):[];
 $insert=$db->prepare('INSERT OR IGNORE INTO admin_accounts(email,password_hash) VALUES(?,?)');
 foreach($seed as $email=>$hash)$insert->execute([$email,$hash]);
 $done=$db->query("SELECT 1 FROM cms WHERE key='setup:password-auth-v1'")->fetchColumn();
 if(!$done){$db->exec('DELETE FROM login_tokens');$db->exec("INSERT INTO cms VALUES('setup:password-auth-v1','true',datetime('now'))");}
}
function passwordSessionValid():bool{
 sessionStart();$email=$_SESSION['email']??'';
 if(!in_array($email,ADMIN_EMAILS,true)||($_SESSION['expires']??0)<=time())return false;
 $account=query('SELECT version,must_change FROM admin_accounts WHERE email=?',[$email])->fetch(PDO::FETCH_ASSOC);
 return $account&&(int)($_SESSION['authVersion']??0)===(int)$account['version']&&(bool)($_SESSION['mustChangePassword']??true)===(bool)$account['must_change'];
}
function authPageStart(string $title):void{
 header('Cache-Control: no-store');header('X-Robots-Tag: noindex');header('Content-Type: text/html;charset=utf-8');
 echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.e($title).' · Best Tours Dubai</title><link rel="icon" href="/favicon.svg"><link rel="stylesheet" href="/redesign.css"><style>.auth-card{width:min(480px,calc(100% - 32px));margin:8vh auto;padding:30px;background:white;border:1px solid #d9e2ed;border-radius:18px}.auth-card h1{font-size:30px;margin:30px 0 10px}.auth-card label{display:block;font-weight:600;margin-top:20px}.auth-card input{display:block;width:100%;min-height:48px;padding:12px;margin-top:8px;border:1px solid #b9c9dc;border-radius:8px;font:inherit}.auth-card button{margin-top:22px}.auth-card .password-wrap{position:relative}.auth-card .password-wrap input{padding-right:72px}.auth-card .password-toggle{position:absolute;right:4px;top:4px;min-height:40px;padding:0 10px;background:none;border:0;color:#123158;margin:0;font:inherit;font-size:14px;cursor:pointer}.auth-card .auth-message{padding:12px;background:#fff3ee;color:#812a17;border-radius:8px}.auth-card .auth-links{margin-top:22px;display:flex;gap:20px}.auth-card .brand{max-width:270px}</style></head><body><main class="auth-card"><a class="brand" href="/"><span class="logo-frame"><img src="/optimized/best-tours-dubai-logo-480.webp" alt="Best Tours Dubai"></span></a><h1>'.e($title).'</h1>';
}
function passwordField(string $name,string $label,string $autocomplete):void{
 echo '<label for="'.e($name).'">'.e($label).'</label><div class="password-wrap"><input required type="password" id="'.e($name).'" name="'.e($name).'" autocomplete="'.e($autocomplete).'" maxlength="72"><button class="password-toggle" type="button" aria-controls="'.e($name).'" aria-label="Show '.e(strtolower($label)).'" data-password="'.e($name).'">Show</button></div>';
}
function authPageEnd():never{
 echo '</main><script src="/auth-ui.js?v=password-1" defer></script></body></html>';exit;
}
function loginPage($message=''):never{
 authPageStart('Dashboard sign in');echo '<p>Sign in with your administrator email and password.</p>';
 if($message)echo '<p class="auth-message" role="alert">'.e($message).'</p>';
 echo '<form method="post" action="/auth/login"><label for="email">Email address</label><input required id="email" name="email" type="email" autocomplete="username" maxlength="254">';
 passwordField('password','Password','current-password');echo '<button class="button" type="submit">Sign in</button></form>';authPageEnd();
}
function passwordPage(string $message=''):never{
 if(!passwordSessionValid())loginPage('Please sign in before changing your password.');
 $_SESSION['passwordCsrf']??=bin2hex(random_bytes(32));authPageStart('Choose your password');
 echo '<p>'.(($_SESSION['mustChangePassword']??true)?'Replace your temporary password to open the dashboard.':'Update the password for your administrator account.').'</p>';
 if($message)echo '<p class="auth-message" role="alert">'.e($message).'</p>';
 echo '<form method="post" action="/auth/password"><input type="hidden" name="csrf" value="'.e($_SESSION['passwordCsrf']).'">';
 passwordField('currentPassword','Current password','current-password');passwordField('newPassword','New password','new-password');
 echo '<p id="password-help">Use at least 12 characters.</p>';
 passwordField('confirmPassword','Confirm new password','new-password');echo '<button class="button" type="submit">Save password</button></form><div class="auth-links">';
 if(!($_SESSION['mustChangePassword']??true))echo '<a href="/dashboard">Dashboard</a>';
 echo '<a href="/signout-with-chatgpt">Sign out</a></div>';authPageEnd();
}
function authRoute($path):never{
 if($path==='/auth/login'){
  if(($_SERVER['REQUEST_METHOD']??'GET')!=='POST')loginPage();requireOrigin();limitRequests('password-login',10,900);
  $email=strtolower(trim((string)($_POST['email']??'')));$password=(string)($_POST['password']??'');
  $account=query('SELECT * FROM admin_accounts WHERE email=?',[$email])->fetch(PDO::FETCH_ASSOC);
  $seed=is_file(__DIR__.'/initial-admin-accounts.json')?json_decode(file_get_contents(__DIR__.'/initial-admin-accounts.json'),true):[];
  $valid=password_verify($password,$account['password_hash']??($seed[ADMIN_EMAIL]??''));
  if(!$account||!in_array($email,ADMIN_EMAILS,true)||strlen($password)>72||!$valid)loginPage('The email or password is incorrect.');
  sessionStart();session_regenerate_id(true);$_SESSION=['email'=>$email,'expires'=>time()+86400,'authVersion'=>(int)$account['version'],'mustChangePassword'=>(bool)$account['must_change']];
  header('Location: '.($account['must_change']?'/auth/password':'/dashboard'),true,303);exit;
 }
 if($path==='/auth/password'){
  if(!passwordSessionValid())loginPage('Please sign in before changing your password.');
  if(($_SERVER['REQUEST_METHOD']??'GET')==='POST'){
   requireOrigin();limitRequests('password-change',10,900);
   if(!isset($_SESSION['passwordCsrf'])||!hash_equals($_SESSION['passwordCsrf'],(string)($_POST['csrf']??'')))passwordPage('Please refresh this page and try again.');
   $account=query('SELECT * FROM admin_accounts WHERE email=?',[$_SESSION['email']])->fetch(PDO::FETCH_ASSOC);
   $current=(string)($_POST['currentPassword']??'');$new=(string)($_POST['newPassword']??'');$confirm=(string)($_POST['confirmPassword']??'');
   if(strlen($current)>72||!password_verify($current,$account['password_hash']))passwordPage('Your current password is incorrect.');
   if(preg_match_all('/./us',$new)<12||strlen($new)>72||str_contains($new,"\0"))passwordPage('Use at least 12 characters. If your password is very long, choose a shorter one.');
   if($new!==$confirm)passwordPage('The new passwords do not match.');
   if(password_verify($new,$account['password_hash']))passwordPage('Choose a new password that differs from your current password.');
   query('UPDATE admin_accounts SET password_hash=?,version=version+1,must_change=0 WHERE email=?',[password_hash($new,PASSWORD_BCRYPT,['cost'=>12]),$_SESSION['email']]);
   $_SESSION['authVersion']=(int)$account['version']+1;$_SESSION['mustChangePassword']=false;unset($_SESSION['passwordCsrf']);session_regenerate_id(true);
   header('Location: /dashboard',true,303);exit;
  }
  passwordPage();
 }
 if(str_starts_with($path,'/signout')){sessionStart();$_SESSION=[];session_destroy();setcookie('btd_session','',['expires'=>time()-3600,'path'=>'/','secure'=>true,'httponly'=>true,'samesite'=>'Lax']);header('Location: /dashboard',true,303);exit;}
 if($path==='/auth/request'||$path==='/auth/verify'){http_response_code(410);loginPage('Email sign-in links have been disabled. Please use your email and password.');}
 loginPage();
}

<?php
function jget($v,$k){if($k==='length')return is_array($v)?count($v):mb_strlen((string)$v);return is_array($v)?($v[$k]??null):(is_object($v)?($v->$k??null):null);}
function jstr($v){return $v===null?'':(is_bool($v)?($v?'true':'false'):(string)$v);}
function jtruth($v){return is_array($v)||is_object($v)||($v!==null&&$v!==false&&$v!==''&&$v!==0&&$v!==0.0);}
function jadd($a,$b){return is_string($a)||is_string($b)?jstr($a).jstr($b):$a+$b;}
function jentries($a){$r=[];foreach($a as $k=>$v)$r[]=[$k,$v];return $r;}
function jjson($v){return json_encode($v,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);}
function jparse($s){return strtotime((string)$s)*1000;}
function jnumber($v){return is_numeric($v)?0+$v:0;}
function jisnan($s){return $s===false||$s===null;}
function jregex($s,$flags=''){return '~'.str_replace('~','\\~',$s).'~'.str_replace('g','',$flags);}
function jmethod($v,$method,...$a){
switch($method){
case 'map':return array_map($a[0],$v,array_keys($v));
case 'filter':return array_values(array_filter($v,$a[0]));
case 'find':foreach($v as $x)if($a[0]($x))return $x;return null;
case 'join':return implode($a[0]??',',$v);
case 'slice':return is_array($v)?array_slice($v,$a[0],isset($a[1])?$a[1]-$a[0]:null):mb_substr((string)$v,$a[0],isset($a[1])?$a[1]-$a[0]:null);
case 'split':return explode($a[0],(string)$v);
case 'pop':return $v?end($v):null;
case 'includes':return is_array($v)?in_array($a[0],$v,true):str_contains((string)$v,(string)$a[0]);
case 'startsWith':return str_starts_with((string)$v,$a[0]);
case 'endsWith':return str_ends_with((string)$v,$a[0]);
case 'toLowerCase':return strtolower((string)$v);
case 'padStart':return str_pad((string)$v,$a[0],$a[1]??' ',STR_PAD_LEFT);
case 'trim':return trim((string)$v);
case 'replaceAll':return str_replace($a[0],$a[1],(string)$v);
case 'replace':if(is_string($a[0])&&in_array(substr($a[0],0,1),['/','~'],true)){$rx=preg_replace('~g([imu]*)$~','$1',$a[0]);return preg_replace($rx,$a[1],(string)$v,str_contains(substr($a[0],strrpos($a[0],'/')+1),'g')?-1:1);}return preg_replace('~'.preg_quote($a[0],'~').'~',$a[1],(string)$v,1);
case 'match':$rx=preg_replace('~g([imu]*)$~','$1',$a[0]);return preg_match($rx,(string)$v,$m)?$m:null;
case 'test':return (bool)preg_match(preg_replace('~g([imu]*)$~','$1',$v),(string)$a[0]);
case 'toLocaleString':return rtrim(rtrim(number_format((float)$v,2,'.',','),'0'),'.');
case 'getFullYear':return (int)date('Y');
case 'toLocaleDateString':return $v->date->format(($a[0]??'')==='en-CA'?'Y-m-d':'j F Y');
default:throw new RuntimeException('Unsupported template method '.$method);
}}
class JDate{public $date;function __construct($s='now'){$this->date=new DateTime((string)$s,new DateTimeZone('Asia/Dubai'));}}

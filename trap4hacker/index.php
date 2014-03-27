<?php
header('Content-Type: text/html; charset=utf8');
//header('HTTP/1.0 404 Not Found');
setlocale(LC_ALL, "ru_RU.UTF-8");
session_start();
if(!isset($_SESSION['attempt_num'])) $_SESSION['attempt_num']=1;
else $_SESSION['attempt_num']++;

define(DEBUG, false);

// Конфиг
$path	=	'';
$filename = 'log.txt';

// Инициализация
$startTime=microtime(true);
if(DEBUG) print "<pre>\n";
// Открытие текстовых файлов
$fhBuf = fopen($path.$filename, "a");
$locked = flock($fhBuf, LOCK_EX | LOCK_NB);
if(!$locked) {
    echo 'Не удалось получить блокировку';
    exit(-1);
}

// Функции
function logWrite($str, $fh)
{
	if(DEBUG) print "$str\n";
	fwrite($fh, "$str\n");
}


$output="-------------------------------------------\n";
$output.="REMOTE_ADDR		".$_SERVER['REMOTE_ADDR']."\n";
$output.="-------------------------------------------\n";
$output.='Попытка '.$_SESSION['attempt_num']."		от ".date('j-m-Y H:i:s')."\n";
if($_SERVER['HTTP_X_REAL_IP'] != $_SERVER['REMOTE_ADDR'])
$output.="HTTP_X_REAL_IP	".$_SERVER['HTTP_X_REAL_IP']."\n";

$domain	= gethostbyaddr($_SERVER['REMOTE_ADDR']);
if($domain != $_SERVER['REMOTE_ADDR'])
$output.="HOST DNS NAME		".$domain."\n";

$output.="HTTP_USER_AGENT	".$_SERVER['HTTP_USER_AGENT']."\n";
$output.="REQUEST_URI		".$_SERVER['REQUEST_URI']."\n";
if(!empty($_SERVER['QUERY_STRING']))
$output.="QUERY_STRING		".$_SERVER['QUERY_STRING']."\n";
$output.="REQUEST_TIME		".$_SERVER['REQUEST_TIME']."\n";
logWrite($output,$fhBuf);

//logWrite(microtime(true) - $startTime, $fhBuf);
// Закрываем файл
fflush($fhBuf) or die($php_errormsg);
flock($fhBuf,LOCK_UN) or die($php_errormsg);
fclose($fhBuf) or die($php_errormsg);
unset($fhBuf);
// Файл освобождён

if(DEBUG) print "</pre>";
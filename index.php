<?php
header("Access-Control-Allow-Origin: *");
$_POST = json_decode(file_get_contents('php://input'), true);
$config = include('config.php');

require_once('classes/Db.php');
require_once('classes/Basic.php');
require_once('classes/RouterLite.php');
require_once('classes/controllers/Admin.php');
require_once('classes/controllers/API.php');
require_once('classes/controllers/Bot.php');
require_once('classes/controllers/Statistics.php');
require_once('classes/controllers/Crontab.php');

RouterLite::addRoute('', 'Admin/main');
RouterLite::addRoute('/results', 'Admin/results');
RouterLite::addRoute('/bot', 'Bot/main');
RouterLite::addRoute('/crontab', 'Crontab/main');
RouterLite::addRoute('/test', 'API/test');
RouterLite::addRoute('/getData', 'API/getData');
RouterLite::addRoute('/sendResult', 'API/sendResult');
RouterLite::addRoute('/sendAvatar', 'API/sendAvatar');
RouterLite::addRoute('/moreProfessions', 'API/moreProfessions');
RouterLite::addRoute('/referral', 'API/referral');
RouterLite::addRoute('/downloadTrack', 'API/downloadTrack');
RouterLite::addRoute('/notFound', 'API/notFound');
RouterLite::dispatch();
?>
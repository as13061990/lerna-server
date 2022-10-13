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

RouterLite::addRoute('', 'Admin/main');
RouterLite::addRoute('/bot', 'Bot/main');
RouterLite::addRoute('/test', 'API/test');
RouterLite::addRoute('/getData', 'API/getData');
RouterLite::addRoute('/sendAvatar', 'API/sendAvatar');
RouterLite::addRoute('/notFound', 'API/notFound');
RouterLite::dispatch();

?>
<?php

use Model\UserAuth;

session_start();
define('ROOT', str_replace('public/index.php', '', $_SERVER['SCRIPT_FILENAME']));

require_once ROOT . 'config.dist.php';
require_once ROOT . 'vendor/autoload.php';

$bool = PROD_ENV ? 0 : 1;
$econst = PROD_ENV ? 0 : E_ALL;
ini_set('display_errors', $bool);
ini_set('display_startup_errors', $bool);
error_reporting($econst ^ E_DEPRECATED);
date_default_timezone_set('UTC');

define('WEBROOT', str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));
define('SELF_API_URL', 'http' . (PROD_ENV ? 's' : '') . '://' . PROD_HOST . WEBROOT . 'api/');
define('POST', isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST');
define('ENTITIES', ROOT.'entities/');
define('SYSTEM', ROOT.'system/');
define('APP', ROOT.'app/');
define('MODELS', APP.'models/');
define('VIEWS', APP.'views/');
define('CONTROLLERS', APP.'controllers/');
define('ASSETS', WEBROOT.'assets/');
define('CSS', ASSETS.'css/');
define('JS', ASSETS.'js/');
define('FONTS', ASSETS.'fonts/');
define('IMG', ASSETS.'img/');
define('VENDORS', ASSETS.'vendors/');

spl_autoload_register(function ($classname) {
    $ext = '.php';
    $split = explode('\\', $classname);
    $namespace = '';
    if (count($split) > 1) {
        $namespace = $split[0];
        $classname = $split[1];
    }

    $path = ROOT;
    if ($namespace == 'Model' && file_exists(ROOT.'app/models/'.$classname.$ext)) {
        $path .= 'app/models/';
    } elseif ($namespace == 'Entity' && file_exists(ROOT.'entities/'.$classname.$ext)) {
        $path .= 'entities/';
    } elseif (file_exists(ROOT.'system/'.$classname.$ext)) {
        $path .= 'system/';
    }

    if ($path != ROOT) {
        require_once $path . $classname . $ext;
    }
});

if (Request::get()->getArg(0) == 'api' && empty($_POST)) {
    $php_input = file_get_contents('php://input');
    $json_data = json_decode($php_input, true);
    parse_str($php_input, $http_data);
    if ($json_data !== NULL) {
        $_POST = $json_data;
    } else {
        $_POST = $http_data;
    }
}

header("Access-Control-Allow-Origin: *");
UserAuth::flushOutdatedAuths();

require_once Router::get()->getPathToRequire();

if (Request::get()->getArg(0) == 'api') {
    Logger::logInfo($_GET['arg'] . ': ' . json_encode(Data::get()->getData()));
}
<?php

define("maxImgSize",'5');
define("site",'http://localhost:8888/25.9_Gallery');

define("ROOT", dirname(__DIR__, 1) . DIRECTORY_SEPARATOR );
define("APP", ROOT . 'application' . DIRECTORY_SEPARATOR);
define("CONTROLLER", ROOT . 'application' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR);
define("CORE", ROOT . 'application' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR);
define("DATA", ROOT . 'application' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR);
define("MODEL", ROOT . 'application' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR);
define("VIEW", ROOT . 'application' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR);
define("PAGES", ROOT . 'application' . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR);
define("LAYOUT", VIEW . 'layout' . DIRECTORY_SEPARATOR);
define("VENDOR", ROOT . 'vendor' . DIRECTORY_SEPARATOR);


require_once 'core/model.php'; 
require_once 'core/view.php'; 
require_once 'core/controller.php'; 
require_once 'core/route.php'; 
require_once 'core/dbConnect.php'; 
require_once 'core/sql.php'; 
?>
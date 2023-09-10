<?php
require_once dirname(__DIR__, 2) . "/vendor/autoload.php" ;

use Monolog\Logger;
use Monolog\Level;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\HtmlFormatter;

// Создаем логгер 
$log = new Logger('mylogger');
$log->pushHandler(new StreamHandler(dirname(__DIR__, 2) . "/logs/mylog.log", Level::Debug));

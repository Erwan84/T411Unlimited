<?php 

define('ROOT', dirname(__FILE__) . '/../');
define('ROOT_CORE', ROOT . 'core/');
define('ROOT_CLASS', ROOT_CORE . 'class/');

require_once(ROOT_CLASS . 'Config.class.php');
require_once(ROOT_CORE . 'functions.php');

while (true)
{
    checkT411TrackerStatus();
    sleep(60 * 5); // 5 minutes
}
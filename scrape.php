<?php

define('ROOT', dirname(__FILE__) . '/');
define('ROOT_CORE', ROOT . 'core/');
define('ROOT_CLASS', ROOT_CORE . 'class/');

require_once(ROOT_CLASS . 'Config.class.php');
require_once(ROOT_CLASS . 'T411API.class.php');
require_once(ROOT_CLASS . 'MyTorrent.class.php');
require_once(ROOT_CORE . 'functions.php');

error_reporting(0);

$stats = getStats();

$old_error_handler = set_error_handler("myErrorHandler");

$token = Config::$t411Tracker['token'];
$url = "http://tracker.t411.me:56969/" . $token . "/scrape.php?info_hash=" . urlencode($_GET['info_hash']);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($curl, CURLOPT_PROXY, "127.0.0.1:9150");
// curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    "UserAgent: uTorrent/342(109416199)(36615)",
    "Connection: close",
    "Accept-Encoding: gzip"
));
$content = curl_exec($curl);
curl_close($curl);

echo $content;
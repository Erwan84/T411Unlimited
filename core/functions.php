<?php 

require_once(ROOT_CLASS . "Lang.class.php");
require_once(ROOT_CLASS . "Config.class.php");

function uploadTorrent(&$file)
{
    if (!preg_match("#\.torrent$#", $file['name']))
    {
        $error = Lang::$uploadTorrent['upload_wrong_extension']['fr'];
    }
    if ($file['type'] != "application/x-bittorrent")
    {
        $error = Lang::$uploadTorrent['upload_wrong_mime']['fr'];
    }
    if($file['size'] > 10000000)
    {
        $error = "Le fichier torrent ne doit pas dépasser les 10Mo";
    }
    if ($file['error'] > 0)
    {
        $error = "Erreur durant l'upload";
    }
    if (strlen($file['name']) > 255)
    {
        $error = "Le nom du fichier est un peu long non ?";
    }

    if (isset($error))
    {
        @unlink($file["tmp_name"]);
        throw new MyException($error);
    }
    
    $path = createAndWriteToRandomFile(file_get_contents($file["tmp_name"]));
    @unlink($file["tmp_name"]);
    
    return $path;
}

function debug($debug)
{
    echo "<pre>";
    print_r($debug);
    echo "</pre>";
}

function jsonMessage($message, $code = 0)
{
    return json_encode(array(
        "message" => $message,
        "code" => $code
    ));
}

function createAndWriteToRandomFile($content, $folder = "./tmp/")
{
    $randomFileName = md5(rand(111111,999999) . uniqid() . rand(111111,999999));
    
    $file = fopen($folder . $randomFileName, "w+");
    fwrite($file, $content);
    fclose($file);
    
    return $folder . $randomFileName;
}

function logToFile($message)
{
    $file = @fopen(time() . "." . rand(100,999), "w+");
    @fwrite($file, $message);
    @fclose($file);
}

function myErrorHandler($errno, $errstr, $errfile, $errline) {
    header("HTTP/1.1 500 Internal Server Error");
    $date = date("Y-m-d H:i:s", time());
    $message = "";
    $message.= "-------------------------------------------------------\n";
    $message.= "DATE : " . $date . "\n";
    $message.= "ERRNO : " . $errno . "\n";
    $message.= "ERRSTR : " . $errstr . "\n";
    $message.= "ERRFILE : " . $errfile . "\n";
    $message.= "ERRLINE : " . $errline . "\n";
    $message.= "-------------------------------------------------------";
    logToFile($message);
    die('Erreur serveur');
}

function checkT411TrackerStatusFromCache()
{
    $content = 0;
    $content  = (int) file_get_contents(ROOT . "cache/t411_tracker_status");
    
    if ($content === 1)
        return true;
        
    return false;
    
}

function checkT411TrackerStatus($timeout = 5)
{
    $ip = Config::$t411Tracker["ip"];
    $port = Config::$t411Tracker["port"];
    $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    socket_set_option($socket,SOL_SOCKET, SO_RCVTIMEO, array("sec"=>$timeout, "usec"=>0));
    socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => $timeout, 'usec' => 0)); 
    $status = @socket_connect($socket, Config::$t411Tracker["ip"], Config::$t411Tracker["port"]);
    $status = ($status == true) ? 1 : 0;
    if ($status)
        @socket_close($socket);
    @file_put_contents(ROOT . "cache/t411_tracker_status", $status);
    return $status;
}

function getStats()
{
    $filePath = ROOT . "cache/users";
    $content = file_get_contents($filePath);
    $myIp = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
    $lines = [];
    $lineMyIp = -1;
    $i = 0;
    
    if (!empty($content))
    {
        $lines = explode("\r\n", $content);
        foreach ($lines as $line)
        {
            $ip = explode(" ", $line)[0];
            $time = (int) explode(" ", $line)[1];
            
            $ips[] = $ip;
            
            if (time() > $time + (60 * 20))
            {
                unset($lines[$i]);
            } else
            {
                if ($ip == $myIp)
                {
                    $lineMyIp = $i;
                }
            }
        }
        
        $i++;
    }
    
    if ($lineMyIp == -1)
    {
       $lines[] = $myIp . " " . time(); 
    } else
    {
        $lines[$lineMyIp] = $myIp . " " . time(); 
    }

    $content = implode("\r\n", $lines);
    file_put_contents($filePath, $content);
    return count($lines);
}
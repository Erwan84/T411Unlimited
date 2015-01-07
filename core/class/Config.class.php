<?php 
abstract class Config
{
    public static $debug = false;
    public static $myTrackerURI = "http://t411unlimited.tk/";
    
    public static $t411Account = array(
        "username" => "",
        "password" => ""
    );
    
    public static $t411Tracker = array(
        "url" => "http://tracker.t411.me",
        "ip" => "46.246.117.194",
        "port" => 56969,
        "token" => "",
        "userAgent" => "uTorrent/342(109416199)(36615)",
        "proxy" => array(
            "activate" => false,
            "host" => "127.0.0.1",
            "port" => 9050
        )
    );
    
    public static $t411API = array(
        "authUrl" => "https://api.t411.me/auth",
        "userAgent" => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.101 Safari/537.36",
        "proxy" => array(
            "activate" => false,
            "host" => "127.0.0.1",
            "port" => 9050
        )
    );
}
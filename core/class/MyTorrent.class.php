<?php 
require_once(ROOT_CLASS . 'Torrent.class.php');

class MyTorrent extends Torrent
{
    public function __construct($data = null, $meta = array(), $piece_length = 256)
    {
        parent::__construct($data, $meta, $piece_length);
        $this->announce = "http://t411unlimited.tk/announce.php";
    }
}
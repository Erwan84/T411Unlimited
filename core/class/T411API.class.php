<?php 
require_once(ROOT_CLASS . 'Config.class.php');
require_once(ROOT_CLASS . 'MyException.class.php');
require_once(ROOT_CLASS . 'Lang.class.php');

class T411API
{
    private $_username,
            $_password;
            
    public $token;

    const WRONG_ID = 1;
    const T411SERVER_ERROR = 2;
    const ACCESS_GRANTED = 3;
    const ACCOUNT_BANNED = 4;
    const ERROR_UNKNWOWN = 5;
    const TORRENT_NOT_FOUND = 6;

    public function __construct($username, $password)
    {
        $this->_username = $username;
        $this->_password = $password;
    }
    
    public function connectionRequest()
    {
        if ($this->_username == null || $this->_password == null)
        {
            return T411API::WRONG_ID;
        }
        
        $postfields = array(
            "username" => $this->_username,
            "password" => $this->_password
        );
    
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, Config::$t411API['authUrl']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, Config::$t411API['userAgent']);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
        $response = curl_exec($curl);
        $infos = curl_getinfo($curl);
        curl_close($curl);
        
        return $this->handleConnectionResponse($infos, $response);
    }
    
    private function handleConnectionResponse($infos, $response)
    {

        if ($infos['http_code'] != 200)
            throw new MyException(Lang::$lang['api_t411server_error']['fr'], self::T411SERVER_ERROR);

        $content_parsed = json_decode($response, true);
        
        if (array_key_exists('token', $content_parsed))
        {
            $this->token = $content_parsed["token"];
            return self::ACCESS_GRANTED;
        }
        if (array_key_exists("error", $content_parsed))
        {
            if (in_array($content_parsed["code"], array(103, 104, 105)))
                throw new MyException(Lang::$lang['api_account_banned']['fr'], self::ACCOUNT_BANNED);
            else if(in_array($content_parsed["code"], array(101, 107)))
                throw new MyException(Lang::$lang['api_wrong_id']['fr'], self::WRONG_ID);
        }
        
        print_r($content_parsed);
        throw new MyException(Lang::$lang['api_error_unknown']['fr'], self::ERROR_UNKNWOWN);
    }
    
    private function parseId($id)
    {
        
        if (preg_match("#https?://(www\.)?t411\.me/t/([0-9]+)/?#", $id, $matches))
        {
            $id = $matches[2];
        }
        if (preg_match("#https?://(www\.)?t411\.me/torrents/download/\?id=([0-9]+)/?#", $id, $matches))
        {
            $id = $matches[2];
        }
        
        if (!preg_match("#[0-9]+#", $id))
        {
            throw new MyException("Id incorrect");
        }
        
        return $id;
    }

    public function downloadTorrentRequest($id)
    {
        $id = $this->parseId($id);
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://api.t411.me/torrents/download/" . $id);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, Config::$t411API['userAgent']);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: '. $this->token));
        $response = curl_exec($curl);
        $infos = curl_getinfo($curl);
        curl_close($curl);
        
        return $this->handleDownloadTorrentResponse($id, $infos, $response);
    }
    
    private function handleDownloadTorrentResponse($id, $infos, $response)
    {
        if ($infos['http_code'] != 200)
            throw new MyException(Lang::$lang['api_t411server_error']['fr'], self::T411SERVER_ERROR);
        
        $content_parsed = json_decode($response, true);
        if (!empty($content_parsed) && is_array($content_parsed))
        {
            if (array_key_exists("error", $content_parsed))
            {
                if ($content_parsed["code"] == 1301)
                    throw new MyException(Lang::$api['api_torrent_not_found']['fr'], self::TORRENT_NOT_FOUND);
            }
        }

        return createAndWriteToRandomFile($response);
    }
}

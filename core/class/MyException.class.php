<?php 
class MyException extends Exception 
{
    protected $code;
    protected $message;
    protected $type;
    
    const PHP_ERROR = 1;
    const APP_ERROR = 2;
        
    public function __construct($message=NULL, $code=0, $type = self::APP_ERROR) 
    {
        $this->message = $message;
        $this->code = $code;
        $this->type = $type;
        
        parent::__construct($message, $code);
    }
    
    public function message()
    {
        if ($this->type == self::PHP_ERROR)
            $this->phpErrorMessage();
        else
            $this->appErrorMessage();
    }
    
    public function appErrorMessage()
    {
        return $this->message;
    }
    
    public static function phpErrorMessage($errno, $errstr, $errfile, $errline)
    {
        $error = "";
        $error.= "<fieldset style='display:inline-block;'><legend><strong>Erreur fatale</strong></legend>";
        $error.= "<strong>CODE</strong> : " . $errno . "<br />";
        $error.= "<strong>FICHIER</strong> : " . $errfile . "<br />";
        $error.= "<strong>LIGNE</strong> : " . $errline . "<br />";
        $error.= "<strong>MESSAGE</strong> : " . $errstr . "<br />";
        $error.= "<p><em>L'administrateur a été prévenu et corrigera cette erreur dans les plus brefs délais.</em></p>";
        $error.= "</fieldset>";
    }
}
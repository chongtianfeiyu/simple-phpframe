<?php

class Test{
    private $encoding   = 'UTF-8';
    
    function __construct() {
       $this->encoding  = 'DING';
    }
	

    function GetEncoding(){
        return $this->encoding;
    }

}

class EditClass{
    function GetEncoding(){
        return 'DASDAFA';
    }

    static function SGetEncoding(){
        return 'static';
    }
}
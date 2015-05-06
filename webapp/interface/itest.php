<?php

class iTest{
    private $encoding   = 'UTF-8';
    
    function __construct() {
       $this->encoding  = 'DING2';
    }
	

    function GetEncoding(){
        return $this->encoding;
    }

}

class iEditClass{
    function GetEncoding(){
        return 'DASDAFA';
    }

    static function SGetEncoding(){
        return 'static';
    }
}
<?php 

class Hulu {

    function __construct(){
        $ch = curl_init("http://www.hulu.com/browse/alphabetical/feature_films");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $this->movies = curl_exec($ch);
    }
    
    function available($title){
        return preg_match("/<a.+?>$title<\/a>/", $this->movies);
    }

}

?>
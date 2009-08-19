<?php

require("OAuth.php");

class Netflix {

    protected $consumer;

    function __construct($key, $secret){
        $this->consumer = new OAuthConsumer($key, $secret);
    }

    function request($url, $method, $params = null){
        $sign = new OAuthSignatureMethod_HMAC_SHA1();
        $request = OAuthRequest::from_consumer_and_token($this->consumer, null, $method, $url, $params);
        $request->sign_request($sign, $this->consumer, null);
        $ch = curl_init($request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        return curl_exec($ch);
    }
    

}

?>
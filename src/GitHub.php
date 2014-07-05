<?php

namespace duncan3dc\OAuth;

use duncan3dc\Helpers\Helper;
use duncan3dc\Helpers\Json;

class GitHub extends OAuth2 {


    public function __construct($options) {

        $options = Helper::getOptions($options,[
            "username"  =>  false,
            "client"    =>  "",
            "secret"    =>  "",
            "code"      =>  "",
        ]);

        parent::__construct([
            "type"          =>  "github",
            "username"      =>  $options["username"],
            "client"        =>  $options["client"],
            "secret"        =>  $options["secret"],
            "code"          =>  $options["code"],
            "authoriseUrl"  =>  "https://github.com/login/oauth/authorize?scope=repo",
            "redirectUrl"   =>  "http://developer.github.com/v3/",
            "accessUrl"     =>  "https://github.com/login/oauth/access_token",
        ]);

    }


    public function fetch($url,$data=false,$headers=false) {

        if(!is_array($headers)) {
            $headers = [];
        }
        $headers["Accept"] = "application/vnd.github.v3+json";
        $headers["User-Agent"] = $this->username;

        return parent::fetch($url,$data,$headers);

    }


    public function post($url,$data) {

        $headers = array(
            "Accept"        =>  "application/vnd.github.v3+json",
            "User-Agent"    =>  $this->username,
        );

        $url = Helper::url($url,[
            "access_token"  =>  $this->get("token"),
        ]);

        $json = Helper::curl([
            "url"       =>  $url,
            "headers"   =>  $headers,
        ],Json::encode($data));

        return Json::decode($json);

    }


}

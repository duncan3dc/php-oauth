<?php

namespace duncan3dc\OAuth;
use \duncan3dc\Helpers\Cache;
use \duncan3dc\Helpers\Helper;
use \duncan3dc\Helpers\Json;

class OAuth2 {

    public  $type;
    public  $username;
    public  $client;
    public  $secret;
    public  $code;

    public  $authoriseUrl;
    public  $redirectUrl;
    public  $accessUrl;


    public function __construct($options) {

        $options = Helper::getOptions($options,[
            "type"          =>  "",
            "username"      =>  "",
            "client"        =>  "",
            "secret"        =>  "",
            "code"          =>  "",
            "authoriseUrl"  =>  "",
            "redirectUrl"   =>  "",
            "accessUrl"     =>  "",
        ]);

        $this->type     =   $options["type"];
        $this->username =   $options["username"];
        $this->client   =   $options["client"];
        $this->secret   =   $options["secret"];
        $this->code     =   $options["code"];

        $this->authoriseUrl =   $options["authoriseUrl"];
        $this->redirectUrl  =   $options["redirectUrl"];
        $this->accessUrl    =   $options["accessUrl"];

    }


    public function authorise() {

        if($this->get("state") == OAuth::STATE_COMPLETE && !$this->get("token")) {
            $this->set("state",OAuth::STATE_INIT);
        }

        # If no action has been taken yet then request authorisation to the users account
        if($this->get("state") == OAuth::STATE_INIT) {
            $url = Helper::url($this->authoriseUrl,[
                "client_id"     =>  $this->client,
                "redirect_uri"  =>  $this->redirectUrl,
                "response_type" =>  "code",
            ]);
            $this->set([
                "state" =>  OAuth::STATE_ACCESS,
            ]);
            return $url;
        }

        # If access has been requested, then check that it was granted
        if($this->get("state") == OAuth::STATE_ACCESS) {
            $response = $system->curl($this->accessUrl,[
                "client_id"     =>  $this->client,
                "client_secret" =>  $this->secret,
                "redirect_uri"  =>  $this->redirectUrl,
                "grant_type"    =>  "authorization_code",
                "code"          =>  $this->code,
            ]);
            try {
                $data = $system->jsonDecode($response);
                $token = $data["access_token"];
            } catch(\Exception $e) {
                preg_match("/\baccess_token=([a-z0-9]+)\b/",$response,$matches);
                $token = $matches[1];
            }

            if(!$token) {
                $this->set("state",OAuth::STATE_INIT);
                throw new \Exception("Failed to parse the access token from the response (" . $response . ")");
            }

            $this->set([
                "state"     =>  OAuth::STATE_COMPLETE,
                "token"     =>  $token,
            ]);
        }

        return false;

    }


    public function get($key) {
        global $sql;

        if(Cache::check("data")) {
            $data = Cache::get("data");

        } else {
            $data = $sql->select("oauth",[
                "type"      =>  $this->type,
                "username"  =>  $this->username,
            ]);
            Cache::set("data",$data);

        }

        return $data[$key];

    }


    public function set($params,$value=false) {
        global $sql;

        if(!is_array($params)) {
            $params = [$params => $value];
        }

        $sql->insertOrUpdate("oauth",$params,[
            "type"      =>  $this->type,
            "username"  =>  $this->username,
        ]);

        Cache::clear("data");

        return true;

    }


    public function fetch($url,$data=false,$headers=false) {

        if(!is_array($data)) {
            $data = [];
        }
        $data["access_token"] = $this->get("token");

        $json = Helper::curl([
            "url"       =>  Helper::url($url,$data),
            "headers"   =>  $headers,
        ]);

        return Json::decode($json);

    }


}
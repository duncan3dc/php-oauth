<?php

namespace duncan3dc\OAuth;

class Sql {

    protected static $class;

    public static function useClass($class) {
        static::$class = $class;
    }

    public static function getInstance($server = false) {
        if(!static::$class) {
            throw new \Exception("No SQL class specified for the OAuth project");
        }
        return call_user_func(static::$class . "::getInstance", $server);
    }

}

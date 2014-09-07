<?php

namespace duncan3dc\OAuth;

class Sql extends \duncan3dc\SqlClass\Sql
{
    protected static $class;

    public static function useClass($class)
    {
        static::$class = $class;
    }

    public static function getInstance($server = null)
    {
        if (static::$class) {
            return call_user_func(static::$class . "::getInstance", $server);
        } else {
            return parent::getInstance($server);
        }
    }
}

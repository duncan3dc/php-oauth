<?php

namespace duncan3dc\OAuth;

use duncan3dc\SqlClass\Sql as SqlClass;

class Sql extends SqlClass
{
    protected static $class;
    protected static $sql;

    public static function useClass($class)
    {
        static::$class = $class;
    }

    public static function useInstance(SqlClass $sql)
    {
        static::$sql = $sql;
    }

    public static function getInstance($server = null)
    {
        if (static::$sql !== null) {
            return static::$sql;
        }

        if (static::$class) {
            return call_user_func(static::$class . "::getInstance", $server);
        } else {
            return parent::getInstance($server);
        }
    }
}

<?php namespace src;

class App
{
    protected static $app;
 
    public static function setup($app)
    {
        static::$app = $app;
    }

    public static function object()
    {
        return static::$app;
    }
}

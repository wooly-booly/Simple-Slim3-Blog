<?php namespace src\Middleware;

class Middleware
{
    protected $container;

    public function __construct($c)
    {
        $this->container = $c;
    }
}

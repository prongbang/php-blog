<?php

namespace App\Utils;

use Slim\Container;

class Utils
{
    var $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function __get($var)
    {
        return $this->container->{$var};
    }
}
<?php

namespace App\Controller;

use Slim\Container;

class Controller
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
<?php

namespace App\Model;

use Slim\Container;

class Model {

    protected $db;
    protected $core;
    protected $table_name; 
    
    var $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->connect();
    }

    public function __get($var)
    {
        return $this->container->{$var};
    }

    public function connect() {
        $this->core = Database::getInstance();
        $this->db = $this->core->db;
    }

    public function close() {
        $this->db->close();
    }

}
<?php

class Database {
    
    public $db;
    private static $instance;

    function __construct() {
        $setting = [

            // Set true to production
            'production' => false,

            // Database Dev
            'devdb' => [
                'host' => 'localhost',
                'user' => 'root',
                'pass' => 'buffer',
                'db' => 'buffer',
            ],

            // Database Production
            'proddb' => [
                'host' => 'localhost',
                'user' => 'root',
                'pass' => 'buffer',
                'db' => 'buffer',
            ],
        ];

        $production = $setting['production'];
        $key = $production ? 'proddb' : 'devdb';
        $config = $setting[$key];

        $this->db = new mysqli(
            $config['host'], 
            $config['user'], 
            $config['pass'], 
            $config['db']
        );
        // $this->db = new PDO("mysql:dbname={$config['db']};host={$config['host']}", $config['user'], $config['pass']);

        $this->db->query("SET NAMES utf8");
    }

    public static function getInstance() {
        if (!isset(self::$instance)) {
            $object = __CLASS__;
            self::$instance = new $object;
        }
        return self::$instance;
    }

}

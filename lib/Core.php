<?php

namespace lib;
use lib\Config;
use PDO;

class Core {
    public $dbh;
    private static $instance;

    /**
     * コンストラクタ
     * 設定ファイルはrootに配置されているconfig.php
     */
    private function __construct() {
        // building data source name from config
        $dsn = 'mysql:host=' . Config::read('db.host') .
            ';dbname='    . Config::read('db.basename') .
            ';port='      . Config::read('db.port') .
            ';connect_timeout=15';
        // getting DB user from config
        $user = Config::read('db.user');
        // getting DB password from config
        $password = Config::read('db.password');
        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . Config::read('db.encode'),
        );
        $this->dbh = new PDO($dsn, $user, $password, $options);
    }

    /**
     * シングルトンパターンで行こう
     * @return mixed
     */
    public static function getInstance() {
        if (!isset(self::$instance))
        {
            $object = __CLASS__;
            self::$instance = new $object;
        }
        return self::$instance;
    }
}
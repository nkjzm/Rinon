<?php

namespace lib;

/**
 * プロジェクト内で利用するグローバルな値を保持する
 * Class Config
 * @package lib
 */
class Config {
    static $confArray;
    public static function read($name) {
        return self::$confArray[$name];
    }
    public static function write($name, $value) {
        self::$confArray[$name] = $value;
    }
}
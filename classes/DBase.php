<?php
/**
 * Created by IntelliJ IDEA.
 * User: adi
 * Date: 10/17/13
 * Time: 1:57 PM
 * To change this template use File | Settings | File Templates.
 */

define('DB_DSN', 'mysql:host=localhost;dbname=mock_rating');
define('DB_USER', 'root');
define('DB_PASS', 'password');

class DBase {
    private static $objInstance;
    private function __construct() {}
    private function __clone() {}

    public static function getInstance() {
        if(!self::$objInstance){
            self::$objInstance = new PDO(DB_DSN, DB_USER, DB_PASS);
            self::$objInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$objInstance;
    }
    final public static function __callStatic($name, $arguments) {
        $objInstance = self::getInstance();
        return call_user_func_array(array($objInstance, $name), $arguments);
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: rk521
 * Date: 18/10/17
 * Time: 12:30 AM
 */
define("DATABASE", "teccheck");
define("HOSTNAME", "teccheck.c4grwswsltbr.us-east-2.rds.amazonaws.com");
define("USERNAME", "root");
define("PASSWORD", "06720Doctores!");

class DB
{
    private static $db = null;
    private static $pdo;

    final private function __construct()
    {
        try {
            self::getDB();
        } catch (PDOException $e) {
            return json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public static function init()
    {
        try {
            if (self::$db === null) {
                self::$db = new self();
            }

            return self::$db;
        } catch (PDOException $e) {
            return json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public function getDB()
    {
        try {
            if (self::$pdo == null) {
                self::$pdo = new PDO(
                    'mysql:dbname=' . DATABASE .
                    ';host=' . HOSTNAME . ";",
                    USERNAME, PASSWORD,
                    [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]
                );
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            return self::$pdo;
        } catch (Exception $e) {
            return json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    function _destructor()
    {
        self::$pdo = null;
    }
}
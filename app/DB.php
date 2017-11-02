<?php
/**
 * Created by PhpStorm.
 * User: rk521
 * Date: 18/10/17
 * Time: 12:30 AM
 */
define("DATABASE", "tc");
define("HOSTNAME", "localhost");
define("USERNAME", "root");
define("PASSWORD", "dr4g0n");

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
        if (self::$db === null) {
            self::$db = new self();
        }

        return self::$db;
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
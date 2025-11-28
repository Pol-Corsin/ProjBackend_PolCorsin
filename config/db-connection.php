<?php
class DB
{
    private static $conn;

    public static function connect()
    {
        if (!self::$conn) {
            self::$conn = new PDO("mysql:host=localhost;dbname=pt04_pol_corsinv2;charset=utf8mb4", "root", "");
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$conn;
    }
}

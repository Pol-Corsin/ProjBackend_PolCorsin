<?php
require_once __DIR__ . '/../../config/db-connection.php';

class User
{

    public static function exists($username)
    {
        $db = DB::connect();
        $stmt = $db->prepare("SELECT id FROM users WHERE username=?");
        $stmt->execute([$username]);
        return $stmt->fetch() ? true : false;
    }

    public static function create($username, $email, $password)
    {
        $db = DB::connect();
        $stmt = $db->prepare("INSERT INTO users (username,email,password) VALUES (?,?,?)");
        $stmt->execute([$username, $email, $password]);
    }

    public static function findByUsername($val)
    {
        $db = DB::connect();
        $stmt = $db->prepare("SELECT * FROM users WHERE username=?");
        $stmt->execute([$val]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findByEmail($email)
    {
        $db = DB::connect();
        $stmt = $db->prepare("SELECT * FROM users WHERE email=?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function update($username, $newusername) {
        $db = DB::connect();
        $stmt = $db->prepare("UPDATE users SET username=? WHERE username=?");
        $stmt->execute([$newusername, $username]);
        return true;
    }
}

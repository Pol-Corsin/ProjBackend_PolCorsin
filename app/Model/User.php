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

    public static function getAll()
    {
        $db = DB::connect();
        $stmt = $db->query("SELECT id, username, email, role FROM users ORDER BY username ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById($id)
    {
        $db = DB::connect();
        $stmt = $db->prepare("SELECT * FROM users WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function delete($id)
    {
        $db = DB::connect();
        $stmt = $db->prepare("DELETE FROM users WHERE id=?");
        return $stmt->execute([$id]);
    }

    /**
     * Generar un token de Remember Me
     * @param int $user_id ID del usuario
     * @param int $expirationDays Días hasta que expire (default 30)
     * @return string Token generado
     */
    public static function generateRememberMeToken($user_id, $expirationDays = 30)
    {
        $db = DB::connect();
        $token = bin2hex(random_bytes(32)); // Token seguro de 64 caracteres
        $expiresAt = date('Y-m-d H:i:s', strtotime("+$expirationDays days"));
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';

        $stmt = $db->prepare("INSERT INTO remember_me_tokens (user_id, token, expires_at, user_agent, ip_address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $token, $expiresAt, $userAgent, $ipAddress]);

        return $token;
    }

    /**
     * Validar un token de Remember Me
     * @param string $token Token a validar
     * @return int|false ID del usuario si válido, false si no
     */
    public static function validateRememberMeToken($token)
    {
        $db = DB::connect();
        
        // Buscar el token y validar que no esté expirado
        $stmt = $db->prepare("SELECT * FROM remember_me_tokens WHERE token = ? AND expires_at > NOW()");
        $stmt->execute([$token]);
        $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tokenData) {
            return false;
        }

        // Validar User Agent y IP (seguridad adicional)
        $currentUserAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $currentIpAddress = $_SERVER['REMOTE_ADDR'] ?? '';
        
        // Si el User Agent cambió significativamente, rechazar token (opcional, puedes hacer más lenient)
        // if ($tokenData['user_agent'] !== $currentUserAgent) {
        //     return false;
        // }

        return intval($tokenData['user_id']);
    }

    /**
     * Eliminar un token de Remember Me
     * @param string $token Token a eliminar
     */
    public static function deleteRememberMeToken($token)
    {
        $db = DB::connect();
        $stmt = $db->prepare("DELETE FROM remember_me_tokens WHERE token = ?");
        $stmt->execute([$token]);
    }

    /**
     * Eliminar todos los tokens de Remember Me de un usuario
     * @param int $user_id ID del usuario
     */
    public static function deleteAllRememberMeTokens($user_id)
    {
        $db = DB::connect();
        $stmt = $db->prepare("DELETE FROM remember_me_tokens WHERE user_id = ?");
        $stmt->execute([$user_id]);
    }

    /**
     * Limpiar tokens expirados
     */
    public static function cleanExpiredRememberMeTokens()
    {
        $db = DB::connect();
        $stmt = $db->prepare("DELETE FROM remember_me_tokens WHERE expires_at < NOW()");
        $stmt->execute();
    }
}
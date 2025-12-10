<?php
// FUNCIONS: login, registre, logout
require_once __DIR__ . '/../Model/User.php';

/**
 * Controlador d'usuaris
 * - Fem servir SESSIONS ($_SESSION) per controlar l'estat de l'usuari
 * - Les COOKIES s'utilitzen per guardar una clau que identifica la sessió al navegador.
 */
class UserController
{

    /**
     * Processa un POST de 'login' i retorna ['success'=>bool, 'errors'=>[]]
     */
    // Processa el login a partir d'un array de dades (pot ser $_POST)
    // Retorna array: ['success' => bool, 'errors' => array]
    public static function loginFromPost(array $post)
    {
        $username = trim($post['username'] ?? '');
        $password = $post['password'] ?? '';
        $errors = [];
        if ($username === '' || $password === '') {
            $errors[] = 'Cal introduir usuari i contrasenya';
            return ['success' => false, 'errors' => $errors];
        }
        $user = User::findByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'] ?? null;
            return ['success' => true, 'errors' => []];
        }
        $errors[] = 'Usuari o contrasenya incorrecta';
        return ['success' => false, 'errors' => $errors];
    }

    /**
     * Processa un POST de 'register' i retorna ['success'=>bool, 'errors'=>[]]
     */
    // Processa registre a partir d'un array (pot ser $_POST)
    // Retorna array: ['success' => bool, 'errors' => array]
    public static function registerFromPost(array $post)
    {
        $username = trim($post['username'] ?? '');
        $email = trim($post['email'] ?? '');
        $password = $post['password'] ?? '';
        $password2 = $post['password2'] ?? '';
        $errors = [];
        if ($username === '')
            $errors[] = 'El nom d\'usuari és obligatori';
        if ($email === '')
            $errors[] = "L'email és obligatori";
        if ($password === '')
            $errors[] = 'La contrasenya és obligatòria';
        if ($password !== $password2)
            $errors[] = 'Les contrasenyes no coincideixen';
        if (User::findByUsername($username))
            $errors[] = 'Aquest usuari ja existeix';
        if (User::findByEmail($email))
            $errors[] = 'Aquest email ja està registrat';
        // Comprovem la robustesa de la contrasenya: mínim 8+ caràcters, lletra, número
        if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errors[] = 'La contrasenya ha de tenir mínim 8 caràcters i incloure majúscules, minúscules i números';
        }
        if (count($errors) > 0)
            return ['success' => false, 'errors' => $errors];
        $hash = password_hash($password, PASSWORD_BCRYPT);
        User::create($username, $email, $hash);
        $user = User::findByUsername($username);
        if ($user) {
            // Guardem la sessió de l'usuari
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'] ?? null;
            return ['success' => true, 'errors' => []];
        }
        return ['success' => false, 'errors' => ['No s\'ha pogut crear l\'usuari']];
    }

    public static function logout()
    {
        // Tancar la sessió i eliminar la cookie de sessió (si n'hi ha)
        session_unset();
        session_destroy();
        // Eliminar cookie de sessió del navegador per a neteja completa
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
    }


    public static function generateTokenRecuperacio($user_id) {
        $token = bin2hex(random_bytes(16)); // Genera un token aleatori
        $expiration_date = date('Y-m-d H:i:s', strtotime('+1 hour')); // Expira en 1 hora

        // Emmagatzema el token a la base de dades
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO tokens_recuperacio (user_id, token, expiration_date) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $token, $expiration_date]);

        return $token;
    }

    //send token to user email phpMailer
    public static function sendTokenEmail($email, $token) {
        $resetLink = "http://yourdomain.com/reset_password.php?token=" . $token;

        $subject = "Recuperació de contrasenya";
        $message = "Feu clic al següent enllaç per restablir la vostra contrasenya: " . $resetLink;
        $headers = "From: no-reply@yourdomain.com\r\n";
        mail($email, $subject, $message, $headers);
    }

    public static function validateToken($token) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM tokens_recuperacio WHERE token = ?");
        $stmt->execute([$token]);
        $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($tokenData) {
            // Comprova si el token ha expirat
            if (strtotime($tokenData['expiration_date']) > time()) {
                return $tokenData['user_id'];
            } else {
                // Elimina el token caducat
                $stmt = $db->prepare("DELETE FROM tokens_recuperacio WHERE token = ?");
                $stmt->execute([$token]);
            }
        }
        return false;
    }

    public static function resetPassword($user_id, $new_password) {
        $hash = password_hash($new_password, PASSWORD_BCRYPT);
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $user_id]);

        // Elimina tots els tokens associats a l'usuari
        $stmt = $db->prepare("DELETE FROM tokens_recuperacio WHERE user_id = ?");
        $stmt->execute([$user_id]);
    }

    /**
     * Obtenir tots els usuaris
     */
    public static function getAllUsers()
    {
        return User::getAll();
    }

    /**
     * Eliminar un usuari amb autenticació
     * @param mixed $user_id ID del usuari a eliminar
     * @param mixed $current_user_id ID de l'usuari actual
     * @param mixed $role Rol de l'usuari actual
     * @return array{errors: string[], success: bool}
     */
    public static function deleteUser($user_id, $current_user_id, $role = null)
    {
        $errors = [];
        
        // Verificar que l'usuari actual és admin
        if ($role !== 'admin') {
            $errors[] = 'No tens permisos per eliminar usuaris';
            return ['success' => false, 'errors' => $errors];
        }

        // Validar que el ID és vàlid
        if ($user_id <= 0) {
            $errors[] = 'ID d\'usuari invàlid';
            return ['success' => false, 'errors' => $errors];
        }

        // No permitir que un admin s'elimini a si mateix
        if ($user_id == $current_user_id) {
            $errors[] = 'No pots eliminar la teva pròpia compte';
            return ['success' => false, 'errors' => $errors];
        }

        // Verificar que l'usuari existeix
        $user = User::findById($user_id);
        if (!$user) {
            $errors[] = 'L\'usuari no existeix';
            return ['success' => false, 'errors' => $errors];
        }

        // Eliminar l'usuari i els seus articles
        $db = DB::connect();
        try {
            // Primer, eliminar els articles de l'usuari
            $stmt = $db->prepare("DELETE FROM articles WHERE user_id = ?");
            $stmt->execute([$user_id]);

            // Després, eliminar l'usuari
            $ok = User::delete($user_id);
            
            if ($ok) {
                return ['success' => true, 'errors' => []];
            } else {
                $errors[] = 'No s\'ha pogut eliminar l\'usuari';
                return ['success' => false, 'errors' => $errors];
            }
        } catch (Exception $e) {
            $errors[] = 'Error en eliminar l\'usuari: ' . $e->getMessage();
            return ['success' => false, 'errors' => $errors];
        }
    }
}
?>
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
}

?>
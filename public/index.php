<?php
// Utilitzem SESSIONS per a l'autenticació. Les cookies s'utilitzen per guardar la cookie de sessió al navegador,

// session.gc_maxlifetime controla el temps en què PHP manté la informació de sessió al servidor.
ini_set('session.gc_maxlifetime', 2400); // durada del servidor per a dades de sessió
session_set_cookie_params(2400); // cookie del navegador dura 40min
session_start();

// Autoload controllers and router
require_once __DIR__ . '/../app/Controller/UserController.php';
require_once __DIR__ . '/../app/Controller/ArticleController.php';
require_once __DIR__ . '/../app/Router.php';

// Validar token de Remember Me al inicio
UserController::validateRememberMeTokenOnStartup();

// Gestionar accions POST (login / register / article update/create)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    // USUARI --> login / register
    if ($action === 'login') {
        $result = UserController::loginFromPost($_POST);
        if ($result['success']) {
            header('Location: index.php');
            exit;
        } else {
            $errors = $result['errors'];
            $_GET['view'] = 'login';
        }
    }
    if ($action === 'register') {
        $result = UserController::registerFromPost($_POST);
        if ($result['success']) {
            header('Location: index.php');
            exit;
        } else {
            $errors = $result['errors'];
            $_GET['view'] = 'register';
        }
    }

    // ARTICLES --> create / update
    if ($action === 'article_create' || $action === 'article_update') {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }
        if ($action === 'article_create') {
            $result = ArticleController::createFromPost($_POST, $_SESSION['user_id']);
        } else {
            $result = ArticleController::updateFromPost($_POST, $_SESSION['user_id'], $_SESSION['role'] ?? null);
        }
        if ($result['success']) {
            header('Location: index.php');
            exit;
        } else {
            $errors = $result['errors'];
        }
    }
}

// Gestionar accions GET (logout, delete)
if (isset($_GET['action'])) {
    $gAction = $_GET['action'];
    if ($gAction === 'logout') {
        UserController::logout();
        header('Location: index.php');
        exit;
    }
    if ($gAction === 'delete' && isset($_GET['id'])) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }
        $id = intval($_GET['id']);
        $result = ArticleController::deleteWithAuth($id, $_SESSION['user_id'], $_SESSION['role'] ?? null);
        if ($result['success']) {
            header('Location: index.php');
            exit;
        } else {
            $errors = $result['errors'];
        }
    }
    if ($gAction === 'delete_user' && isset($_GET['id'])) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }
        $user_id = intval($_GET['id']);
        $result = UserController::deleteUser($user_id, $_SESSION['user_id'], $_SESSION['role'] ?? null);
        if ($result['success']) {
            header('Location: index.php?view=user_management');
            exit;
        } else {
            $errors = $result['errors'];
        }
    }
}

// Initialize router and dispatch
$errors = [];
$router = new Router();
$router->dispatch();
<?php
// Utilitzem SESSIONS per a l'autenticació. Les cookies s'utilitzen per guardar la cookie de sessió al navegador,

// session.gc_maxlifetime controla el temps en què PHP manté la informació de sessió al servidor.
ini_set('session.gc_maxlifetime', 2400); // durada del servidor per a dades de sessió
session_set_cookie_params(2400); // cookie del navegador dura 40min
session_start();

// Autoload controllers (they will require models internals)
require_once __DIR__ . '/../app/Controller/UserController.php';
require_once __DIR__ . '/../app/Controller/ArticleController.php';

$error = null;
$errors = [];

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
            $error = $errors[0] ?? 'Usuari o contrasenya incorrectes';
            $username = $_POST['username'] ?? '';
            // Permetem romandre a la vista de login conservant dades
            $view = 'login';
        }
    }
    if ($action === 'register') {
        $result = UserController::registerFromPost($_POST);
        if ($result['success']) {
            header('Location: index.php');
            exit;
        } else {
            $errors = $result['errors'];
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            // Permetem romandre a la vista de registre conservant dades
            $view = 'register';
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
}

// Paginació 
$perPageOptions = [1,2,4,6];
$perPage = isset($_GET['perPage']) ? max(1,intval($_GET['perPage'])) : 4; // default 4
if (!in_array($perPage, $perPageOptions)) $perPage = 4;
$page = isset($_GET['page']) ? max(1,intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// Paràmetres d'ordenació
$allowedSortBy = ['creation_date', 'title'];
$allowedSortOrder = ['ASC', 'DESC'];
$sortBy = isset($_GET['sortBy']) && in_array($_GET['sortBy'], $allowedSortBy) ? $_GET['sortBy'] : 'creation_date';
$sortOrder = isset($_GET['sortOrder']) && in_array($_GET['sortOrder'], $allowedSortOrder) ? $_GET['sortOrder'] : 'DESC';

// Articles paginats 
$totalArticles = ArticleController::countAll();
$articles = ArticleController::getPaginated($perPage, $offset, $sortBy, $sortOrder);

// ROUTER
if (!isset($view)) {
    $view = $_GET['view'] ?? 'home';
}
switch ($view) {
    case 'login':
        include __DIR__ . '/../app/View/login.view.php';
        break;
    case 'register':
        include __DIR__ . '/../app/View/register.view.php';
        break;
    case 'article_edit':
        // Donar $article a la view si id està definit
        $article = null;
        if (isset($_GET['id'])) {
            $article = ArticleController::findById(intval($_GET['id']));
        }
        include __DIR__ . '/../app/View/article_edit.view.php';
        break;
    case 'my_articles':
        // Nomes els articles de l'usuari actual
        if (isset($_SESSION['user_id'])) {
            // Paginar per usuari actual
            $totalArticles = ArticleController::countByUser($_SESSION['user_id']);
            $articles = ArticleController::getPaginatedByUser($perPage, $offset, $_SESSION['user_id'], $sortBy, $sortOrder);
        }
        include __DIR__ . '/../app/View/home.view.php';
        break;
    case 'user_management':
        // mostra nomes en cas de tenir el rol d'administrador
        include __DIR__ . '/../app/View/user_management.view.php';
        break;
    case 'recover';
        include __DIR__ . '/../app/View/recover.view.php';
        break;


    default:
        include __DIR__ . '/../app/View/home.view.php';
        break;
}

?>
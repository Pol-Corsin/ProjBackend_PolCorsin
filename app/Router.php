<?php

class Router
{
    private $routes = [];
    private $view;
    private $viewVars = [];

    public function __construct()
    {
        $this->registerRoutes();
    }

    /**
     * Register all available routes
     */
    private function registerRoutes()
    {
        // Define routes as: view => [
        //   'file' => path to view file,
        //   'beforeRender' => callable (optional) to prepare data,
        //   'requireAuth' => bool,
        //   'requireAdmin' => bool
        // ]

        $this->routes['home'] = [
            'file' => __DIR__ . '/View/home.view.php',
            'beforeRender' => [$this, 'prepareHome'],
        ];

        $this->routes['login'] = [
            'file' => __DIR__ . '/View/login.view.php',
        ];

        $this->routes['register'] = [
            'file' => __DIR__ . '/View/register.view.php',
        ];

        $this->routes['recover'] = [
            'file' => __DIR__ . '/View/recover.view.php',
        ];

        $this->routes['article_edit'] = [
            'file' => __DIR__ . '/View/article_edit.view.php',
            'beforeRender' => [$this, 'prepareArticleEdit'],
            'requireAuth' => true,
        ];

        $this->routes['my_articles'] = [
            'file' => __DIR__ . '/View/home.view.php',
            'beforeRender' => [$this, 'prepareMyArticles'],
            'requireAuth' => true,
        ];

        $this->routes['user_management'] = [
            'file' => __DIR__ . '/View/user_management.view.php',
            'beforeRender' => [$this, 'prepareUserManagement'],
            'requireAdmin' => true,
        ];

        $this->routes['article'] = [
            'file' => __DIR__ . '/View/article.view.php',
            'beforeRender' => [$this, 'prepareSingleArticle'],
        ];
    }

    /**
     * Get the requested view, with fallback to 'home'
     */
    public function getRequestedView()
    {
        return $_GET['view'] ?? 'home';
    }

    /**
     * Check if user is authenticated
     */
    private function isAuthenticated()
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Check if user is admin
     */
    private function isAdmin()
    {
        return ($SESSION['role'] ?? null) === 'admin';
    }

    /**
     * Route the request: validate permissions, prepare data, render view
     */
    public function dispatch()
    {
        $this->view = $this->getRequestedView();

        // If view doesn't exist, fallback to home
        if (!isset($this->routes[$this->view])) {
            $this->view = 'home';
        }

        $route = $this->routes[$this->view];

        // Check authentication
        if (isset($route['requireAuth']) && $route['requireAuth'] && !$this->isAuthenticated()) {
            header('Location: index.php');
            exit;
        }

        // Check admin role
        if (isset($route['requireAdmin']) && $route['requireAdmin'] && ($_SESSION['role'] ?? null) !== 'admin') {
            header('Location: index.php');
            exit;
        }

        // Execute beforeRender callback if provided
        if (isset($route['beforeRender']) && is_callable($route['beforeRender'])) {
            call_user_func($route['beforeRender']);
        }

        // Include the view file with viewVars in scope
        extract($this->viewVars);
        include $route['file'];
    }

    /**
     * Set a view variable
     */
    public function set($key, $value)
    {
        $this->viewVars[$key] = $value;
    }

    /**
     * Get a view variable
     */
    public function get($key)
    {
        return $this->viewVars[$key] ?? null;
    }

    // ============ BEFORE RENDER CALLBACKS ============

    /**
     * Prepare data for home view (paginated articles)
     */
    public function prepareHome()
    {
        require_once __DIR__ . '/Controller/ArticleController.php';

        $perPageOptions = [1, 2, 4, 6];
        $perPage = isset($_GET['perPage']) ? max(1, intval($_GET['perPage'])) : 4;
        if (!in_array($perPage, $perPageOptions))
            $perPage = 4;
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($page - 1) * $perPage;

        $allowedSortBy = ['creation_date', 'title'];
        $allowedSortOrder = ['ASC', 'DESC'];
        $sortBy = isset($_GET['sortBy']) && in_array($_GET['sortBy'], $allowedSortBy) ? $_GET['sortBy'] : 'creation_date';
        $sortOrder = isset($_GET['sortOrder']) && in_array($_GET['sortOrder'], $allowedSortOrder) ? $_GET['sortOrder'] : 'DESC';

        $totalArticles = ArticleController::countAll();
        $articles = ArticleController::getPaginated($perPage, $offset, $sortBy, $sortOrder);

        $this->set('articles', $articles);
        $this->set('totalArticles', $totalArticles);
        $this->set('perPage', $perPage);
        $this->set('page', $page);
        $this->set('sortBy', $sortBy);
        $this->set('sortOrder', $sortOrder);
        $this->set('search', $_GET['q'] ?? '');
    }

    /**
     * Prepare data for article edit view
     */
    public function prepareArticleEdit()
    {
        require_once __DIR__ . '/Controller/ArticleController.php';

        $article = null;
        if (isset($_GET['id'])) {
            $article = ArticleController::findById(intval($_GET['id']));
        }
        $this->set('article', $article);
    }

    /**
     * Prepare data for my_articles view (user's articles)
     */
    public function prepareMyArticles()
    {
        require_once __DIR__ . '/Controller/ArticleController.php';

        $perPageOptions = [1, 2, 4, 6];
        $perPage = isset($_GET['perPage']) ? max(1, intval($_GET['perPage'])) : 4;
        if (!in_array($perPage, $perPageOptions))
            $perPage = 4;
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($page - 1) * $perPage;

        $allowedSortBy = ['creation_date', 'title'];
        $allowedSortOrder = ['ASC', 'DESC'];
        $sortBy = isset($_GET['sortBy']) && in_array($_GET['sortBy'], $allowedSortBy) ? $_GET['sortBy'] : 'creation_date';
        $sortOrder = isset($_GET['sortOrder']) && in_array($_GET['sortOrder'], $allowedSortOrder) ? $_GET['sortOrder'] : 'DESC';

        $totalArticles = ArticleController::countByUser($_SESSION['user_id']);
        $articles = ArticleController::getPaginatedByUser($perPage, $offset, $_SESSION['user_id'], $sortBy, $sortOrder);

        $this->set('articles', $articles);
        $this->set('totalArticles', $totalArticles);
        $this->set('perPage', $perPage);
        $this->set('page', $page);
        $this->set('sortBy', $sortBy);
        $this->set('sortOrder', $sortOrder);
        $this->set('search', $_GET['q'] ?? '');
    }

    /**
     * Prepare data for user_management view
     */
    public function prepareUserManagement()
    {
        require_once __DIR__ . '/Controller/UserController.php';

        $users = UserController::getAllUsers();
        $this->set('users', $users);
    }

    /**
     * Prepare data for single article view
     */
    public function prepareSingleArticle()
    {
        require_once __DIR__ . '/Controller/ArticleController.php';

        $article = null;
        if (isset($_GET['id'])) {
            $article = ArticleController::findById(intval($_GET['id']));
        }
        $this->set('article', $article);
    }
}

?>

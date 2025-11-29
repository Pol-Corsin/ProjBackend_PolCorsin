<?php
// FUNCIONS: mostrar, crear, editar, eliminar
require_once __DIR__ . '/../Model/Article.php';

class ArticleController
{


    public static function findById($id)
    {
        return Article::findById($id);
    }

    public static function countByUser($user_id)
    {
        return Article::countByUser($user_id);
    }
    public static function getPaginatedByUser($limit, $offset, $user_id)
    {
        return Article::getPaginatedByUser($limit, $offset, $user_id);
    }
    // Wrapper per obtenir el total d'articles
    public static function countAll()
    {
        return Article::countAll();
    }
    // Wrapper per articles paginats
    public static function getPaginated($limit, $offset)
    {
        return Article::getPaginated($limit, $offset);
    }

    /**
     *  aquesta funció processa un POST per crear un article
     * @param array $post
     * @param mixed $user_id
     * @return array{errors: string[], success: bool}
     */
    public static function createFromPost(array $post, $user_id)
    {
        $title = trim($post['title'] ?? '');
        $content = trim($post['content'] ?? '');
        $errors = [];
        
        if ($title === '')
            $errors[] = 'El títol és obligatori';
        if ($content === '')
            $errors[] = 'El contingut és obligatori';
        if (count($errors) > 0)
            return ['success' => false, 'errors' => $errors];
        $ok = Article::create($title, $content, $user_id);
        return ['success' => (bool) $ok, 'errors' => $ok ? [] : ['No s\'ha pogut crear l\'article']];
    }

    /**
     *  aquesta funció processa un POST per actualitzar un article
     * @param array $post
     * @param mixed $user_id
     * @param mixed $role
     * @return array{errors: string[], success: bool}
     */
    public static function updateFromPost(array $post, $user_id, $role = null)
    {
        $id = intval($post['id'] ?? 0); // ID de l'article a actualitzar
        $title = trim($post['title'] ?? ''); // Nou títol
        $content = trim($post['content'] ?? ''); // Nou contingut
        $errors = [];
        // verificacions
        if ($id <= 0)
            $errors[] = 'ID d\'article invàlid';
        if ($title === '')
            $errors[] = 'El títol és obligatori';
        if ($content === '')
            $errors[] = 'El contingut és obligatori';
        // si hi ha errors, retornar
        if (count($errors) > 0)
            return ['success' => false, 'errors' => $errors];


        $article = Article::findById($id); // obtenir l'article existen
        if (!$article)
            return ['success' => false, 'errors' => ['Article no trobat']];

        // comprovar permisos
        if ($article['user_id'] != $user_id && $role !== 'admin')
            return ['success' => false, 'errors' => ['No tens permisos per editar aquest article']];
        $ok = Article::update($id, $title, $content);

        return ['success' => (bool) $ok, 'errors' => $ok ? [] : ['No s\'ha pogut actualitzar l\'article']];
    }

    /**
     * Elimina un article si l'usuari té permisos
     * @param mixed $id
     * @param mixed $user_id
     * @param mixed $role
     * @return array{errors: string[], success: bool}
     */
    public static function deleteWithAuth($id, $user_id, $role = null)
    {
        $article = Article::findById($id);
        if (!$article)
            return ['success' => false, 'errors' => ['Article no trobat']];
        if ($article['user_id'] != $user_id && $role !== 'admin')
            return ['success' => false, 'errors' => ['No tens permisos per eliminar aquest article']];
        $ok = Article::delete($id);
        return ['success' => (bool) $ok, 'errors' => $ok ? [] : ['No s\'ha pogut eliminar l\'article']];
    }
}

?>
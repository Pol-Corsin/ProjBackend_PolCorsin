<?php
require_once __DIR__ . '/../../config/db-connection.php';

class Article
{
    public static function getAll()
    {
        $db = DB::connect();
        $stmt = $db->query("SELECT * FROM articles ORDER BY creation_date DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Retorna el total d'articles a la BD
    public static function countAll()
    {
        $db = DB::connect();
        $stmt = $db->query("SELECT COUNT(*) as cnt FROM articles");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($row['cnt'] ?? 0);
    }

    // Retorna articles paginats
    public static function getPaginated($limit, $offset)
    {
        $db = DB::connect();
        $stmt = $db->prepare("SELECT * FROM articles ORDER BY creation_date DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findByUser($user_id)
    {
        $db = DB::connect();
        $stmt = $db->prepare("SELECT * FROM articles WHERE user_id = ? ORDER BY creation_date DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Count articles by user
    public static function countByUser($user_id)
    {
        $db = DB::connect();
        $stmt = $db->prepare("SELECT COUNT(*) as cnt FROM articles WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($row['cnt'] ?? 0);
    }

    // Paginated for user
    public static function getPaginatedByUser($limit, $offset, $user_id)
    {
        $db = DB::connect();
        $stmt = $db->prepare("SELECT * FROM articles WHERE user_id = ? ORDER BY creation_date DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById($id)
    {
        $db = DB::connect();
        $stmt = $db->prepare("SELECT * FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //CRUD
    public static function create($title, $content, $user_id)
    {
        $db = DB::connect();
        $stmt = $db->prepare("INSERT INTO articles (title, content, user_id) VALUES (?, ?, ?)");
        return $stmt->execute([$title, $content, $user_id]);
    }

    public static function update($id, $title, $content)
    {
        $db = DB::connect();
        $stmt = $db->prepare("UPDATE articles SET title=?, content=? WHERE id=?");
        return $stmt->execute([$title, $content, $id]);
    }

    public static function delete($id)
    {
        $db = DB::connect();
        $stmt = $db->prepare("DELETE FROM articles WHERE id=?");
        return $stmt->execute([$id]);
    }
}

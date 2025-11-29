<?php

// Obtenir l'article si s'edita
if ($article && !isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
// Si s'edita un article, comprovar que l'usuari té permisos
if ($article && !(($article['user_id'] == ($_SESSION['user_id'] ?? null)) || (($_SESSION['role'] ?? null) === 'admin'))) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/styles.css">
    <title>Editar / Crear article</title>
</head>
<body>
    <?php include __DIR__ . '/partials/nav.view.php'; ?>
    <h1><?= $article ? 'Editar article' : 'Crear article' ?></h1>
    <?php if (isset($errors) && count($errors) > 0): ?>
        <ul class="error">
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form action="index.php" method="post">
        <input type="hidden" name="action" value="article_<?= $article ? 'update' : 'create' ?>">
        <?php if ($article): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($article['id']) ?>">
        <?php endif; ?>
        <label>Títol:</label>
        <input type="text" name="title" required value="<?= htmlspecialchars($article['title'] ?? '') ?>">
        <label>Contingut:</label>
        <textarea name="content" rows="6" required><?= htmlspecialchars($article['content'] ?? '') ?></textarea>
        <button type="submit"><?= $article ? 'Actualitzar' : 'Crear' ?></button>
    </form>
    <p><a href="index.php">Tornar a Home</a></p>
</body>
</html>

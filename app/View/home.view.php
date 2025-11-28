<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Home</title>
</head>

<body>
    <h1>Pagina home d'articles</h1>

    <?php if (isset($_SESSION['user_id'])): ?> <!-- si l'usuari està loguejat -->

        <p>Hola, <?= $_SESSION['username']; ?> | <a href="logout.php">Cerrar sesión</a></p>
        <a href="article_edit.view.php">Gestionar els meus articles</a>

    <?php else: ?>
        <p><a href="login.php">Login</a> | <a href="register.php">Register</a></p>
    <?php endif; ?>

    <h2>Articles:</h2>
    <?php foreach ($articles as $article): ?>
        <div class="tarja-article">
            <h3><?= htmlspecialchars($article['title']); ?></h3>
            <p><?= nl2br(htmlspecialchars($article['content'])); ?></p> <!-- nl2br per mantenir salts de línia -->
            <?php
            if (
                isset($_SESSION['user_id']) &&
                ($article['user_id'] == $_SESSION['user_id'] || $_SESSION['role'] === 'admin') 
            ): ?>
                <a href="article_edit.view.php?id=<?= $article['id']; ?>">Editar</a>
                <a href="article_delete.php?id=<?= $article['id']; ?>"
                    onclick="return confirm('Estàs segur que vols eliminar aquest article?');">Eliminar</a>
            <?php endif; ?>
        </div>
        <hr>
    <?php endforeach; ?>
</body>

</html>
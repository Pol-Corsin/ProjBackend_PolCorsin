<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <title>Home</title>
</head>

<body>
    <link rel="stylesheet" href="css/styles.css">
    <?php include __DIR__ . '/partials/nav.view.php'; ?>
    <div class="container">
    <?php if (isset($errors) && count($errors) > 0): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php elseif (isset($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>



    <h2 style="color: white">Articles públics</h2>
    <?php foreach ($articles as $article): ?>
        <div class="tarja-article">
            <h3><?= htmlspecialchars($article['title']); ?></h3>
            <p><?= nl2br(htmlspecialchars($article['content'])); ?></p> <!-- nl2br per mantenir salts de linia -->
            <?php
            if (
                isset($_SESSION['user_id']) &&
                ($article['user_id'] == $_SESSION['user_id'] || ($_SESSION['role'] ?? null) === 'admin') 
            ): ?>
                <a href="index.php?view=article_edit&id=<?= $article['id']; ?>">Editar</a>
                <a href="index.php?action=delete&id=<?= $article['id']; ?>"
                    onclick="return confirm('Estàs segur que vols eliminar aquest article?');">Eliminar</a>
            <?php endif; ?>
        </div>
        <hr>
    <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    
</body>

</html>
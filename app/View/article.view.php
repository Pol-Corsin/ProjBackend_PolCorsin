<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <title>Article</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php include __DIR__ . '/partials/nav.view.php'; ?>
<main class="container">
    <?php if (isset($article) && $article): ?>
        <article class="tarja-article">
            <h2><?= htmlspecialchars($article['title']) ?></h2>
            <p><em>Creació: <?= htmlspecialchars($article['creation_date']) ?> · Per: <?= htmlspecialchars($article['author'] ?? 'Anònim') ?></em></p>
            <div><?= nl2br(htmlspecialchars($article['content'])) ?></div>
        </article>
    <?php else: ?>
        <p>Article no trobat.</p>
    <?php endif; ?>
</main>
</body>
</html>
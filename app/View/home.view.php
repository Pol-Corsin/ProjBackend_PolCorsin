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

    <div class="title">
        <h1>Home</h1>
    </div>

    <!-- ! Los label "no arriben an cap lloc" !Per implementar --> <!-- ! TODO -->
    <div class="searchbar" style="margin-bottom: 10px">
        <input type="text" id="iSearch" placeholder="Search here..."> <!-- Barra de cerca -->
    </div>

    
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
    <div class="container">
        <?php
        $totalPages = max(1, (int)ceil($totalArticles / $perPage));
        $currentPage = max(1, $page);

        $baseUrl = 'index.php'; // Base URL for pagination links
        $viewParam = isset($_GET['view']) ? 'view=' . urlencode($_GET['view']) . '&' : '';  // Preserve view parameter

        ?>
        <div class="pagination">
            <?php if ($currentPage > 1): ?>
                <a href="<?= $baseUrl ?>?<?= $viewParam ?>page=1&perPage=<?= $perPage ?>">Principi</a>
                <a href="<?= $baseUrl ?>?<?= $viewParam ?>page=<?= $currentPage - 1 ?>&perPage=<?= $perPage ?>">Anterior</a>
            <?php endif; ?>

            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <?php if ($p == $currentPage): ?>
                    <span class="page current"><?= $p ?></span>
                <?php else: ?>
                    <a class="page" href="<?= $baseUrl ?>?<?= $viewParam ?>page=<?= $p ?>&perPage=<?= $perPage ?>"><?= $p ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <a href="<?= $baseUrl ?>?<?= $viewParam ?>page=<?= $currentPage + 1 ?>&perPage=<?= $perPage ?>">Seguent</a>
                <a href="<?= $baseUrl ?>?<?= $viewParam ?>page=<?= $totalPages ?>&perPage=<?= $perPage ?>">Final</a>
            <?php endif; ?>

            <!-- perPage dropdown -->
            <div class="perpage">
                <form method="get" action="<?= $baseUrl ?>">
                    <?php if (isset($_GET['view'])): ?><input type="hidden" name="view" value="<?= htmlspecialchars($_GET['view']) ?>"><?php endif; ?>
                    <label>Mostra per pàgina:</label>
                    <select name="perPage" onchange="this.form.submit()">
                        <?php foreach ([1,2,4,6] as $pp): ?>
                            <option value="<?= $pp ?>" <?php if ($pp == $perPage) echo 'selected'; ?>><?= $pp ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="page" value="1">
                </form>
            </div>
        </div>
    </div>
</body>

</html>
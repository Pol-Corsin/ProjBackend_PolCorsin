<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <title>Home</title>
</head>

<body>
    <link rel="stylesheet" href="css/styles.css">
    <?php include __DIR__ . '/partials/nav.view.php'; ?>
    <main class="container">
        <?php if (isset($errors) && count($errors) > 0): ?>
            <div role="alert" aria-live="polite">
                <ul style="color:red;">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php elseif (isset($error)): ?>
            <p style="color:red;" role="alert" aria-live="polite"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <section class="title">
            <h1>Home</h1>
        </section>

        <!-- Search bar with accessible label -->
        <section class="searchbar" style="margin-bottom: 10px">
            <label for="iSearch"></label>
            <input type="text" id="iSearch" placeholder="Escriu aquí per cercar..."> <!-- Barra de cerca -->
        </section>

        <!-- Articles section -->
        <section class="articles-list">
            <?php foreach ($articles as $article): ?>
                <article class="tarja-article">
                    <h3><?= htmlspecialchars($article['title']); ?></h3>
                    <p><?= nl2br(htmlspecialchars($article['content'])); ?></p>
                    <?php
                    if (
                        isset($_SESSION['user_id']) &&
                        ($article['user_id'] == $_SESSION['user_id'] || ($_SESSION['role'] ?? null) === 'admin')
                    ): ?>
                        <div class="article-actions">
                            <a href="index.php?view=article_edit&id=<?= $article['id']; ?>"
                                aria-label="Editar article: <?= htmlspecialchars($article['title']) ?>">Editar</a>
                            <a href="index.php?action=delete&id=<?= $article['id']; ?>"
                                onclick="return confirm('Estàs segur que vols eliminar aquest article?');"
                                aria-label="Eliminar article: <?= htmlspecialchars($article['title']) ?>">Eliminar</a>
                        </div>
                    <?php endif; ?>
                </article>
                <hr>
            <?php endforeach; ?>
        </section>
    </main>

    <!-- ! Pagination -->
    <section class="container pagination-section">
        <?php
        $totalPages = max(1, (int) ceil($totalArticles / $perPage));
        $currentPage = max(1, $page);

        $baseUrl = 'index.php'; // Base URL for pagination links
        $viewParam = isset($_GET['view']) ? 'view=' . urlencode($_GET['view']) . '&' : '';  // Preserve view parameter
        
        ?>

        <nav class="pagination" aria-label="Paginació d'articles">
            <!-- Pagination controls -->
            <div class="pagination-controls">
                <?php if ($currentPage > 1): ?>
                    <a href="<?= $baseUrl ?>?<?= $viewParam ?>page=1&perPage=<?= $perPage ?>&sortBy=<?= $sortBy ?>&sortOrder=<?= $sortOrder ?>"
                        aria-label="Primera pàgina">Principi</a>
                    <a href="<?= $baseUrl ?>?<?= $viewParam ?>page=<?= $currentPage - 1 ?>&perPage=<?= $perPage ?>&sortBy=<?= $sortBy ?>&sortOrder=<?= $sortOrder ?>"
                        aria-label="Pàgina anterior">Anterior</a>
                <?php endif; ?>

                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <?php if ($p == $currentPage): ?>
                        <span class="page current" aria-current="page" aria-label="Pàgina <?= $p ?> (actual)"><?= $p ?></span>
                    <?php else: ?>
                        <a class="page"
                            href="<?= $baseUrl ?>?<?= $viewParam ?>page=<?= $p ?>&perPage=<?= $perPage ?>&sortBy=<?= $sortBy ?>&sortOrder=<?= $sortOrder ?>"
                            aria-label="Pàgina <?= $p ?>"><?= $p ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?= $baseUrl ?>?<?= $viewParam ?>page=<?= $currentPage + 1 ?>&perPage=<?= $perPage ?>&sortBy=<?= $sortBy ?>&sortOrder=<?= $sortOrder ?>"
                        aria-label="Pàgina següent">Seguent</a>
                    <a href="<?= $baseUrl ?>?<?= $viewParam ?>page=<?= $totalPages ?>&perPage=<?= $perPage ?>&sortBy=<?= $sortBy ?>&sortOrder=<?= $sortOrder ?>"
                        aria-label="Última pàgina">Final</a>
                <?php endif; ?>
            </div>

            <!-- perPage dropdown -->
            <div class="perpage">
                <form method="get" action="<?= $baseUrl ?>">
                    <?php if (isset($_GET['view'])): ?><input type="hidden" name="view"
                            value="<?= htmlspecialchars($_GET['view']) ?>"><?php endif; ?>
                    <label for="perPageSelect">Mostra per pàgina:</label>
                    <select id="perPageSelect" name="perPage" onchange="this.form.submit()"
                        aria-label="Selecciona quants articles mostrar per pàgina">
                        <?php foreach ([1, 2, 4, 6] as $pp): ?>
                            <option value="<?= $pp ?>" <?php if ($pp == $perPage)
                                    echo 'selected'; ?>><?= $pp ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="page" value="1">
                    <input type="hidden" name="sortBy" value="<?= $sortBy ?>">
                    <input type="hidden" name="sortOrder" value="<?= $sortOrder ?>">
                </form>
            </div>

            <!-- Sort options -->
            <div class="sort-options">
                <form method="get" action="<?= $baseUrl ?>">
                    <?php if (isset($_GET['view'])): ?><input type="hidden" name="view"
                            value="<?= htmlspecialchars($_GET['view']) ?>"><?php endif; ?>

                    <label for="sortBySelect">Order:</label>
                    <select id="sortBySelect" name="sortBy" aria-label="Selecciona per quin camp ordenar">
                        <option value="creation_date" <?php if (($sortBy ?? 'creation_date') == 'creation_date')
                            echo 'selected'; ?>>Date</option>
                        <option value="title" <?php if (($sortBy ?? 'creation_date') == 'title')
                            echo 'selected'; ?>>Title
                        </option>
                    </select>

                    <label for="sortOrderSelect">Ordre:</label>
                    <select id="sortOrderSelect" name="sortOrder" aria-label="Selecciona l'ordre d'ordenació">
                        <option value="DESC" <?php if (($sortOrder ?? 'DESC') == 'DESC')
                            echo 'selected'; ?>>DESC</option>
                        <option value="ASC" <?php if (($sortOrder ?? 'DESC') == 'ASC')
                            echo 'selected'; ?>>ASC</option>
                    </select>

                    <input type="hidden" name="page" value="1">
                    <input type="hidden" name="perPage" value="<?= $perPage ?>">
                    <button type="submit">Aplicar</button>
                </form>
            </div>
        </nav>
    </section> <!-- End Pagination -->
</body>

</html>
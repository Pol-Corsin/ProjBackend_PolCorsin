<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN - User Management</title> <!-- aixo es admin-exclusiu -->
</head>

<body>
    <link rel="stylesheet" href="css/styles.css">
    <?php include __DIR__ . '/partials/nav.view.php'; ?>
    <main class="container">
        <section class="title">
            <h1>Gestió d'Usuaris</h1>
        </section>

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

        <section class="users-list">
            <?php if (empty($users)): ?>
                <p>No hi ha usuaris registrats.</p>
            <?php else: ?>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom d'usuari</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Accions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['id']); ?></td>
                                <td><?= htmlspecialchars($user['username']); ?></td>
                                <td><?= htmlspecialchars($user['email']); ?></td>
                                <td><?= htmlspecialchars($user['role'] ?? 'user'); ?></td>
                                <td>
                                    <?php if ($_SESSION['user_id'] != $user['id']): ?>
                                        <a href="index.php?action=delete_user&id=<?= $user['id']; ?>"
                                            onclick="return confirm('Estàs segur que vols eliminar aquest usuari? Això eliminarà tots els seus articles.');"
                                            class="delete-link"
                                            aria-label="Eliminar usuari: <?= htmlspecialchars($user['username']) ?>">Eliminar</a>
                                    <?php else: ?>
                                        <span class="current-user">(YOU)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>

</body>

</html>
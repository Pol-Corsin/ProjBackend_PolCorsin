<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <title>Iniciar sessió</title>
</head>

<body>
    <?php include __DIR__ . '/partials/nav.view.php'; ?>
    <div class="container">
    <link rel="stylesheet" href="css/styles.css">
    <h1>Iniciar sessió</h1>

    <?php if (isset($errors) && count($errors) > 0): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php elseif (isset($error)): ?>
        <p class='error' style='color:red;'><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="index.php" method="post">
        <input type="hidden" name="action" value="login">
        <label>Usuari:</label>
        <input type="text" name="username" value="<?= $username ?? '' ?>" required>
        <label>Contrasenya:</label>
        <input type="password" name="password" required>
        <button type="submit">Entrar</button>
    </form>
    <p><a href="index.php?view=register">Registrar-se</a> | <a href="index.php?view=recover">Has oblidat la contrasenya?</a></p> <!-- Falta implementar recover view -->
    </div>
</body>

</html>
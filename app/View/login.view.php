<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <title>Iniciar sessió</title>
</head>

<body>
    <?php include __DIR__ . '/partials/nav.view.php'; ?>
    <main class="container">
        <link rel="stylesheet" href="css/styles.css">
        <h1>Iniciar sessió</h1>

        <?php if (isset($errors) && count($errors) > 0): ?>
            <div role="alert" aria-live="polite">
                <ul style="color:red;">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php elseif (isset($error)): ?>
            <p class='error' style='color:red;' role="alert" aria-live="polite"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form class="auth-form" action="index.php" method="post"> 
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label for="login_username">Usuari:</label>
                <input type="text" id="login_username" name="username" value="<?= $username ?? '' ?>" required>
            </div>
            <div class="form-group">
                <label for="login_password">Contrasenya:</label>
                <input type="password" id="login_password" name="password" required>
            </div>
            <button type="submit" aria-label="Entrar a la plataforma">Entrar</button>
        </form>
        <p style="color: #a324ae"><a href="index.php?view=register">Registrar-se</a> | <a href="index.php?view=recover">Has oblidat la contrasenya?</a></p> <!-- Falta implementar recover view -->
    </main>
</body>

</html>
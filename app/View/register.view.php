<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <title>Registrar-se</title>
</head>

<body>
    <?php include __DIR__ . '/partials/nav.view.php'; ?>
    <div class="container">
    <link rel="stylesheet" href="css/styles.css">
    <h1>Registrar-se</h1>

    <!-- Mostrar errors si n'hi ha -->
    <?php if (isset($errors) && count($errors) > 0): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- Formulari Register -->
    <form action="index.php" method="post">
        <input type="hidden" name="action" value="register">
        <label>Usuari:</label>
        <input type="text" name="username" value="<?= $username ?? '' ?>" required>
        <label>Email:</label>
        <input type="email" name="email" value="<?= $email ?? '' ?>" required>
        <label>Contrasenya:</label>
        <input type="password" name="password" required>
        <label>Repetir contrasenya:</label>
        <input type="password" name="password2" required>
        <button type="submit">Registrar</button>
    </form>
    <p><a href="index.php?view=login">Iniciar sessió</a></p>
    </div>
</body>

</html>
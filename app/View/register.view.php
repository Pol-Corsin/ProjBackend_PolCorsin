<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <title>Registrar-se</title>
</head>

<body>
    <?php include __DIR__ . '/partials/nav.view.php'; ?>
    <main class="container">
        <link rel="stylesheet" href="css/styles.css">
        <h1>Registrar-se</h1>

        <!-- Mostrar errors si n'hi ha -->
        <?php if (isset($errors) && count($errors) > 0): ?>
            <div role="alert" aria-live="polite">
                <ul style="color:red;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Formulari Register -->
        <form class="auth-form" action="index.php" method="post">
            <input type="hidden" name="action" value="register">
            <div class="form-group">
                <label for="register_username">Usuari:</label>
                <input type="text" id="register_username" name="username" value="<?= $username ?? '' ?>" required>
            </div>
            <div class="form-group">
                <label for="register_email">Email:</label>
                <input type="email" id="register_email" name="email" value="<?= $email ?? '' ?>" required>
            </div>
            <fieldset>
                <legend>Contrasenya (mínim 8 caràcters, amb majúscula, minúscula i número)</legend>
                <div class="form-group">
                    <label for="register_password">Contrasenya:</label>
                    <input type="password" id="register_password" name="password" required aria-describedby="password_requirements">
                </div>
                <div class="form-group">
                    <label for="register_password2">Repetir contrasenya:</label>
                    <input type="password" id="register_password2" name="password2" required>
                </div>
                <p id="password_requirements" style="font-size: 0.9em; color: #666;">La contrasenya ha de tenir almenys 8 caràcters, inclosa una lletra majúscula, una minúscula i un dígit.</p>
            </fieldset>
            <button type="submit" aria-label="Registrar-se a la plataforma">Registrar</button>
        </form>
        <p><a href="index.php?view=login">Iniciar sessió</a></p>
    </main>
</body>

</html>
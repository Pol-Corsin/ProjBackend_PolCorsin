<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Recover</title>
</head>

<body>
    <?php include __DIR__ . '/partials/nav.view.php'; ?>
    <main class="controller" style="justify-content: center; align-items: center; display: flex; height: 80vh;">
        <link rel="stylesheet" href="css/styles.css">
        <h1>Password Recovery</h1>
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

        <p>An email has been sent with instructions to reset your password.</p>

        <form class="recover-form" action="index.php" method="post">
            <input type="hidden" name="action" value="recover">
            <div class="form-group">
                <label for="recover-email">Enter your email address:</label>
                <input type="email" id="recover-email" name="recover-email" required>
            </div>
            <button type="submit" aria-label="Send password recovery email">Send Recovery Email</button>
        </form>

    </main>
</body>

</html>
<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN - User Management</title>
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

    <div id="user_list">

    </div>
</body>
</html>
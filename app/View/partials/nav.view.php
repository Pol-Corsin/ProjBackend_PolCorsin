<?php
// Barra de navegació reutilitzable: "Inici" a l'esquerra, i accions a la dreta segons login
?>
<nav class="topnav">
  <div class="nav-left" aria-label="Navegació principal">
    <a href="index.php" aria-label="Anar a la pàgina d'inici">Inici</a>
  </div>

  <div class="nav-right">
    <?php if (($_SESSION['role'] ?? null) === 'admin'):  ?>
    <a href="index.php?view=user_management" aria-label="Gestionar usuaris (admin)">Gestionar Usuaris</a> <!--TODO --> 
    <?php endif; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="index.php?view=my_articles" aria-label="Gestionar els meus articles publicats">Gestionar els meus articles</a>
      <a href="index.php?view=article_edit" aria-label="Crear un nou article">Crear nou article</a>
      <a href="index.php?action=logout" aria-label="Tancar la sessió actual">Tancar sessió</a>
    <?php else: ?>
      <a href="index.php?view=login" aria-label="Iniciar sessió a la plataforma">Iniciar sessió</a>
      <a href="index.php?view=register" aria-label="Crear un nou compte d'usuari">Registrar-se</a>
    <?php endif; ?>
  </div>
  <div style="clear:both"></div>
</nav>

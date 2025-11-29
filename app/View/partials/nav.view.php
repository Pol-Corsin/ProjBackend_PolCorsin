<?php
// Barra de navegació reutilitzable: "Inici" a l'esquerra, i accions a la dreta segons login
?>
<nav class="topnav">
  <div class="nav-left">
    <a href="index.php">Inici</a>
  </div>
  <div class="nav-right">
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="index.php?view=my_articles">Gestionar els meus articles</a>
      <a href="index.php?view=article_edit">Crear nou article</a>
      <a href="index.php?action=logout">Tancar sessió</a>
    <?php else: ?>
      <a href="index.php?view=login">Iniciar sessió</a>
      <a href="index.php?view=register">Registrar-se</a>
    <?php endif; ?>
  </div>
  <div style="clear:both"></div>
</nav>

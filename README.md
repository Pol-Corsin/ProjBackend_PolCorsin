# ProjBackend_PolCorsin

- Sessions en PHP (login/logout)
- Un `public/index.php` per gestionar rutes i accions
- MVC: models, vistes i controladors
- CRUD (crear, llegir, editar, eliminar) per a articles
- Connexió a MySQL amb PDO

---

- `app/Model/`: classes per a accedir a la base de dades (`Article`, `User`).
- `app/Controller/`: controladors que centralitzen la lògica d'aplicació (`UserController`, `ArticleController`).
- `app/View/`: arxius de vista (HTML + PHP).
- `config/db-connection.php`: connexió PDO a la base de dades.
- `public/index.php`: controlador frontal i punt d’entrada.

---

## Com funciona `public/index.php`

- Fa `session_start()` per activar sessions.
- Processa formularis POST amb `action` (login, register, article_create, article_update).
- Processa operacions GET amb `action` (logout, delete).
- Selecciona la vista amb el paràmetre `view` (ex.: `index.php?view=login`).
- Carrega articles per mostrar a la vista principal o només els teus articles (`my_articles`).
 - Paginació: hi ha un control per navegar entre pàgines i triar quants elements per pàgina (1,2,4,6). Els paràmetres `page` i `perPage` es passen per GET.
 - Si hi ha errors en registrar-se, el formulari conservarà les dades.
 - Carrega articles per mostrar a la vista principal o només els teus articles (`my_articles`).

 El `index.php` només actua com a enrutador i encarrega les accions als controllers.

La sessió s'ha configurat per durar 40 minuts, utilitzem sessions per mantenir l'estat d'autenticació i la cookie de sessió per identificar el navegador. Per a un "remember me" caldria usar cookies persistents i tokens segurs.

---

## Sessions i permisos

- En iniciar sessió, es guarden dades a la sessió:

```php
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];
```

- Per tancar sessió: `index.php?action=logout`.
- Per mostrar opcions privades a les vistes es comprova `isset($_SESSION['user_id'])`.
- Per comprovar permisos d’edició/eliminació:

```php
if (isset($_SESSION['user_id']) && ($article['user_id'] == $_SESSION['user_id'] || ($_SESSION['role'] ?? null) === 'admin')) {
    // Mostrar editar/eliminar
}
```

---

## Crear / editar / eliminar articles

- `article_edit.view.php` fa POST a `index.php` amb `action=article_create` o `action=article_update`.
- Eliminar: `index.php?action=delete&id=...` (es comproven permisos abans d'eliminar).

---

- Com sé si un usuari està loguejat?
  - Comprovant `isset($_SESSION['user_id'])`.

---

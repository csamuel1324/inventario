<?php
  ob_start();
  require_once('includes/load.php');
  if($session->isUserLoggedIn(true)) { redirect('home.php', false); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio de Sesión</title>
  <link rel="stylesheet" href="usuario.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet">
</head>
<body>
  <!-- Barra superior -->
  <header class="navbar">
    <nav class="nav-container">
      <div class="nav-left">
        <img src="ulalogo2.png" alt="Logo de la Organización" class="org-logo">
        <a href="Reseña.html" class="nav-link">Reseña</a>
        <a href="Desarrolladores.html" class="nav-link">Desarrolladores</a>
      </div>
    </nav>
  </header>

  <!-- Contenido principal -->
  <div class="login-container">
    <div class="login-box">
      <h1>Iniciar Sesión Usuario</h1>

      <?php echo display_msg($msg); ?>

      <form method="post" action="auth.php">
        <label for="username">Usuario</label>
        <input type="text" name="username" id="username" placeholder="Introduce tu usuario" required>

        <label for="password">Contraseña</label>
        <input type="password" name="password" id="password" placeholder="Introduce tu contraseña" required>

        <div class="remember">
          <input type="checkbox" id="remember">
          <label for="remember">Recordar contraseña</label>
        </div>

        <button type="submit">Ingresar</button>
      </form>
    </div>
  </div>
</body>
</html>

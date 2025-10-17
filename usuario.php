<?php
session_start();

// Si ya está logueado, redirige
if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit();
}

// Capturar error desde URL
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="usuario.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet">
    <script src="usuario.js" defer></script>
</head>
<body>
    <!-- Barra superior -->
    <header class="navbar">
        <nav class="nav-container">
            <div class="nav-left">
                <img src="ulalogo2.png" alt="Logo de la Organización" class="org-logo">
                <a href="index.html" class="nav-link">Inicio</a>
                <a href="Reseña.html" class="nav-link">Reseña</a>
                <a href="Desarrolladores.html" class="nav-link">Desarrolladores</a>
            </div>
            <div class="nav-right">
                <button class="nav-btn" onclick="window.location.href='usuario.php';">Usuario</button>
                <button class="nav-btn" onclick="window.location.href='administrador.php';">Administrador</button>
            </div>
        </nav>
    </header>

    <!-- Contenido principal -->
    <div class="login-container">
        <div class="login-box">
            <h1>Iniciar Sesión Usuario</h1>

            <?php if ($error): ?>
                <p style="color: red; font-weight: bold;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <form action="auth.php" method="POST">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" placeholder="Introduce tu usuario" required>

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Introduce tu contraseña" required>

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

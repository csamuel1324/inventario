<?php
  $page_title = 'Inicio';
  require_once('includes/load.php');
  if (!$session->isUserLoggedIn(true)) { redirect('index.php', false); }
?>
<?php include_once('layouts/header.php'); ?>

<style>
  .home-container {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    min-height: 100vh;
    padding-top: 80px;
  }

    .welcome-box {
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(10px);
    padding: 40px 30px;
    border-radius: 20px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
    max-width: 600px;
    text-align: center;
    color: white;
    margin: 20px;
  }

  .welcome-box h1 {
    font-size: 32px;
    margin-bottom: 20px;
    color: white;
  }

  .welcome-box p {
    font-size: 16px;
    line-height: 1.6;
    color: #e0e0e0;
  }

  .welcome-box a {
    margin-top: 20px;
    display: inline-block;
    padding: 12px 25px;
    border-radius: 8px;
    background: linear-gradient(90deg, #007BFF, #0056b3);
    color: white;
    text-decoration: none;
    font-weight: bold;
    transition: transform 0.2s, box-shadow 0.2s;
  }

  .welcome-box a:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 10px rgba(0, 123, 255, 0.5);
  }
</style>

<div class="home-container">
  <div class="welcome-box">
    <h1>Bienvenido, <?php echo remove_junk(ucfirst($user['name'])); ?> 👋</h1>
    <p>Este es tu sistema de inventario. Usa el menú lateral para navegar por productos, categorías, usuarios y reportes.</p>
    <a href="product.php">Ver Inventario</a>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>

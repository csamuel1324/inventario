<?php
  $page_title = 'Filtrar Productos';
  require_once('includes/load.php');
  if (!$session->isUserLoggedIn(true)) { redirect('index.php', false); }

  $all_categories = find_all('categories');

  // Obtener filtros
  $nombre = isset($_GET['nombre']) ? remove_junk($db->escape($_GET['nombre'])) : '';
  $categoria = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;

  $sql  = "SELECT p.name, p.quantity, p.buy_price, c.name AS category FROM products p ";
  $sql .= "LEFT JOIN categories c ON p.categorie_id = c.id ";
  $sql .= "WHERE 1=1 ";

  if ($nombre != '') {
    $sql .= "AND p.name LIKE '%{$nombre}%' ";
  }
  if ($categoria > 0) {
    $sql .= "AND p.categorie_id = '{$categoria}' ";
  }

  $products = find_by_sql($sql);
?>

<?php include_once('layouts/header.php'); ?>

<div class="container-fluid">
  <div class="panel panel-default">
    <div class="panel-heading">
      <strong><span class="glyphicon glyphicon-filter"></span> Filtrar productos</strong>
    </div>
    <div class="panel-body">
      <form method="get" action="filtrar_productos.php" class="form-inline">
        <div class="form-group">
          <label for="nombre">Nombre:</label>
          <input type="text" name="nombre" value="<?php echo $nombre; ?>" class="form-control" placeholder="Buscar nombre">
        </div>
        <div class="form-group">
          <label for="categoria">Categoría:</label>
          <select name="categoria" class="form-control">
            <option value="0">Todas</option>
            <?php foreach ($all_categories as $cat): ?>
              <option value="<?php echo (int)$cat['id']; ?>" <?php if ($categoria == $cat['id']) echo 'selected'; ?>>
                <?php echo remove_junk($cat['name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Filtrar</button>
      </form>
    </div>
  </div>

  <?php if (!empty($products)): ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong><span class="glyphicon glyphicon-th-list"></span> Resultados</strong>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Categoría</th>
              <th>Cantidad</th>
              <th>Origen</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $prod): ?>
              <tr>
                <td><?php echo remove_junk($prod['name']); ?></td>
                <td><?php echo remove_junk($prod['category']); ?></td>
                <td><?php echo (int)$prod['quantity']; ?></td>
                <td><?php echo remove_junk($prod['buy_price']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-info">
      No se encontraron productos con los filtros seleccionados.
    </div>
  <?php endif; ?>
</div>

<?php include_once('layouts/footer.php'); ?>

<?php
  // add_dispatch.php
  $page_title = 'Agregar despacho';
  require_once('includes/load.php');
  page_require_level(3);

  // Traemos los productos para el select
  $products = find_all('products');

  if (isset($_POST['add_sale'])) {
    // Validamos campos obligatorios
    $req_fields = ['s_id','quantity','origin','destination','date'];
    validate_fields($req_fields);

    if (empty($errors)) {
      // Recogemos y escapamos datos
      $p_id        = (int) $_POST['s_id'];
      $s_qty       = (int) $_POST['quantity'];
      $origin      = $db->escape($_POST['origin']);
      $destination = $db->escape($_POST['destination']);
      $s_date      = $db->escape($_POST['date']);

      // Obtener datos del producto
$product = find_by_id('products', $p_id);

// Verificar si hay suficiente stock
if ($product['quantity'] < $s_qty) {
  $session->msg('d', 'No hay suficiente stock para despachar.');
  redirect('add_sale.php', false);
}


      // Insertamos en sales (ahora despacho)
      $sql  = "INSERT INTO sales (product_id, qty, origin, destination, date) ";
      $sql .= "VALUES ('{$p_id}','{$s_qty}','{$origin}','{$destination}','{$s_date}')";

      if ($db->query($sql)) {
        // Actualiza stock restando la cantidad despachada
        update_product_qty($s_qty, $p_id);
        // Verificar si quedó en 0
$new_qty = $product['quantity'] - $s_qty;
if ($new_qty <= 0) {
  delete_by_id('products', $p_id); // Esta función debería ya estar en tu sistema
}
        $session->msg('s',"Despacho registrado correctamente");
        redirect('add_sale.php', false);
      } else {
        $session->msg('d','Error al registrar el despacho.');
        redirect('add_sale.php', false);
      }
    } else {
      $session->msg('d', $errors);
      redirect('add_sale.php', false);
    }
  }
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
    <form method="post" action="add_sale.php">
      <div class="form-group">
        <label for="s_id">Producto</label>
        <select name="s_id" class="form-control" required>
          <option value="">Selecciona un producto</option>
          <?php foreach ($products as $product): ?>
            <option value="<?php echo $product['id']; ?>">
              <?php echo remove_junk($product['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="quantity">Cantidad</label>
        <input type="number" name="quantity" class="form-control" min="1" required>
      </div>

      <div class="form-group">
        <label for="origin">Origen del producto</label>
        <input type="text" name="origin" class="form-control" maxlength="255" required>
      </div>

      <div class="form-group">
        <label for="destination">Destino del despacho</label>
        <input type="text" name="destination" class="form-control" maxlength="255" required>
      </div>

      <div class="form-group">
        <label for="date">Fecha del despacho</label>
        <input type="date" name="date" class="form-control" required>
      </div>

      <button type="submit" name="add_sale" class="btn btn-primary">Registrar despacho</button>
    </form>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>

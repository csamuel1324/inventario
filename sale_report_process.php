<?php
$page_title = 'Reporte de despachos';
$results = '';
require_once('includes/load.php');
require_once('includes/sql.php');
page_require_level(3);

if (isset($_POST['submit'])) {
  $req_dates = array('start-date','end-date');
  validate_fields($req_dates);

  if (empty($errors)) {
    $start_date = remove_junk($db->escape($_POST['start-date']));
    $end_date   = remove_junk($db->escape($_POST['end-date']));
    $results    = find_sales_by_dates($start_date, $end_date);
  } else {
    $session->msg("d", $errors);
    redirect('sales_report.php', false);
  }
} else {
  $session->msg("d", "Selecciona las fechas");
  redirect('sales_report.php', false);
}
?>

<?php if ($results): ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Reporte de despachos</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
</head>
<body>
  <div class="container">
    <h2 class="text-center">Reporte de despachos</h2>
    <strong><?php echo $start_date; ?> a <?php echo $end_date; ?></strong>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Producto</th>
          <th>Origen</th>
          <th>Destino</th>
          <th>Cantidad</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($results as $result): ?>
          <tr>
            <td><?php echo remove_junk($result['date']); ?></td>
            <td><?php echo remove_junk($result['name']); ?></td>
            <td><?php echo remove_junk($result['origin']); ?></td>
            <td><?php echo remove_junk($result['destination']); ?></td>
            <td><?php echo remove_junk($result['qty']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <!-- Botón para descargar PDF -->
<form method="post" action="reporte_pdf.php" target="_blank" style="margin-top: 15px;">
  <input type="hidden" name="start-date" value="<?php echo $start_date; ?>">
  <input type="hidden" name="end-date" value="<?php echo $end_date; ?>">
  <button type="submit" name="submit" class="btn btn-success">Descargar PDF</button>
</form>

  </div>
</body>
</html>
<?php else: ?>
  <?php
    $session->msg("d", "No se encontraron despachos.");
    redirect('sales_report.php', false);
  ?>
<?php endif; ?>

<?php
require_once(__DIR__ . '/../includes/api.php');
api_handle_options_preflight();
api_require_auth(3);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  // Filtros opcionales por fecha
  $start = isset($_GET['start_date']) ? $_GET['start_date'] : null;
  $end = isset($_GET['end_date']) ? $_GET['end_date'] : null;
  if ($start && $end) {
    $rows = find_sales_by_dates($start, $end);
  } else {
    $rows = find_all_sale();
  }
  $out = [];
  foreach ($rows as $r) {
    $out[] = [
      'id' => (int)$r['id'],
      'product' => $r['name'] ?? null,
      'qty' => (int)$r['qty'],
      'total' => $r['price'] ?? null,
      'date' => $r['date'],
    ];
  }
  api_json($out, 200);
}

if ($method === 'POST') {
  // Registrar despacho (resta stock). Nivel 3 puede registrar.
  $b = api_read_json();
  $product_id = (int)($b['product_id'] ?? 0);
  $qty = (int)($b['qty'] ?? 0);
  $origin = trim((string)($b['origin'] ?? ''));
  $destination = trim((string)($b['destination'] ?? ''));
  $date = isset($b['date']) ? (string)$b['date'] : date('Y-m-d');
  if ($product_id <= 0 || $qty <= 0) api_error('Datos inválidos', 400);

  $product = find_by_id('products', $product_id);
  if (!$product) api_error('Producto no existe', 404);
  if ((int)$product['quantity'] < $qty) api_error('Stock insuficiente', 400);

  global $db;
  // No hay transacciones en wrapper, usamos directamente mysqli
  $db->query('START TRANSACTION');
  $ok = true;
  $p_id_e = $db->escape($product_id);
  $qty_e = $db->escape($qty);
  $ori_e = $db->escape($origin);
  $des_e = $db->escape($destination);
  $date_e = $db->escape($date);
  $ins = $db->query("INSERT INTO sales (product_id, qty, origin, destination, date) VALUES ('{$p_id_e}','{$qty_e}','{$ori_e}','{$des_e}','{$date_e}')");
  if (!$ins) $ok = false;
  if ($ok) {
    $upd = $db->query("UPDATE products SET quantity = quantity - '{$qty_e}' WHERE id = '{$p_id_e}'");
    if (!$upd) $ok = false;
  }
  if ($ok) {
    $db->query('COMMIT');
    api_json(['created' => true], 201);
  } else {
    $db->query('ROLLBACK');
    api_error('No se pudo registrar', 500);
  }
}

api_error('Método no permitido', 405);

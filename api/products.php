<?php
require_once(__DIR__ . '/../includes/api.php');
api_handle_options_preflight();
$claims = api_require_auth(3); // todos los usuarios autenticados

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  // List or single by id
  $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
  if ($id > 0) {
    $p = find_by_id('products', $id);
    if (!$p) api_error('No encontrado', 404);
    $out = [
      'id' => (int)$p['id'],
      'name' => $p['name'],
      'quantity' => (int)$p['quantity'],
      'origin' => $p['buy_price'],
      'destination' => $p['sale_price'],
      'categorie_id' => (int)$p['categorie_id'],
      'media_id' => (int)$p['media_id'],
      'date' => $p['date'],
    ];
    api_json($out, 200);
  }
  $rows = join_product_table();
  $list = [];
  foreach ($rows as $r) {
    $list[] = [
      'id' => (int)$r['id'],
      'name' => $r['name'],
      'quantity' => (int)$r['quantity'],
      'origin' => $r['buy_price'],
      'destination' => $r['sale_price'],
      'categorie' => $r['categorie'],
      'media_id' => (int)$r['media_id'],
      'image' => $r['image'],
      'date' => $r['date'],
    ];
  }
  api_json($list, 200);
}

if ($method === 'POST') {
  // Create product (nivel 2: special o admin)
  api_require_auth(2);
  $b = api_read_json();
  $name = trim((string)($b['name'] ?? ''));
  $categorie_id = (int)($b['categorie_id'] ?? 0);
  $quantity = (int)($b['quantity'] ?? 0);
  $origin = trim((string)($b['origin'] ?? ''));
  $destination = trim((string)($b['destination'] ?? ''));
  $media_id = isset($b['media_id']) ? (int)$b['media_id'] : 0;
  if ($name === '' || $categorie_id <= 0) api_error('Datos incompletos', 400);
  $date = make_date();
  global $db;
  $name_e = $db->escape($name);
  $cat_e = $db->escape($categorie_id);
  $qty_e = $db->escape($quantity);
  $ori_e = $db->escape($origin);
  $des_e = $db->escape($destination);
  $med_e = $db->escape($media_id);
  $dat_e = $db->escape($date);
  $sql  = "INSERT INTO products (name,quantity,buy_price,sale_price,categorie_id,media_id,date) VALUES ('{$name_e}','{$qty_e}','{$ori_e}','{$des_e}','{$cat_e}','{$med_e}','{$dat_e}')";
  if (!$db->query($sql)) api_error('No se pudo crear', 500);
  $id = $db->insert_id();
  api_json(['id' => (int)$id], 201);
}

if ($method === 'PUT') {
  // Update product (nivel 2)
  api_require_auth(2);
  $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
  if ($id <= 0) api_error('ID requerido', 400);
  $p = find_by_id('products', $id);
  if (!$p) api_error('No encontrado', 404);
  $b = api_read_json();
  global $db;
  $fields = [];
  if (isset($b['name'])) $fields[] = "name='" . $db->escape(trim((string)$b['name'])) . "'";
  if (isset($b['quantity'])) $fields[] = "quantity='" . $db->escape((int)$b['quantity']) . "'";
  if (isset($b['origin'])) $fields[] = "buy_price='" . $db->escape((string)$b['origin']) . "'";
  if (isset($b['destination'])) $fields[] = "sale_price='" . $db->escape((string)$b['destination']) . "'";
  if (isset($b['categorie_id'])) $fields[] = "categorie_id='" . $db->escape((int)$b['categorie_id']) . "'";
  if (isset($b['media_id'])) $fields[] = "media_id='" . $db->escape((int)$b['media_id']) . "'";
  if (!$fields) api_error('Nada que actualizar', 400);
  $sql = "UPDATE products SET " . implode(',', $fields) . " WHERE id='" . $db->escape($id) . "'";
  if (!$db->query($sql)) api_error('No se pudo actualizar', 500);
  api_json(['updated' => true], 200);
}

if ($method === 'DELETE') {
  // Delete product (nivel 2)
  api_require_auth(2);
  $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
  if ($id <= 0) api_error('ID requerido', 400);
  if (!delete_by_id('products', $id)) api_error('No se pudo eliminar', 500);
  api_json(['deleted' => true], 200);
}

api_error('Método no permitido', 405);

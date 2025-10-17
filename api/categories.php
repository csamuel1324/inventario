<?php
require_once(__DIR__ . '/../includes/api.php');
api_handle_options_preflight();
api_require_auth(3); // nivel 3 o superior (1=admin, 3=usuario)

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  api_error('Método no permitido', 405);
}

$cats = find_all('categories') ?: [];
$payload = [];
foreach ($cats as $c) {
  $payload[] = [
    'id' => (int)$c['id'],
    'name' => $c['name'],
  ];
}
api_json($payload, 200);

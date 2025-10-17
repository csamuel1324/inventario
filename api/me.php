<?php
require_once(__DIR__ . '/../includes/api.php');
api_handle_options_preflight();
$claims = api_require_auth(3);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  api_error('Método no permitido', 405);
}

$user = find_by_id('users', (int)$claims['sub']);
if (!$user) api_error('No encontrado', 404);

api_json([
  'id' => (int)$user['id'],
  'username' => $user['username'],
  'name' => $user['name'],
  'user_level' => (int)$user['user_level'],
  'image' => $user['image'],
  'last_login' => $user['last_login'],
], 200);

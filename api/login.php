<?php
require_once(__DIR__ . '/../includes/api.php');
api_handle_options_preflight();
api_method_require('POST');

$body = api_read_json();
$username = isset($body['username']) ? trim((string)$body['username']) : '';
$password = isset($body['password']) ? (string)$body['password'] : '';
if ($username === '' || $password === '') {
  api_error('username y password son requeridos', 400);
}

// Reutilizamos authenticate_v2 para obtener usuario (usa sha1 hoy; ideal migrar)
$user = authenticate_v2($username, $password);
if (!$user) {
  api_error('Credenciales inválidas', 401);
}

$token = api_issue_token($user);
// Info básica del usuario (no exponer hash)
$user_info = [
  'id' => (int)$user['id'],
  'username' => $user['username'],
  'user_level' => (int)$user['user_level'],
  'name' => $user['name'] ?? null,
];

api_json(['token' => $token['token'], 'expires_in' => $token['expires_in'], 'user' => $user_info], 200);

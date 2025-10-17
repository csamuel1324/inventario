<?php
require_once(__DIR__ . '/load.php');

// CORS helpers
function api_allow_cors(array $allow_methods = ['GET','POST','PUT','DELETE','OPTIONS']): void {
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Credentials: false');
  header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
  header('Access-Control-Allow-Methods: ' . implode(',', $allow_methods));
}

function api_handle_options_preflight(): void {
  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    api_allow_cors();
    http_response_code(204);
    exit;
  }
}

// JSON helpers
function api_read_json(): array {
  $raw = file_get_contents('php://input');
  if ($raw === false || $raw === '') {
    return [];
  }
  $data = json_decode($raw, true);
  if (json_last_error() !== JSON_ERROR_NONE) {
    api_error('JSON inválido: ' . json_last_error_msg(), 400);
  }
  return is_array($data) ? $data : [];
}

function api_json($data, int $status = 200): void {
  api_allow_cors();
  header('Content-Type: application/json; charset=utf-8');
  http_response_code($status);
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}

function api_error(string $message, int $status = 400, array $extra = []): void {
  $payload = array_merge(['error' => $message], $extra);
  api_json($payload, $status);
}

function api_method_require(string $method): void {
  if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== strtoupper($method)) {
    api_error('Método no permitido', 405, ['allowed' => $method]);
  }
}

// Authorization header helpers
function api_get_authorization_header(): ?string {
  $headers = null;
  if (isset($_SERVER['Authorization'])) {
    $headers = trim($_SERVER['Authorization']);
  } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
  } elseif (function_exists('apache_request_headers')) {
    $requestHeaders = apache_request_headers();
    if (isset($requestHeaders['Authorization'])) {
      $headers = trim($requestHeaders['Authorization']);
    }
  }
  return $headers ?: null;
}

function api_get_bearer_token(): ?string {
  $header = api_get_authorization_header();
  if (!$header) return null;
  if (stripos($header, 'Bearer ') === 0) {
    return substr($header, 7);
  }
  return null;
}

// Minimal JWT (HS256)
function base64url_encode(string $data): string {
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode(string $data): string {
  $remainder = strlen($data) % 4;
  if ($remainder) {
    $padlen = 4 - $remainder;
    $data .= str_repeat('=', $padlen);
  }
  return base64_decode(strtr($data, '-_', '+/'));
}

function jwt_sign(array $payload): string {
  $header = ['typ' => 'JWT', 'alg' => 'HS256'];
  $segments = [
    base64url_encode(json_encode($header)),
    base64url_encode(json_encode($payload))
  ];
  $signing_input = implode('.', $segments);
  $signature = hash_hmac('sha256', $signing_input, JWT_SECRET, true);
  $segments[] = base64url_encode($signature);
  return implode('.', $segments);
}

function jwt_verify(string $jwt): ?array {
  $parts = explode('.', $jwt);
  if (count($parts) !== 3) return null;
  [$h64, $p64, $s64] = $parts;
  $header = json_decode(base64url_decode($h64), true);
  $payload = json_decode(base64url_decode($p64), true);
  $sig = base64url_decode($s64);
  if (!$header || !$payload || !is_string($sig)) return null;
  if (($header['alg'] ?? '') !== 'HS256') return null;
  $signing_input = $h64 . '.' . $p64;
  $expected = hash_hmac('sha256', $signing_input, JWT_SECRET, true);
  if (!hash_equals($expected, $sig)) return null;
  $now = time();
  if (isset($payload['exp']) && $now >= (int)$payload['exp']) return null;
  if (isset($payload['nbf']) && $now < (int)$payload['nbf']) return null;
  if (isset($payload['iss']) && JWT_ISSUER && $payload['iss'] !== JWT_ISSUER) return null;
  return $payload;
}

function api_issue_token(array $user): array {
  $now = time();
  $ttl = (int)JWT_TTL_SECONDS;
  $payload = [
    'sub' => (int)$user['id'],
    'username' => $user['username'],
    'user_level' => (int)$user['user_level'],
    'iat' => $now,
    'nbf' => $now,
    'exp' => $now + $ttl,
  ];
  if (JWT_ISSUER) $payload['iss'] = JWT_ISSUER;
  $token = jwt_sign($payload);
  return [
    'token' => $token,
    'expires_in' => $ttl,
  ];
}

function api_require_auth(?int $min_level = null): array {
  $token = api_get_bearer_token();
  if (!$token) api_error('No autorizado', 401);
  $claims = jwt_verify($token);
  if (!$claims) api_error('Token inválido o expirado', 401);
  if ($min_level !== null && (int)$claims['user_level'] > $min_level) {
    api_error('Permisos insuficientes', 403);
  }
  return $claims;
}

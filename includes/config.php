<?php
/*
|--------------------------------------------------------------------------
| OWSA-INV V2
|--------------------------------------------------------------------------
| Author: Siamon Hasan
| Project Name: OSWA-INV
| Version: v2
| Offcial page: http://oswapp.com/
| facebook Page: https://www.facebook.com/oswapp
|
|
|
*/
  define( 'DB_HOST', 'localhost' );          // Set database host
  define( 'DB_USER', 'root' );             // Set database user
  define( 'DB_PASS', '' );             // Set database password
  define( 'DB_NAME', 'oswa_inv' );        // Set database name
// API/JWT configuration
defined('JWT_SECRET') or define('JWT_SECRET', getenv('JWT_SECRET') ?: 'change-this-secret-in-env');
defined('JWT_TTL_SECONDS') or define('JWT_TTL_SECONDS', getenv('JWT_TTL_SECONDS') ?: 3600);
defined('JWT_ISSUER') or define('JWT_ISSUER', getenv('JWT_ISSUER') ?: 'oswa-inv-api');

?>

<!--Purpose: Config file to define db credentials from the .env (environment) file-->
<?php
require_once __DIR__ . '/../lib/env.php';

loadenv(__DIR__ . '/../.env');

define('DB_HOST', getenv('DB_HOST'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASS'));
define('DB_NAME', getenv('DB_NAME'));
define('DB_PORT', getenv('DB_PORT'));
define('API_KEY', getenv('API_KEY'));


?>
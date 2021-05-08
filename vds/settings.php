<?php

define('DEV_MODE', true);
define('DS', DIRECTORY_SEPARATOR);
define('HTTP_PROTO', (isset($_SERVER["HTTPS"])?'https://':'http://'));
define('APP_BASE', __DIR__);
define('APP_BASE_URL', HTTP_PROTO .  str_replace($_SERVER['DOCUMENT_ROOT'], $_SERVER['SERVER_NAME'], APP_BASE));

define('CSS_DIR_URL', APP_BASE_URL . '/assets/css/');

// Cache settings
define('CACHE_DIR', APP_BASE . DS . 'cache' . DS);

define('USER', 'beer');
define('PASSWD', 'lqlqlq');

define('DROPBOX_APP_KEY', 'hkfi3gnae2uqq8j');
define('DROPBOX_APP_SECRET', '8x2rv27je2o5fek');

define('ITEMS_PER_PAGE', 15);
define('PAGINATION_DISPLAY_NEAREST', 3);

?>

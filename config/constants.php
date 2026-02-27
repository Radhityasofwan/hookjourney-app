<?php
// Konstanta Global Aplikasi Hookjourney

// Path absolut aplikasi. 
// Karena domain langsung mengarah ke folder app/, APP_PATH dan ROOT_PATH adalah sama.
define('APP_PATH', dirname(__DIR__)); 
define('ROOT_PATH', APP_PATH);

// Direktori Uploads 
// Disesuaikan agar langsung mengarah ke folder uploads/ di dalam root aplikasi
define('UPLOAD_DIR_BRANDS', APP_PATH . '/uploads/brands/');
define('UPLOAD_DIR_ADS', APP_PATH . '/uploads/ads_reports/');
define('UPLOAD_DIR_CREATIVE', APP_PATH . '/uploads/creative_assets/');
define('UPLOAD_DIR_FOOTAGE', APP_PATH . '/uploads/content_footage/');
define('UPLOAD_DIR_TICKETS', APP_PATH . '/uploads/tickets/');
define('UPLOAD_DIR_FORUMS', APP_PATH . '/uploads/forums/');

// Konstanta Roles (Sesuai dengan enum role_global di database)
define('ROLE_LEADER', 'leader');
define('ROLE_TEAM', 'team');
define('ROLE_CLIENT', 'client');
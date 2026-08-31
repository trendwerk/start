<?php

use Roots\WPConfig\Config;

use function Env\env;

// USE_ENV_ARRAY + CONVERT_* + STRIP_QUOTES
Env\Env::$options = 31;

$root_dir = __DIR__;
$webroot_dir = $root_dir . '/web';

// Define required environment variables and load .env file
if (file_exists($root_dir . '/.env')) {
  $env_files = file_exists($root_dir . '/.env.local')
    ? ['.env', '.env.local']
    : ['.env'];

  $repository = Dotenv\Repository\RepositoryBuilder::createWithNoAdapters()
    ->addAdapter(Dotenv\Repository\Adapter\EnvConstAdapter::class)
    ->addAdapter(Dotenv\Repository\Adapter\PutenvAdapter::class)
    ->immutable()
    ->make();

  $dotenv = Dotenv\Dotenv::create($repository, $root_dir, $env_files, false);
  $dotenv->load();

  $dotenv->required(['DB_NAME', 'DB_PASSWORD', 'DB_USER', 'WP_ENV', 'WP_HOME']);
}

// Define environment variable and load its config first
define('WP_ENV', env('WP_ENV') ?: 'production');

// WordPress
Config::define('AUTOMATIC_UPDATER_DISABLED', true);
Config::define('CONCATENATE_SCRIPTS', false);
Config::define('DISABLE_WP_CRON', env('DISABLE_WP_CRON') ?: false);
Config::define('DISALLOW_FILE_EDIT', true);
Config::define('DISALLOW_FILE_MODS', true);
Config::define('WP_ENVIRONMENT_TYPE', WP_ENV);
Config::define('WP_HOME', env('WP_HOME'));
Config::define('WP_POST_REVISIONS', env('WP_POST_REVISIONS') ?? true);
Config::define('WP_SITEURL', env('WP_HOME') . '/wp');

// Content directory
Config::define('CONTENT_DIR', '/wp-content');
Config::define('WP_CONTENT_DIR', $webroot_dir . Config::get('CONTENT_DIR'));
Config::define(
  'WP_CONTENT_URL',
  Config::get('WP_HOME') . Config::get('CONTENT_DIR')
);

// Database
Config::define('DB_HOST', env('DB_HOST') ?: 'localhost');
Config::define('DB_NAME', env('DB_NAME'));
Config::define('DB_USER', env('DB_USER'));
Config::define('DB_PASSWORD', env('DB_PASSWORD'));
Config::define('DB_CHARSET', 'utf8mb4');
Config::define('DB_COLLATE', '');
$table_prefix = env('DB_PREFIX') ?: 'wp_';

// Debugging
Config::define('WP_DEBUG', env('WP_DEBUG') ?? false);
Config::define('WP_DEBUG_DISPLAY', env('WP_DEBUG_DISPLAY') ?? false);
Config::define('WP_DEBUG_LOG', env('WP_DEBUG_LOG') ?? false);
Config::define('SCRIPT_DEBUG', env('SCRIPT_DEBUG') ?? false);
ini_set('display_errors', '0');

// Theme
Config::define('DEV_MAIL_TO', env('DEV_MAIL_TO'));
Config::define('PRODUCTION_URL', env('PRODUCTION_URL'));

// WP Mail SMTP
Config::define('WPMS_ON', env('WPMS_ON') ?? false);
Config::define('WPMS_MAIL_FROM', env('WPMS_MAIL_FROM'));
Config::define('WPMS_MAIL_FROM_NAME', env('WPMS_MAIL_FROM_NAME'));
Config::define('WPMS_MAILER', env('WPMS_MAILER') ?? 'sendgrid');
Config::define('WPMS_SENDGRID_API_KEY', env('WPMS_SENDGRID_API_KEY'));
Config::define('WPMS_SENDGRID_DOMAIN', env('WPMS_SENDGRID_DOMAIN'));

// Authentication
Config::define('AUTH_KEY', env('AUTH_KEY'));
Config::define('SECURE_AUTH_KEY', env('SECURE_AUTH_KEY'));
Config::define('LOGGED_IN_KEY', env('LOGGED_IN_KEY'));
Config::define('NONCE_KEY', env('NONCE_KEY'));
Config::define('AUTH_SALT', env('AUTH_SALT'));
Config::define('SECURE_AUTH_SALT', env('SECURE_AUTH_SALT'));
Config::define('LOGGED_IN_SALT', env('LOGGED_IN_SALT'));
Config::define('NONCE_SALT', env('NONCE_SALT'));

// Allow WordPress to detect HTTPS when used behind a reverse proxy or a load balancer
if (
  isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
  $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
) {
  $_SERVER['HTTPS'] = 'on';
}

Config::apply();

// Bootstrap WordPress
if (!defined('ABSPATH')) {
  define('ABSPATH', $webroot_dir . '/wp/');
}

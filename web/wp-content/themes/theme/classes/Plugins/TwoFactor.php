<?php

namespace Theme\Plugins;

use Two_Factor_Core;
use WP_User;

class TwoFactor
{
  private static string $requiredForCapability = 'edit_posts';

  public static function init(): void
  {
    // Bail if the Two Factor plugin is not active
    if (!class_exists('Two_Factor_Core')) {
      return;
    }

    // Remove email as a two-factor provider
    add_filter('two_factor_providers', [static::class, 'removeEmail']);

    // Require two-factor for users with the required capability
    add_action('admin_init', [static::class, 'requireTwoFactor']);

    // Skip two-factor on login for whitelisted IP addresses
    add_filter(
      'two_factor_primary_provider_for_user',
      [static::class, 'disableForWhitelistedIps'],
      10,
      2
    );
  }

  /**
   * Remove email as a two-factor provider.
   *
   * @param array $providers
   * @return array
   */
  public static function removeEmail($providers)
  {
    unset($providers['Two_Factor_Email']);

    return $providers;
  }

  /**
   * Send users with the required capability to their profile until they have
   * set up two-factor.
   */
  public static function requireTwoFactor()
  {
    if (!is_user_logged_in() || static::isIpWhitelisted()) {
      return;
    }

    $user = wp_get_current_user();

    // Contributors and above; subscribers are exempt
    if (
      !($user instanceof WP_User) ||
      !$user->exists() ||
      !$user->has_cap(static::$requiredForCapability)
    ) {
      return;
    }

    if (Two_Factor_Core::is_user_using_two_factor($user)) {
      return;
    }

    // Do not interrupt background or CLI tasks
    if (wp_doing_ajax() || wp_doing_cron() || (defined('WP_CLI') && WP_CLI)) {
      return;
    }

    global $pagenow;

    // Own profile, where the plugin renders its settings
    if ($pagenow === 'profile.php') {
      return;
    }

    if (
      $pagenow === 'user-edit.php' &&
      isset($_GET['user_id']) &&
      (int) $_GET['user_id'] === get_current_user_id()
    ) {
      return;
    }

    wp_safe_redirect(admin_url('profile.php#two-factor-options'));
    exit();
  }

  /**
   * Treat two-factor as inactive for whitelisted IP addresses while logging
   * in.
   *
   * @param string $provider
   * @param int $userId
   * @return string
   */
  public static function disableForWhitelistedIps($provider, $userId)
  {
    if (!static::isIpWhitelisted()) {
      return $provider;
    }

    // Only during authentication: session revalidation on wp-login.php needs
    // the real provider, otherwise users get "provider not available"
    $isAuthentication =
      doing_filter('authenticate') ||
      doing_action('wp_login') ||
      (defined('REST_REQUEST') && REST_REQUEST);

    if (!$isAuthentication) {
      return $provider;
    }

    return '';
  }

  /**
   * Check whether the request comes from a whitelisted IP address. Loopback
   * addresses count as whitelisted locally.
   */
  private static function isIpWhitelisted(): bool
  {
    $ip = static::clientIp();

    if ($ip === '') {
      return false;
    }

    if (in_array($ip, static::ipWhitelist(), true)) {
      return true;
    }

    return WP_ENV === 'development' &&
      in_array($ip, ['127.0.0.1', '::1'], true);
  }

  /**
   * Read the IP whitelist from TWOFACTOR_IP_WHITELIST, a comma-separated list.
   */
  private static function ipWhitelist(): array
  {
    if (!defined('TWOFACTOR_IP_WHITELIST') || !TWOFACTOR_IP_WHITELIST) {
      return [];
    }

    return array_values(
      array_filter(
        array_map('trim', explode(',', (string) TWOFACTOR_IP_WHITELIST))
      )
    );
  }

  /**
   * Get the client IP address, including common proxy and CDN headers.
   */
  private static function clientIp(): string
  {
    $headers = [
      'HTTP_CF_CONNECTING_IP',
      'HTTP_X_FORWARDED_FOR',
      'HTTP_X_FORWARDED',
      'REMOTE_ADDR',
    ];

    foreach ($headers as $header) {
      if (empty($_SERVER[$header])) {
        continue;
      }

      $value = $_SERVER[$header];

      if (str_contains($value, ',')) {
        $value = trim(explode(',', $value)[0]);
      }

      if (filter_var($value, FILTER_VALIDATE_IP)) {
        return $value;
      }
    }

    return '';
  }
}

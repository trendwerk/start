<?php

namespace Theme\Plugins;

use Two_Factor_Core;
use WP_User;

class TwoFactor
{
  public static function init(): void
  {
    // Require two-factor for administrators, except locally
    add_filter(
      'two_factor_enabled_providers_for_user',
      [static::class, 'enabledProviders'],
      10,
      2
    );
  }

  /**
   * Turn on emailed codes for administrators without a configured provider,
   * and turn off two-factor locally, where the database is a copy of
   * production.
   *
   * @param string[] $providers
   * @param int $userId
   * @return string[]
   */
  public static function enabledProviders($providers, $userId)
  {
    if (WP_ENV === 'development') {
      return [];
    }

    $user = get_userdata($userId);

    if (!($user instanceof WP_User) || !static::isAdministrator($user)) {
      return $providers;
    }

    if (!static::hasConfiguredProvider($providers, $user)) {
      $providers[] = 'Two_Factor_Email';
    }

    return $providers;
  }

  /**
   * Check whether the user is an administrator on this site or, on a
   * multisite, on any of the network's sites, since they share one login.
   */
  private static function isAdministrator(WP_User $user): bool
  {
    if ($user->has_cap('manage_options')) {
      return true;
    }

    if (!is_multisite()) {
      return false;
    }

    foreach (array_keys(get_blogs_of_user($user->ID)) as $siteId) {
      if (user_can_for_site($user, $siteId, 'manage_options')) {
        return true;
      }
    }

    return false;
  }

  /**
   * Check whether one of the enabled providers is set up for the user.
   */
  private static function hasConfiguredProvider(
    array $providers,
    WP_User $user
  ): bool {
    $supported = Two_Factor_Core::get_supported_providers_for_user($user);

    foreach ($providers as $provider) {
      if (
        isset($supported[$provider]) &&
        $supported[$provider]->is_available_for_user($user)
      ) {
        return true;
      }
    }

    return false;
  }
}

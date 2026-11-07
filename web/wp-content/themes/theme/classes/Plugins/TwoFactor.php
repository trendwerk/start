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

    if (!$user instanceof WP_User || !$user->has_cap('manage_options')) {
      return $providers;
    }

    if (!static::hasConfiguredProvider($providers, $user)) {
      $providers[] = 'Two_Factor_Email';
    }

    return $providers;
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

<?php

namespace Theme\Plugins;

use Two_Factor_Core;

class TwoFactor
{
  public static function init(): void
  {
    // Send users who can edit posts to their profile until they set up two-factor
    add_action('admin_init', [static::class, 'requireTwoFactor']);
  }

  /**
   * Redirect users who can edit posts to their profile until they have set up
   * two-factor.
   */
  public static function requireTwoFactor()
  {
    if (
      !class_exists('Two_Factor_Core') ||
      wp_doing_ajax() ||
      !current_user_can('edit_posts') ||
      Two_Factor_Core::is_user_using_two_factor(get_current_user_id())
    ) {
      return;
    }

    global $pagenow;

    if ($pagenow === 'profile.php') {
      return;
    }

    wp_safe_redirect(admin_url('profile.php#two-factor-options'));
    exit();
  }
}

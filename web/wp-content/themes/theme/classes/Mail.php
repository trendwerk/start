<?php

namespace Theme;

class Mail
{
  public static function init(): void
  {
    // Reroute emails to DEV_MAIL_TO outside production
    add_filter('wp_mail', [static::class, 'devMailTo']);
  }

  /**
   * Reroute emails to DEV_MAIL_TO if this is not the production environment.
   *
   * @param array $args
   * @return array
   */
  public static function devMailTo($args)
  {
    if (WP_ENV !== 'production' && defined('DEV_MAIL_TO') && DEV_MAIL_TO) {
      $args['to'] = DEV_MAIL_TO;
    }

    return $args;
  }
}

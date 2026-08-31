<?php

namespace Theme;

use WP_Error;

class Security
{
  public static function init()
  {
    // Disable XML-RPC
    add_filter('xmlrpc_enabled', '__return_false');
    add_filter('xmlrpc_methods', '__return_false');

    // Disable REST API
    add_filter('rest_authentication_errors', [static::class, 'restErrors']);

    // Hide users REST endpoints for visitors
    add_filter('rest_endpoints', [static::class, 'disableRestEndpoints']);

    // Hide WordPress version
    add_filter('the_generator', '__return_empty_string');
  }

  // Deny access to Rest API if user is not logged in
  static function restErrors($errors)
  {
    if (!is_user_logged_in()) {
      return new WP_Error('access_denied', 'Rest API is disabled', [
        'status' => rest_authorization_required_code(),
      ]);
    }

    return $errors;
  }

  static function disableRestEndpoints(array $endpoints)
  {
    if (!is_user_logged_in()) {
      if (isset($endpoints['/wp/v2/users'])) {
        unset($endpoints['/wp/v2/users']);
      }

      if (isset($endpoints['/wp/v2/users/(?P<id>[\d]+)'])) {
        unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
      }
    }

    return $endpoints;
  }
}

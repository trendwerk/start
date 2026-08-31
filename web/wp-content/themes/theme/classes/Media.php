<?php

namespace Theme;

use Ramsey\Uuid\Nonstandard\Uuid;

class Media
{
  public static function init(): void
  {
    // Set image sizes
    add_action('after_setup_theme', [static::class, 'imageSizes']);

    // Don't add links to images by default
    add_action('after_setup_theme', [static::class, 'imageDefaultLinkType']);

    // Get images from production if they don't exist locally
    add_filter('wp_get_attachment_image_src', [
      static::class,
      'productionImages',
    ]);

    // Redirect attachment pages to the attachment files
    add_filter('template_redirect', [static::class, 'redirect']);

    // Disable attachment canonical redirect links
    add_filter('redirect_canonical', [static::class, 'canonical'], 0, 1);

    // Disable attachment links
    add_filter('attachment_link', [static::class, 'disableLink'], 10, 2);

    // Randomize attachment slugs to avoid slug reservation
    add_filter('wp_unique_post_slug', [static::class, 'modifySlug'], 10, 4);
  }

  /**
   * Set image sizes when the stored option differs.
   */
  public static function imageSizes()
  {
    $sizes = [
      'thumbnail_size_w' => 180,
      'thumbnail_size_h' => '',
      'medium_size_w' => 360,
      'medium_size_h' => '',
      'large_size_w' => 720,
      'large_size_h' => '',
    ];

    foreach ($sizes as $option => $value) {
      if (get_option($option) != $value) {
        update_option($option, $value);
      }
    }
  }

  /**
   * Don't add links to images by default.
   */
  public static function imageDefaultLinkType()
  {
    if (get_option('image_default_link_type') !== 'none') {
      update_option('image_default_link_type', 'none');
    }
  }

  /**
   * Get images from production if they don't exist locally.
   *
   * @param array|false $src
   * @return array|false
   */
  public static function productionImages($src)
  {
    // Bail if this is the production environment
    if (WP_ENV == 'production') {
      return $src;
    }

    // Bail if PRODUCTION_URL is not defined
    if (!defined('PRODUCTION_URL')) {
      return $src;
    }

    // Bail if this is not an array
    if (!is_array($src)) {
      return $src;
    }

    // Get file path
    $uploadDir = wp_upload_dir();
    $file = str_replace($uploadDir['baseurl'], $uploadDir['basedir'], $src[0]);

    // Replace the image URL if it doesn't exist locally
    if (!file_exists($file)) {
      $home = home_url();
      $production = PRODUCTION_URL;

      $src[0] = str_replace(
        "{$home}/wp-content/uploads/",
        "{$production}/wp-content/uploads/",
        $src[0]
      );
    }

    return $src;
  }

  /**
   * Redirect attachment pages to the attachment files.
   */
  public static function redirect()
  {
    if (is_attachment()) {
      wp_redirect(wp_get_attachment_url(), 301);
      die();
    }
  }

  /**
   * Disable attachment canonical redirect links.
   *
   * @param string $url
   * @return string
   */
  public static function canonical($url)
  {
    static::redirect();

    return $url;
  }

  /**
   * Disable attachment links.
   *
   * @param string $url
   * @param int $id
   * @return string
   */
  public static function disableLink($url, $id)
  {
    if ($attachmentUrl = wp_get_attachment_url($id)) {
      return $attachmentUrl;
    }

    return $url;
  }

  /**
   * Randomize attachment slugs using UUIDs to avoid slug reservation.
   *
   * @param string $slug
   * @param string $id
   * @param string $status
   * @param string $type
   * @return string
   */
  public static function modifySlug($slug, $id, $status, $type)
  {
    if ($type !== 'attachment') {
      return $slug;
    }

    if (
      preg_match(
        '/^[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}$/iD',
        $slug
      ) > 0
    ) {
      return $slug;
    }

    return (string) Uuid::uuid4();
  }
}

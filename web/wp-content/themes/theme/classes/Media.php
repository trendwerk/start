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

    // Get attachment URLs from production if they don't exist locally
    add_filter('wp_get_attachment_url', [static::class, 'productionImages']);

    // Get srcset images from production if they don't exist locally
    add_filter('wp_calculate_image_srcset', [
      static::class,
      'productionImageSrcset',
    ]);

    // Get content images from production if they don't exist locally
    add_filter('the_content', [static::class, 'productionContentImages'], 20);

    // Get editor content images from production if they don't exist locally
    add_filter('the_editor_content', [
      static::class,
      'productionContentImages',
    ]);

    // Get pre-edit content images from production if they don't exist locally
    add_filter('content_edit_pre', [static::class, 'productionContentImages']);

    // Get ACF content images from production if they don't exist locally
    add_filter('acf_the_content', [static::class, 'productionContentImages']);

    // Rewrite upload URLs in REST responses used by the block editor
    add_action('rest_api_init', [static::class, 'registerRestFilters']);

    // Rewrite media-library attachment data for the editor
    add_filter('wp_prepare_attachment_for_js', [
      static::class,
      'productionContentImages',
    ]);

    // Keep production URLs out of the database
    add_filter('content_save_pre', [static::class, 'localContentImages']);

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
   * @param array|false|string $src
   * @return array|false|string
   */
  public static function productionImages($src)
  {
    if (
      WP_ENV === 'production' ||
      !defined('PRODUCTION_URL') ||
      !PRODUCTION_URL
    ) {
      return $src;
    }

    $isArray = is_array($src);
    $url = $isArray ? $src[0] ?? '' : $src;

    if (!is_string($url) || $url === '') {
      return $src;
    }

    $uploadDir = wp_upload_dir();
    $file = str_replace($uploadDir['baseurl'], $uploadDir['basedir'], $url);

    if (!file_exists($file)) {
      $url = str_replace(
        $uploadDir['baseurl'],
        rtrim(PRODUCTION_URL, '/') .
          wp_parse_url($uploadDir['baseurl'], PHP_URL_PATH),
        $url
      );
    }

    if ($isArray) {
      $src[0] = $url;

      return $src;
    }

    return $url;
  }

  /**
   * Get srcset images from production if they don't exist locally.
   *
   * @param array $sources
   * @return array
   */
  public static function productionImageSrcset($sources)
  {
    if (!is_array($sources)) {
      return $sources;
    }

    foreach ($sources as &$source) {
      if (!empty($source['url'])) {
        $source['url'] = static::productionImages($source['url']);
      }
    }

    return $sources;
  }

  /**
   * Get content images from production if they don't exist locally.
   *
   * @param mixed $content
   * @return mixed
   */
  public static function productionContentImages($content)
  {
    if (is_array($content)) {
      return array_map([static::class, 'productionContentImages'], $content);
    }

    if (!is_string($content) || $content === '') {
      return $content;
    }

    $baseUrl = wp_upload_dir()['baseurl'];

    return preg_replace_callback(
      '#' . preg_quote($baseUrl, '#') . '/[^"\'\s<>]+#',
      function ($match) {
        return static::productionImages($match[0]);
      },
      $content
    );
  }

  /**
   * Rewrite upload URLs in REST responses used by the block editor.
   */
  public static function registerRestFilters()
  {
    foreach (get_post_types(['show_in_rest' => true], 'names') as $type) {
      // Rewrite upload URLs in REST responses for this post type
      add_filter("rest_prepare_{$type}", [
        static::class,
        'productionRestResponse',
      ]);
    }
  }

  /**
   * @param \WP_REST_Response $response
   * @return \WP_REST_Response
   */
  public static function productionRestResponse($response)
  {
    $response->set_data(static::productionContentImages($response->get_data()));

    return $response;
  }

  /**
   * Convert production upload URLs back to local URLs before saving.
   *
   * @param string $content
   * @return string
   */
  public static function localContentImages($content)
  {
    if (
      !is_string($content) ||
      $content === '' ||
      WP_ENV === 'production' ||
      !defined('PRODUCTION_URL') ||
      !PRODUCTION_URL
    ) {
      return $content;
    }

    $uploadDir = wp_upload_dir();
    $productionBase =
      rtrim(PRODUCTION_URL, '/') .
      wp_parse_url($uploadDir['baseurl'], PHP_URL_PATH);

    return str_replace($productionBase, $uploadDir['baseurl'], $content);
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

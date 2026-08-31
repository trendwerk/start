<?php

namespace Theme;

use InvalidArgumentException;
use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\ServerFactory;

class Image
{
  private int $id;
  private string $url;
  private string $fit;
  private array $widths;

  public int $height;
  public int $width;
  public string $alt;
  public string $class;
  public string $loading;
  public string $size;
  public string $sizes;
  public string $src;
  public string $srcset;

  public static function init(): void
  {
    // Handle image resizing with Glide
    add_action('after_setup_theme', [static::class, 'handleResize']);
  }

  /**
   * Handle image resizing with Glide.
   */
  public static function handleResize()
  {
    $uri = strtok($_SERVER['REQUEST_URI'], '?');

    // Get and escape URL parameters
    $params = array_map(function ($param) {
      return esc_attr($param);
    }, $_GET);

    // Bail if this is not an image request or if 'url' or 'w' parameter is not present
    if (
      !str_starts_with($uri, '/img') ||
      !isset($params['url']) ||
      !isset($params['w'])
    ) {
      return;
    }

    // Return the resized image or 404 error page if image is not found
    try {
      $server = ServerFactory::create([
        'source' => 'wp-content/uploads',
        'cache' => 'wp-content/uploads/glide-cache',
        'max_image_size' => 2400 * 2400,
      ]);
      $server->outputImage($params['url'], $params);
    } catch (FileNotFoundException $exception) {
      status_header(404);
      include get_query_template('404');
    }

    die();
  }

  public function __construct(array $args)
  {
    // Throw error if src, width or height argument is not set
    if (
      !isset($args['src']) ||
      !isset($args['width']) ||
      !isset($args['height'])
    ) {
      throw new InvalidArgumentException('Missing required "src" argument.');
    }

    // Set URL and alt attributes if an image ID is passed to $args['src']
    if (
      is_int($args['src']) ||
      !filter_var($args['src'], FILTER_VALIDATE_URL)
    ) {
      $this->id = $args['src'];
      $this->url = wp_get_attachment_image_url($args['src'], 'full');

      $alt = get_post_meta($this->id, '_wp_attachment_image_alt', true);
      $this->alt = $args['alt'] ?? $alt;
    } else {
      $this->url = $args['src'];
      $this->alt = $args['alt'] ?? '';
    }

    // Set image properties
    $this->class = $args['class'] ?? '';
    $this->fit = $args['fit'] ?? 'crop';
    $this->height = $args['height'];
    $this->loading = $args['loading'] ?? 'lazy';
    $this->sizes = $args['sizes'] ?? 'auto';
    $this->width = $args['width'];
    $this->widths = $args['widths'] ?? [];

    // Set src and srcset properties after we've set the other properties
    $this->src = $this->src();
    $this->srcset = $this->srcset();
  }

  /**
   * Build the resized image src.
   */
  private function src(): string
  {
    return $this->resize([
      'h' => $this->height,
      'w' => $this->width,
    ]);
  }

  /**
   * Build the srcset of resized image widths, including retina sizes.
   */
  private function srcset(): string
  {
    // Add image width
    $widths = array_merge($this->widths, [$this->width]);

    // Add retina image sizes
    $widths = array_merge(
      $widths,
      array_map(function ($number) {
        return $number * 2;
      }, $widths)
    );

    // Remove duplicate widths and sort
    $widths = array_unique($widths);
    sort($widths);

    // Map widths to array with resized images and widths
    $images = array_map(function ($width) {
      $height = isset($this->height)
        ? intval(round(($width / $this->width) * $this->height))
        : null;

      $src = $this->resize([
        'h' => $height,
        'w' => $width,
      ]);

      return "{$src} {$width}w";
    }, $widths);

    return implode(', ', $images);
  }

  /**
   * Add resize parameters for Glide to the image src.
   */
  private function resize(array $params): string
  {
    // Replace image URL so it is handled by Glide
    $src = str_replace('/wp-content/uploads', '/img?url=', $this->url);

    // Add fit and format property
    $params = array_merge($params, [
      'fit' => $this->fit,
      'fm' => 'webp',
    ]);

    // Return image URL with Glide parameters
    $query = http_build_query($params);
    return "$src&$query";
  }
}

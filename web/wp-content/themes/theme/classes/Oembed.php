<?php

namespace Theme;

class Oembed
{
  public static function init(): void
  {
    // Replace YouTube embeds with a lazy-loaded thumbnail
    add_filter('embed_oembed_html', [static::class, 'lazyLoadYoutube']);

    // Replace ACF oEmbed YouTube fields with a lazy-loaded thumbnail
    add_filter(
      'acf/format_value/type=oembed',
      [static::class, 'lazyLoadYoutube'],
      11,
      3
    );
  }

  /**
   * Replace YouTube embeds with a thumbnail and custom play button to prevent loading YouTube scripts and cookies when the page loads.
   *
   * @param string $html
   * @return string
   */
  public static function lazyLoadYoutube($html)
  {
    // Get YouTube ID from the URL
    preg_match(
      '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i',
      $html,
      $matches
    );
    $youtubeId = $matches[1] ?? null;

    // Return the embed if this is the admin or if no YouTube ID was found
    if (is_admin() || !$youtubeId) {
      return $html;
    }

    // Return video component
    return component('youtube', [
      'youtubeId' => $youtubeId,
    ]);
  }
}

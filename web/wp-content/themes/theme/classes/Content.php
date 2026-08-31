<?php

namespace Theme;

class Content
{
  public static function init(): void
  {
    // Remove empty paragraphs from the content
    add_filter('the_content', [static::class, 'removeEmptyParagraphs']);
  }

  /**
   * Remove empty paragraphs from the content.
   *
   * @param string $content
   * @return string
   */
  public static function removeEmptyParagraphs($content)
  {
    $content = str_replace('<p></p>', '', $content);

    return $content;
  }
}

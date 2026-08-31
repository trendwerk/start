<?php

namespace Theme;

class Assets
{
  public static string $scriptPath = 'web/wp-content/themes/theme/scripts/theme.js';
  public static string $fontUrl = 'https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap';

  public static function init(): void
  {
    // Preconnect to Google Fonts
    add_action('wp_head', [static::class, 'preconnect']);

    // Enqueue scripts and styles
    add_action('wp_enqueue_scripts', [static::class, 'enqueue'], 10);

    // Load theme scripts as deferred modules
    add_filter('script_loader_tag', [static::class, 'scriptLoader'], 10, 3);

    // Enqueue block editor styles
    add_action('after_setup_theme', [static::class, 'editorStyles']);

    // Enqueue the block editor customization script
    add_action('enqueue_block_editor_assets', [static::class, 'editorScripts']);

    // Remove the wp-core-ui body class from the block editor
    add_action('admin_footer', [static::class, 'removeBodyClass']);
  }

  /**
   * Preconnect to Google Fonts.
   */
  public static function preconnect()
  {
    ?>
    <link
      href='https://fonts.googleapis.com'
      rel='preconnect'
    >
    <link
      crossorigin
      href='https://fonts.gstatic.com'
      rel='preconnect'
    >
    <?php
  }

  /**
   * Enqueue scripts and styles.
   */
  public static function enqueue()
  {
    // Enqueue custom font
    wp_enqueue_style('font', static::$fontUrl);

    // Enqueue scripts and styles from the Vite development server or compiled files
    if (is_array(wp_remote_get('http://localhost:5173/'))) {
      wp_enqueue_script('vite', 'http://localhost:5173/@vite/client');
      wp_enqueue_script(
        'theme',
        'http://localhost:5173/' . static::$scriptPath
      );
    } else {
      wp_enqueue_script('theme', get_theme_file_uri(static::compiled('js')));
      wp_enqueue_style('theme', get_theme_file_uri(static::compiled('css')));
    }

    // Pass PHP variables to JavaScript
    wp_localize_script('theme', 'theme', [
      'ajaxUrl' => admin_url('admin-ajax.php'),
    ]);
  }

  /**
   * Load theme scripts as deferred modules.
   *
   * @param string $tag
   * @param string $handle
   * @param string $src
   * @return string
   */
  public static function scriptLoader($tag, $handle, $src)
  {
    if (in_array($handle, ['theme', 'vite'])) {
      return '<script type="module" src="' .
        esc_url($src) .
        '" defer></script>';
    }

    return $tag;
  }

  /**
   * Enqueue block editor styles.
   */
  public static function editorStyles()
  {
    add_theme_support('editor-styles');

    add_editor_style(static::$fontUrl);
    add_editor_style(static::compiled('css'));
  }

  /**
   * Enqueue the block editor customization script.
   */
  public static function editorScripts()
  {
    wp_enqueue_script('theme-editor', get_theme_file_uri('scripts/editor.js'), [
      'wp-blocks',
      'wp-dom-ready',
      'wp-edit-post',
    ]);
  }

  /**
   * Remove the wp-core-ui body class from the block editor to prevent CSS conflicts with WordPress button styles.
   */
  public static function removeBodyClass()
  {
    if (get_current_screen()->is_block_editor) { ?>
      <script>
        const body = document.querySelector('body');
        body.classList.remove('wp-core-ui');
      </script>
    <?php }
  }

  /**
   * Get the URL of compiled CSS or JS.
   */
  private static function compiled(string $type = 'css'): string
  {
    $manifestPath = get_theme_file_path('dist/.vite/manifest.json');
    $manifest = json_decode(file_get_contents($manifestPath), true);
    $data = $manifest[static::$scriptPath];

    $urls = [
      'js' => "dist/{$data['file']}",
      'css' => "dist/{$data['css'][0]}",
    ];

    return $urls[$type];
  }
}

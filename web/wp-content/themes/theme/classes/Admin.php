<?php

namespace Theme;

use WP_Admin_Bar;

final class Admin
{
  public static function init(): void
  {
    // Add and remove admin menu items
    add_action('admin_menu', [static::class, 'adminMenu']);

    // Enable custom admin menu order
    add_filter('custom_menu_order', '__return_true');

    // Reorder admin menu items
    add_action('menu_order', [static::class, 'menuOrder']);

    // Remove admin bar items
    add_action('admin_bar_menu', [static::class, 'adminBar']);

    // Hide the WordPress update notice
    add_action('admin_head', [static::class, 'adminNotices']);

    // Remove admin dashboard widgets
    add_action('wp_dashboard_setup', [static::class, 'dashboardWidgets']);

    // Add public custom post types to the At a Glance widget
    add_filter('dashboard_glance_items', [static::class, 'setGlanceItems']);

    // Remove unused columns from admin posts lists
    add_filter('manage_posts_columns', [static::class, 'managePostsColumns']);
  }

  /**
   * Add and remove admin menu items.
   */
  public static function adminMenu()
  {
    global $menu;

    // Add extra menu separator
    $menu[] = ['', 'read', 'separator3', '', 'wp-menu-separator'];

    // Remove menu items
    remove_menu_page('edit-comments.php');
  }

  /**
   * Reorder admin menu items and put any items not in this list at the end.
   *
   * @param array $order
   * @return array
   */
  public static function menuOrder($order)
  {
    $newOrder = [
      'index.php', // Dashboard

      'separator1',

      'edit.php?post_type=page', // Pages
      'edit.php', // Posts

      'separator2',

      'general-content', // General content
      'upload.php', // Media
      'edit-comments.php', // Comments

      'separator3',

      'themes.php', // Appearance
      'plugins.php', // Plugins
      'users.php', // Users
      'profile.php', // Profile
      'tools.php', // Tools
      'options-general.php', // Settings

      'separator-last',

      'edit.php?post_type=acf-field-group', // ACF
      'varnish-page', // Proxy cache
      'rank-math', // Rank Math SEO
      'wp-mail-smtp', // WP Mail SMTP
    ];

    return array_merge($newOrder, array_diff($order, $newOrder));
  }

  /**
   * Remove admin bar items.
   *
   * @param WP_Admin_Bar $adminBar
   */
  public static function adminBar($adminBar)
  {
    $adminBar->remove_menu('comments');
  }

  /**
   * Hide the WordPress update notice.
   */
  public static function adminNotices()
  {
    remove_action('admin_notices', 'update_nag', 3);
  }

  /**
   * Remove admin dashboard widgets.
   */
  public static function dashboardWidgets()
  {
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal'); // Site Health Status
    remove_meta_box('dashboard_primary', 'dashboard', 'side'); // WordPress Events and News
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side'); // Quick Draft

    remove_meta_box('rank_math_dashboard_widget', 'dashboard', 'normal'); // Rank Math Overview
    remove_meta_box('wp_mail_smtp_reports_widget_lite', 'dashboard', 'normal'); // WP Mail SMTP
  }

  /**
   * Add public custom post types to the At a Glance dashboard widget.
   *
   * @param array $items
   * @return array
   */
  public static function setGlanceItems($items = [])
  {
    $postTypes = get_post_types(
      [
        'public' => true,
        '_builtin' => false,
      ],
      'objects'
    );

    foreach ($postTypes as $postType) {
      $pluralLabel = strtolower($postType->labels->name);
      $count = wp_count_posts($postType->name);
      $count = intval($count->publish);
      $count = number_format_i18n($count);

      if (current_user_can($postType->cap->edit_posts)) {
        $items[] = "<div class='post-count'><a href='edit.php?post_type={$postType->name}'>$count $pluralLabel</a></div>";
      } else {
        $items[] = "<div class='post-count'>$count $pluralLabel</div>";
      }
    }

    return $items;
  }

  /**
   * Remove unused columns from admin posts lists.
   *
   * @param array $columns
   * @return array
   */
  public static function managePostsColumns($columns)
  {
    unset($columns['author']);
    unset($columns['comments']);
    unset($columns['date']);

    return $columns;
  }
}

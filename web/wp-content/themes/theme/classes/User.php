<?php

namespace Theme;

use WP_User;

class User
{
  private int $id;
  private WP_User|false $wpUser;

  public static function init(): void
  {
    // Modify editor capabilities
    add_action('after_setup_theme', [static::class, 'modifyCapabilities']);

    // Register user fields
    add_action('acf/init', [static::class, 'registerFields']);

    // Remove the Gravatar image from the profile page
    add_action('admin_head', [static::class, 'removeGravatarFromProfile']);
  }

  /**
   * Modify editor capabilities.
   */
  public static function modifyCapabilities()
  {
    $editor = get_role('editor');

    // Allow editors to edit theme options like menus
    if ($editor && !$editor->has_cap('edit_theme_options')) {
      $editor->add_cap('edit_theme_options');
    }
  }

  /**
   * Register user fields.
   */
  public static function registerFields()
  {
    $key = 'user';

    acf_add_local_field_group([
      'key' => $key,
      'title' => __('User', 'theme') . ' ' . __('settings', 'theme'),
      'fields' => [
        [
          'key' => "field_{$key}_photo",
          'name' => 'photo',
          'label' => __('Photo', 'theme'),
          'type' => 'image',
          'return_format' => 'id',
          'required' => false,
          'min_width' => 120 * 2,
          'min_height' => 120 * 2,
        ],
      ],
      'instruction_placement' => 'field',
      'location' => [
        [
          [
            'param' => 'user_form',
            'operator' => '==',
            'value' => 'all',
          ],
        ],
      ],
    ]);
  }

  /**
   * Remove the Gravatar image from the profile page.
   */
  public static function removeGravatarFromProfile()
  {
    echo '<style>
      .user-profile-picture { display: none !important; }
    </style>';
  }

  public function __construct(int $id)
  {
    $this->id = $id;
    $this->wpUser = get_user_by('id', $id);
  }

  /**
   * Get the user ID.
   */
  public function id(): int
  {
    return $this->id;
  }

  /**
   * Get the user display name.
   */
  public function name(): string
  {
    return $this->wpUser->display_name;
  }

  /**
   * Get the author archive URL.
   */
  public function link(): string
  {
    return get_author_posts_url($this->id);
  }

  /**
   * Get the user photo attachment ID or Gravatar URL.
   */
  public function avatar(int $size = 60): int|string|null
  {
    if ($photo = get_field('photo', 'user_' . $this->id)) {
      return $photo;
    }

    $avatar = get_avatar_data($this->wpUser->user_email, ['size' => $size]);
    return $avatar['url'] ?? null;
  }
}

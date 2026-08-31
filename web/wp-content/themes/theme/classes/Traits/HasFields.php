<?php

namespace Theme\Traits;

use Theme\GeneralContent;

trait HasFields
{
  /**
   * Header image fields for a field group.
   */
  protected static function headerFields(string $key): array
  {
    return [
      [
        'key' => "field_{$key}_tab_header",
        'name' => 'tab_header',
        'label' => __('Header', 'theme'),
        'type' => 'tab',
        'placement' => 'left',
      ],
      [
        'key' => "field_{$key}_thumbnail",
        'name' => '_thumbnail_id',
        'label' => __('Image', 'theme'),
        'type' => 'image',
        'return_format' => 'id',
        'min_width' => 1200,
        'min_height' => 900,
      ],
    ];
  }

  /**
   * Call to action fields for a field group.
   */
  protected static function ctaFields(
    string $key,
    bool $isOptionsPage = false
  ): array {
    return array_merge(
      [
        [
          'key' => "field_{$key}_tab_cta",
          'name' => 'tab_cta',
          'label' => __('Call to action', 'theme'),
          'type' => 'tab',
          'placement' => 'left',
        ],
        !$isOptionsPage
          ? [
            'key' => "field_{$key}_show_cta",
            'name' => 'show_cta',
            'label' => __('Show component', 'theme'),
            'message' => __(
              'Show "Call to action" component on this page',
              'theme'
            ),
            'type' => 'true_false',
            'default_value' => true,
          ]
          : null,
        [
          'key' => "field_{$key}_cta_title",
          'name' => 'cta_title',
          'label' => __('Title', 'theme'),
          'instructions' => !$isOptionsPage
            ? static::defaultInstructions()
            : null,
          'type' => 'text',
          'required' => $isOptionsPage,
          'conditional_logic' => [
            [
              [
                'field' => "field_{$key}_show_cta",
                'operator' => '==',
                'value' => '1',
              ],
            ],
          ],
        ],
        [
          'key' => "field_{$key}_cta_text",
          'name' => 'cta_text',
          'label' => __('Text', 'theme'),
          'instructions' => !$isOptionsPage
            ? static::defaultInstructions()
            : null,
          'type' => 'wysiwyg',
          'media_upload' => false,
          'tabs' => 'visual',
          'toolbar' => 'basic',
          'conditional_logic' => [
            [
              [
                'field' => "field_{$key}_show_cta",
                'operator' => '==',
                'value' => '1',
              ],
            ],
          ],
        ],
      ],
      static::buttonsFields($key, 'cta', [
        'instructions' => !$isOptionsPage
          ? static::defaultInstructions()
          : null,
        'conditional_logic' => [
          [
            [
              'field' => "field_{$key}_show_cta",
              'operator' => '==',
              'value' => '1',
            ],
          ],
        ],
      ])
    );
  }

  /**
   * Repeater fields for a set of buttons.
   */
  protected static function buttonsFields(
    string $key,
    string $prefix,
    array $args = []
  ): array {
    return [
      array_merge(
        [
          'key' => "field_{$key}_{$prefix}_buttons",
          'name' => "{$prefix}_buttons",
          'label' => __('Buttons', 'theme'),
          'type' => 'repeater',
          'layout' => 'column',
          'button_label' => __('+ Add button', 'theme'),
          'sub_fields' => [
            [
              'key' => "field_{$key}_{$prefix}_button_link",
              'name' => 'link',
              'label' => __('Button', 'theme'),
              'type' => 'link',
              'required' => true,
            ],
            [
              'key' => "field_{$key}_{$prefix}_button_style",
              'name' => 'style',
              'label' => __('Style', 'theme'),
              'type' => 'select',
              'choices' => [
                'blue' => __('Blue', 'theme'),
                'blue-outline' => __('Blue outline', 'theme'),
                'gray' => __('Gray', 'theme'),
                'gray-outline' => __('Gray outline', 'theme'),
              ],
              'default_value' => 'blue',
              'required' => true,
            ],
          ],
        ],
        $args
      ),
    ];
  }

  /**
   * Instructions that point to the default value in general content settings.
   */
  private static function defaultInstructions(): string
  {
    $title = GeneralContent::$title;
    $adminUrl = esc_url(admin_url('admin.php?page=' . GeneralContent::$name));

    return sprintf(
      __('Overwrites the default value in %s settings.', 'theme'),
      "<a href='{$adminUrl}' target='_blank'>{$title}</a>"
    );
  }
}

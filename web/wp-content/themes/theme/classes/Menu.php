<?php

namespace Theme;

class Menu
{
  private mixed $menu;
  private static string $currentUrl = '';

  public static function init(): void
  {
    // Register menus
    add_action('after_setup_theme', [static::class, 'register']);

    // Set the current URL so current and parent menu items can be determined
    add_action('wp', [static::class, 'setCurrentUrl']);
  }

  /**
   * Register menus.
   */
  public static function register()
  {
    register_nav_menu('main', __('Main menu', 'theme'));
    register_nav_menu('footer', __('Footer menu', 'theme'));
  }

  /**
   * Set the current URL so current and parent menu pages can be determined.
   */
  public static function setCurrentUrl()
  {
    global $wp;
    $request = $wp->request ?? '';
    static::$currentUrl = trailingslashit(home_url($request));
  }

  public function __construct(?string $location = null)
  {
    // Bail if location is not provided
    if (!$location) {
      $this->menu = null;
      return;
    }

    // Get all menu locations and check if the location exists
    $locations = get_nav_menu_locations();

    if (!in_array($location, array_keys($locations))) {
      $this->menu = null;
      return;
    }

    // Get menu object by location
    $this->menu = wp_get_nav_menu_object($locations[$location]);
  }

  /**
   * Get the menu title.
   */
  public function name(): string
  {
    return $this->menu ? $this->menu->name : '';
  }

  /**
   * Get nested menu items.
   */
  public function items(): array
  {
    if (!$this->menu) {
      return [];
    }

    $menuIdentifier = $this->menu->term_id ?? ($this->menu->name ?? null);

    if (!$menuIdentifier) {
      return [];
    }

    $items = wp_get_nav_menu_items($menuIdentifier);

    // Return empty array if menu is empty
    if (!$items || count($items) == 0) {
      return [];
    }

    $items = $this->addIsCurrent($items);
    $items = $this->buildTree($items, 0);
    $items = $this->addIsParent($items);

    return $items ?: [];
  }

  /**
   * Build a nested array from a flat menu items array.
   */
  private function buildTree(array &$items, int $parentId = 0): array
  {
    $output = [];

    foreach ($items as &$item) {
      if ($item->menu_item_parent == $parentId) {
        $children = $this->buildTree($items, $item->ID);

        // Ensure children always exists to avoid undefined property notices
        $item->children = $children ?: [];

        $output[$item->ID] = $item;
        unset($item);
      }
    }
    return $output;
  }

  /**
   * Add isCurrent to each menu item.
   */
  private function addIsCurrent(array $items): array
  {
    $items = array_map(function ($item) {
      $item->isCurrent = $item->url === static::$currentUrl;

      return $item;
    }, $items);

    return $items;
  }

  /**
   * Add isParent to parent menu items.
   */
  private function addIsParent(array $items): array
  {
    if (!$items) {
      return [];
    }

    return array_map(function ($item) {
      $urls = [];
      $children = $item->children ?? [];

      if ($children) {
        $urls = array_merge($urls, $this->getSubitemUrls($item));
      }

      $item->isParent = $children && in_array(static::$currentUrl, $urls);
      $item->children = $this->addIsParent($children);

      return $item;
    }, $items);
  }

  /**
   * Collect URLs of all nested menu items.
   */
  private function getSubitemUrls(object $item): array
  {
    $urls = [];
    $children = $item->children ?? [];

    foreach ($children as $subItem) {
      $urls[] = $subItem->url;

      if (!empty($subItem->children)) {
        $urls = array_merge($urls, $this->getSubitemUrls($subItem));
      }
    }

    return $urls;
  }
}

<?php

namespace Theme;

class Cleanup
{
  public static function init(): void
  {
    // Remove stuff from head
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'rest_output_link_wp_head', 10);
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
    remove_action('wp_head', 'wp_oembed_add_host_js');
    remove_action('wp_head', 'wp_resource_hints', 2);
    remove_action('wp_head', 'wp_shortlink_wp_head', 10);

    // Remove stuff from HTML headers
    remove_action('template_redirect', 'wp_shortlink_header', 11);
    remove_action('template_redirect', 'rest_output_link_header', 11);

    // Redirect the default feed to the homepage
    add_action('do_feed', [static::class, 'disableFeeds'], 1);

    // Redirect the Atom feed to the homepage
    add_action('do_feed_atom', [static::class, 'disableFeeds'], 1);

    // Redirect the RDF feed to the homepage
    add_action('do_feed_rdf', [static::class, 'disableFeeds'], 1);

    // Redirect the RSS feed to the homepage
    add_action('do_feed_rss', [static::class, 'disableFeeds'], 1);

    // Redirect the RSS2 feed to the homepage
    add_action('do_feed_rss2', [static::class, 'disableFeeds'], 1);

    // Redirect the Atom comments feed to the homepage
    add_action('do_feed_atom_comments', [static::class, 'disableFeeds'], 1);

    // Redirect the RSS2 comments feed to the homepage
    add_action('do_feed_rss2_comments', [static::class, 'disableFeeds'], 1);

    // Disable comments
    add_filter('comments_open', '__return_false');

    // Disable pings
    add_filter('pings_open', '__return_false');

    // Remove emojis
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
  }

  /**
   * Redirect all feeds to the homepage.
   */
  public static function disableFeeds()
  {
    wp_redirect(home_url());
    exit();
  }
}

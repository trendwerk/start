<?php

namespace Theme\Traits;

use Theme\Taxonomies\Category;
use Theme\Taxonomies\Tag;
use Theme\User;
use WP_Post;

trait IsPost
{
  private int $id;
  private WP_Post|null $wpPost;

  public function __construct(int $id)
  {
    $this->id = $id;
    $this->wpPost = get_post($id);
  }

  /**
   * Get the post ID.
   */
  public function id(): int
  {
    return $this->id;
  }

  /**
   * Get the post title.
   */
  public function title(): string
  {
    return get_the_title($this->id);
  }

  /**
   * Get the filtered post content.
   */
  public function content(): string
  {
    return apply_filters(
      'the_content',
      get_post_field('post_content', $this->id)
    );
  }

  /**
   * Get the post excerpt as plain text.
   */
  public function excerpt(): string
  {
    return strip_tags(get_the_excerpt($this->id));
  }

  /**
   * Get the post permalink.
   */
  public function link(): string
  {
    return get_the_permalink($this->id);
  }

  /**
   * Get the post type archive URL.
   */
  public static function archiveLink(): string|false
  {
    return get_post_type_archive_link(static::$postType);
  }

  /**
   * Get one ACF field value for this post.
   */
  public function field(string $name): mixed
  {
    return get_field($name, $this->id);
  }

  /**
   * Get the featured image attachment ID.
   */
  public function image(): mixed
  {
    return $this->field('_thumbnail_id');
  }

  /**
   * Get the formatted post date.
   */
  public function date(): string
  {
    return get_the_date('', $this->id());
  }

  /**
   * Get the post author.
   */
  public function author(): User
  {
    $authorId = get_post_field('post_author', $this->id());

    return new User((int) $authorId);
  }

  /**
   * Get the post categories.
   */
  public function categories(): ?array
  {
    $terms = get_the_terms($this->id, Category::$taxonomy);

    if (!$terms) {
      return null;
    }

    return array_map(function ($term) {
      return new Category($term->term_id);
    }, $terms);
  }

  /**
   * Get the post tags.
   */
  public function tags(): ?array
  {
    $terms = get_the_terms($this->id, Tag::$taxonomy);

    if (!$terms) {
      return null;
    }

    return array_map(function ($term) {
      return new Tag($term->term_id);
    }, $terms);
  }

  /**
   * Get the latest posts of this post type.
   */
  public static function latest(int $number = 10): array
  {
    $ids = get_posts([
      'fields' => 'ids',
      'post_type' => static::$postType,
      'posts_per_page' => $number,
    ]);

    return array_map(function ($id) {
      return new self($id);
    }, $ids);
  }
}

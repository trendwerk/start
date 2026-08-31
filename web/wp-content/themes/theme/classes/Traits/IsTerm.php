<?php

namespace Theme\Traits;

use WP_Error;
use WP_Term;

trait IsTerm
{
  private int $id;
  private WP_Term|WP_Error|null $wpTerm;

  public function __construct(int $id)
  {
    $this->id = $id;
    $this->wpTerm = get_term($id);
  }

  /**
   * Get the term ID.
   */
  public function id(): int
  {
    return $this->id;
  }

  /**
   * Get the term name.
   */
  public function title(): string
  {
    return $this->wpTerm->name;
  }

  /**
   * Get the term archive URL.
   */
  public function link(): string|WP_Error
  {
    return get_term_link($this->id);
  }
}

<?php

namespace Theme\Taxonomies;

use Theme\Traits\IsTerm;

class Tag
{
  use IsTerm;

  public static string $taxonomy = 'post_tag';
}

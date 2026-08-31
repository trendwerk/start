<?php

/**
 * Render a template file from the components folder.
 */
function component(string $name, array $args = []): string
{
  $file = dirname(__FILE__, 2) . '/components/' . $name . '.php';

  extract($args);
  ob_start();
  require $file;

  return ob_get_clean();
}

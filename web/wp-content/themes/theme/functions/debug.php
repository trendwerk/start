<?php

/**
 * Dump a variable.
 */
if (!function_exists('debug')) {
  function debug($var): void
  {
    ?>
    <div class="
      fixed left-0 top-0
      flex flex-col gap-5
      w-screen h-screen px-5 py-10
      bg-gray-900/50
      overflow-y-auto z-50
    ">
        <pre class="
          bg-gray-800 rounded-md
          text-white text-base
          w-full max-w-screen-md p-5 mx-auto
        ">
        <?php var_dump($var); ?>
      </pre>
    </div>
    <?php
  }
}

/**
 * Log a variable to debug.log.
 */
if (!function_exists('debug_log')) {
  function debug_log($vars): void
  {
    error_log(json_encode($vars));
  }
}

/**
 * Dump a variable.
 */
if (!function_exists('dump')) {
  function dump($vars): void
  {
    debug($vars);
  }
}

/**
 * Dump a variable.
 */
if (!function_exists('dd')) {
  function dd($vars): void
  {
    debug($vars);
  }
}

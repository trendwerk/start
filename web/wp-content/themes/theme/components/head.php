<!DOCTYPE html>
<html
  data-name="head"
  <?php language_attributes(); ?>
>
  <head>
    <title><?php wp_title(); ?></title>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta
      content="width=device-width, initial-scale=1.0"
      name="viewport"
    >
    <?php wp_head(); ?>
  </head>

  <body>
    <?php wp_body_open(); ?>

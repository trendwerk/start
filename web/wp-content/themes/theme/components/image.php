<?php
use Theme\Image;

$image = new Image($args);
?>

<img
  alt="<?= $image->alt; ?>"
  class="<?= $image->class; ?> object-cover bg-gray-600"
  data-name="image"
  fetchpriority="<?= $image->loading == 'eager' ? 'high' : 'low'; ?>"
  height="<?= $image->height; ?>"
  loading="<?= $image->loading; ?>"
  sizes="<?= $image->sizes; ?>"
  src="<?= $image->src; ?>"
  srcset="<?= $image->srcset; ?>"
  width="<?= $image->width; ?>"
/>

<?php
if (!$youtubeId) {
  return;
}

// Get thumbnail image URL
$image = "https://i.ytimg.com/vi/{$youtubeId}/maxresdefault.jpg";
$status = wp_remote_retrieve_response_code(wp_remote_get($image));

if ($status !== 200) {
  $image = "https://i.ytimg.com/vi/{$youtubeId}/hqdefault.jpg";
}
?>

<div
  class="w-full aspect-video overflow-hidden rounded-md shadow-md bg-gray-600 mb-6 group relative"
  data-name="youtube"
>
  <button
    aria-label="Play YouTube video"
    class="youtube absolute top-0 left-0 w-full h-full group cursor-pointer"
    data-youtube-id="<?= $youtubeId; ?>"
  >
    <div
      class="absolute top-1/2 left-1/2 flex aspect-square w-1/6 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-primary-600 text-white transition-colors group-hover:bg-primary-800"
    >
      <svg
        class="h-1/3 w-1/3 translate-x-1/8"
        viewBox="0 0 40 40"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          d="m0 0 40 20-40 20z"
          fill="currentColor"
        />
      </svg>
    </div>

    <img
      class="
        absolute left-0 top-0 w-full h-full object-cover
        group-hover:scale-110 transition-transform ease-out duration-300
      "
      height="<?= round((720 / 16) * 9); ?>"
      loading="lazy"
      src="<?= $image; ?>"
      width="720"
    />
  </button>
</div>

<?php

namespace Theme;

// Require functions
require_once 'functions/component.php';
require_once 'functions/debug.php';

// Initialize classes
Admin::init();
Assets::init();
Blocks\LatestPosts::init();
Cleanup::init();
Content::init();
Editor::init();
Excerpt::init();
GeneralContent::init();
Image::init();
Mail::init();
Media::init();
Menu::init();
Oembed::init();
Performance::init();
Plugins\Acf::init();
Plugins\WpMailSmtp::init();
PostTypes\Page::init();
PostTypes\Post::init();
Security::init();
Translation::init();
User::init();

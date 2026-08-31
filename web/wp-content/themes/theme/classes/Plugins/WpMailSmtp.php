<?php

namespace Theme\Plugins;

class WpMailSmtp
{
  public static function init(): void
  {
    // Disable weekly email summaries
    add_filter(
      'wp_mail_smtp_reports_emails_summary_is_disabled',
      '__return_true'
    );
  }
}

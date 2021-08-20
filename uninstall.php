<?php
// upewnienie się czy skrypt uruchamiany jest podczas dezinstalacji wtyczki
if (!defined('WP_UNINSTALL_PLUGIN'))
  exit();

delete_option(woorescon_custom_text);
delete_option(woorescon_fading_excerpt_info);
delete_option(woorescon_excerpt_length);

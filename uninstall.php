<?php
// upewnienie się czy skrypt uruchamiany jest podczas dezinstalacji wtyczki
if (!defined('WP_UNINSTALL_PLUGIN'))
  exit();

delete_option(resconbop_custom_text);
delete_option(resconbop_fading_excerpt_info);
delete_option(resconbop_excerpt_length);

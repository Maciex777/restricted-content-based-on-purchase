<?php

defined( 'ABSPATH' ) || exit;

/**
  * utwórzenie shortcode, aby wyświetlić w nim treść, jeśli użytkownik kupił produkt o określonym id
  */
function woorescon_show_content($atts = [], $content = null, $tag = ''){
  // normalizuj klucze atrybutów, małe litery
  $atts = array_change_key_case((array) $atts, CASE_LOWER);

  $output = '';
  $output .= '<div class="woorescon-box">';

  $current_user = wp_get_current_user();

  if ( current_user_can('administrator') || wc_customer_bought_product($current_user->email, $current_user->ID, $atts['product_id'])) {

      if (!is_null($content)) {
          // zabezpieczenie output uruchamiające the_content filter hook na zmiennej $content
          $output .= apply_filters('the_content', $content);
      }

  } else {
      // Użytkownik nie kupił tego produktu i nie jest administratorem
      $custom_text = woorescon_display_custom_text();
      $output .= $custom_text;
  }

  $output .= '</div>';

  return $output;
}


function woorescon_shortcodes_init()
{
    add_shortcode('woorescon', 'woorescon_show_content');
}

add_action('init', 'woorescon_shortcodes_init');

<?php

defined( 'ABSPATH' ) || exit;

/**
  * utwórzenie shortcode, aby wyświetlić w nim treść, jeśli użytkownik kupił produkt o określonym id
  */
function resconbop_show_content($atts = [], $content = null, $tag = ''){
  global $post;
  // normalizuj klucze atrybutów, małe litery
  $atts = array_change_key_case((array) $atts, CASE_LOWER);

  $output = '';
  $output .= '<div class="resconbop-box">';

  $current_user = wp_get_current_user();

  if ( current_user_can('administrator') || wc_customer_bought_product($current_user->email, $current_user->ID, $atts['id'])) {

    // jeśli id produktu wybranego w przypadku opcji zajawki jest inne od id produktu w shortcode
    if(!current_user_can('administrator') && $post->resconbop_product_select && $post->resconbop_meta_info && 'hide_excerpt_meta' === $post->resconbop_meta_info && $atts['id'] != $post->resconbop_product_select){
      $custom_text = resconbop_display_custom_text();
      $output .= $custom_text;
    } else {
      if (!is_null($content)) {
          // zabezpieczenie output uruchamiające the_content filter hook na zmiennej $content
          $output .= apply_filters('the_content', $content);
      }
    }

  } else {
      // Użytkownik nie kupił tego produktu i nie jest administratorem
      $custom_text = resconbop_display_custom_text();
      $output .= $custom_text;
  }

  $output .= '</div>';

  return $output;
}


function resconbop_shortcodes_init()
{
    add_shortcode('rescon', 'resconbop_show_content');
}

add_action('init', 'resconbop_shortcodes_init');

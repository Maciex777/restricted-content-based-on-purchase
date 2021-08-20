<?php

defined( 'ABSPATH' ) || exit;

/**
  * Tłumaczenie wtyczki
  */
function woorescon_lang_init() {
  load_plugin_textdomain( 'woo-restricted-content', false, WOORESCON_DIR . '/languages' );
}
add_action('init', 'woorescon_lang_init');

/**
  * Podpięcie stylów
  */
function woorescon_load_styles(){
  wp_enqueue_style('woorescon_style', WOORESCON_URL . 'assets/style.css');
}
add_action('wp_enqueue_scripts', 'woorescon_load_styles');

/**
  * Podpięcie skryptów
  */
function woorescon_load_scripts(){
  wp_enqueue_script('woorescon-script', WOORESCON_URL . 'assets/post_options.js', array('jquery'));
}
add_action('admin_head', 'woorescon_load_scripts');


/**
 * Podmiana zawartości wpisu
 */
function woorescon_replace_post_content( $content ) {
  global $post;
  $product_id = $post->woorescon_product_select;
  $current_user = wp_get_current_user();

  $custom_text = woorescon_display_custom_text();

  // znikająca zajawka
  if (get_option("woorescon_fading_excerpt_info")){
    if ('show_fading_excerpt' == get_option("woorescon_fading_excerpt_info")) {
      $fading_excerpt = " fading-excerpt";
    } else {
      $fading_excerpt = "";
    }
  } else {
    $fading_excerpt = " fading-excerpt";
  }

  // długość zajawki
  $excerpt_length = woorescon_excerpt_length();

  if (!current_user_can('administrator')) {
    if ( !$product_id || !wc_customer_bought_product($current_user->email, $current_user->ID, $product_id)) {
      if($post->woorescon_meta_info === 'hide_default_meta') {
    		$content = $custom_text;
    	} elseif ($post->woorescon_meta_info === 'hide_excerpt_meta') {
        if ($post->post_excerpt) {
          $content = $post->post_excerpt;
          $content = '<div class="woorescon_hidden_excerpt' . $fading_excerpt . '">' . $content . '</div>' . $custom_text;
        } else {
          $content = wp_trim_words($post->post_content, $excerpt_length, '...');
          $content = '<div class="woorescon_hidden_excerpt' . $fading_excerpt . '">' . $content . '</div>' . $custom_text;
        }
      }
    }
  }
    return $content;
}

add_filter( 'the_content', 'woorescon_replace_post_content' );


/**
 * Długość zajawki
 */
function woorescon_excerpt_length() {
  if (get_option("woorescon_excerpt_length")) {
    $excerpt_length = get_option("woorescon_excerpt_length");
  } else {
    $excerpt_length = 45;
  }
  return $excerpt_length;
}

/**
 * Przekierowanie
 */
function woorescon_redirect() {
  global $post;
  $product_id = $post->woorescon_product_select;
  $selected_page = $post->woorescon_selected_page_id;
  $current_user = wp_get_current_user();

  if (!current_user_can('administrator')) {
    if ( !$product_id || !wc_customer_bought_product($current_user->email, $current_user->ID, $product_id)) {
      if (!is_home() && $selected_page && 'redirect_meta' === $post->woorescon_meta_info) {
        $url = get_permalink($selected_page);
        wp_redirect( $url );
        exit;
      }
    }
  }
}
add_action( 'template_redirect', 'woorescon_redirect' );


/**
 * Wyświetlenie własnej treści zastępczej
 */
function woorescon_display_custom_text(){
  if (get_option("woorescon_custom_text")) {
    $custom_text = get_option("woorescon_custom_text");
    $custom_text = '<div class="woorescon_custom_text">' . $custom_text . '</div>' ;
  } else {
    $hidden_content_text = __('Hidden content', 'woo-restricted-content');
    $custom_text = '<div class="woorescon_custom_text"><p>'. $hidden_content_text .'</p></div>';
  }
  return $custom_text;
}

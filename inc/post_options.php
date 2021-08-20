<?php

defined( 'ABSPATH' ) || exit;

/* Zdefiniowanie własnegoo pola */
add_action( 'add_meta_boxes', 'woorescon_post_options_metabox' );

add_action( 'admin_init', 'woorescon_post_options_metabox', 1 );

/* Zapisanie wprowadzonych danych */
add_action( 'save_post', 'woorescon_save_post_options' );


/**
 * Zapisanie własnych danych w momencie zapisania wpisu
 */
function woorescon_save_post_options( $post_id ) {
  // sprawdź, czy jest to procedura automatycznego zapisu.
  // Jeśli to nasz formularz nie został przesłany nie zostanie wykonana żadna akcja
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
      return;

  // zweryfikuj, że pochodzi z danego widoku i z odpowiednią autoryzacją,
  // ponieważ save_post może zostać wywołany w innym czasie
  if ( !wp_verify_nonce( @$_POST[$_POST['post_type'] . '_noncename'], plugin_basename( __FILE__ ) ) )
      return;

  // Sprawdzenie uprawnień
  if ( !current_user_can( 'edit_post', $post_id ) )
     return;
  // W przypadku autoryzacji znalezienie i zapisanie danych
  // if( 'post' == $_POST['post_type'] ) {
  if( 'post' == $_POST['post_type'] || 'page' == $_POST['post_type'] ) {
      if ( !current_user_can( 'edit_post', $post_id ) ) {
          return;
      } else {
          $meta_info_field = sanitize_key($_POST['woorescon_meta_info']);
          $product_select_field = sanitize_text_field($_POST['woorescon_product_select']);
          $selected_page_id_field = sanitize_text_field($_POST['woorescon_selected_page_id']);
          update_post_meta( $post_id, 'woorescon_meta_info', $meta_info_field );
          update_post_meta( $post_id, 'woorescon_product_select', $product_select_field );
          update_post_meta( $post_id, 'woorescon_selected_page_id', $selected_page_id_field );
      }
  }

}


/**
 *  Dodanie pola z opcjami w edycji wpisu, strony lub produktu
 *
 */
function woorescon_post_options_metabox() {
    /* Tylko wpisy */
    // add_meta_box( 'post_options', __( 'Restricted options' ), 'woorescon_post_options_code', 'post', 'normal', 'high' );

    /* Wpisy i strony*/
    add_meta_box( 'post_options', __( 'Restricted content options', 'woo-restricted-content' ), 'woorescon_post_options_code', array('post', 'page'), 'normal', 'high' );
}

/**
 *  Wyświetlenie pól opcji
 */
function woorescon_post_options_code( $post ) {
    wp_nonce_field( plugin_basename( __FILE__ ), $post->post_type . '_noncename' );
    $woorescon_meta_info = get_post_meta( $post->ID, 'woorescon_meta_info', true) ? get_post_meta( $post->ID, 'woorescon_meta_info', true) : 1; ?>

    <div class="alignleft">
        <input id="woorescon_show_default_meta" type="radio" name="woorescon_meta_info" value="show_default_meta"<?php checked( 'show_default_meta', $woorescon_meta_info ); ?><?php echo ( $woorescon_meta_info == 1 )?' checked="checked"' : ''; ?> /> <label for="show_default_meta" class="selectit"><?php _e( 'Show content', 'woo-restricted-content' ); ?></label><br />
        <input id="woorescon_hide_default_meta" type="radio" name="woorescon_meta_info" value="hide_default_meta"<?php checked( 'hide_default_meta', $woorescon_meta_info ); ?> /> <label for="hide_default_meta" class="selectit"><?php _e( 'Hide content and display default placeholder content', 'woo-restricted-content' ); ?></label><br />
        <input id="woorescon_hide_excerpt_meta" type="radio" name="woorescon_meta_info" value="hide_excerpt_meta"<?php checked( 'hide_excerpt_meta', $woorescon_meta_info ); ?> /> <label for="hide_excerpt_meta" class="selectit"><?php _e( 'Hide content and display only excerpt and default content', 'woo-restricted-content' ); ?></label><br />
        <input id="woorescon_redirect_meta" type="radio" name="woorescon_meta_info" value="redirect_meta"<?php checked( 'redirect_meta', $woorescon_meta_info ); ?> /> <label for="redirect_meta" class="selectit"><?php _e( 'Redirect to another page', 'woo-restricted-content' ); ?></label><br />
    </div>
    <div class="alignright">
        <p class="description"><?php _e( 'Set restricted content options', 'woo-restricted-content' ); ?></p>
    </div>
    <div class="clear"></div>
    <hr />
    <div class="woorescon-selected-product">
      <h2><?php _e('Required product', 'woo-restricted-content'); ?></h2>
      <?php
      echo woorescon_products_dropdown( $post );
      ?>
    </div>
    <div class="woorescon-selected-page">
      <h2><?php _e('Redirect page', 'woo-restricted-content'); ?></h2>
      <?php
      wp_dropdown_pages(array('name' => 'woorescon_selected_page_id'));
      ?>
    </div>
    <hr />
    <div class="card">
        <h2>Shortcode</h2>
        <span class="description"><?php _e( 'You can limit the visibility of content in the post options or by using a shortcode - just wrap the content between the opening tag: <br><br><strong>[woorescon product_id="33"]</strong>&nbsp;&nbsp; and closing tag:&nbsp;&nbsp; <strong>[/woorescon]</strong>.<br></br> Enter the product id as value of the "product_id" attribute.', 'woo-restricted-content' ); ?></span>
    </div>
    <?php
}


/**
 * lista rozwijana z wyborem produktów
 */
function woorescon_products_dropdown( $post ) {

    ob_start();

    $query = new WP_Query( array(
        'post_type'      => array('product', 'product_variation'),
        'post_status'    => 'publish',
        'posts_per_page' => '-1',
        'depth'          => -1,
    ) );

    if ( $query->have_posts() ) :

    echo '<div class="products-dropdown"><select name="woorescon_product_select" id="woorescon-product-select">
    <option value="">'.__( 'Select product', 'woo-restricted-content' ).'</option>';

    while ( $query->have_posts() ) : $query->the_post();

    echo '<option ';
     if ($post->woorescon_product_select == get_the_ID()) {
       echo 'selected="true"';
     }
    echo ' value="'.get_the_ID().'">'.get_the_title().'</option>';

    endwhile;

    echo '</select></div>';

    wp_reset_postdata();

    endif;

    return ob_get_clean();
}

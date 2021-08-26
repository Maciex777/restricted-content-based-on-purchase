<?php

defined( 'ABSPATH' ) || exit;

/* Zdefiniowanie własnegoo pola */
add_action( 'add_meta_boxes', 'resconbop_post_options_metabox' );

add_action( 'admin_init', 'resconbop_post_options_metabox', 1 );

/* Zapisanie wprowadzonych danych */
add_action( 'save_post', 'resconbop_save_post_options' );


/**
 * Zapisanie własnych danych w momencie zapisania wpisu
 */
function resconbop_save_post_options( $post_id ) {
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
          $meta_info_field = sanitize_key($_POST['resconbop_meta_info']);
          $product_select_field = sanitize_text_field($_POST['resconbop_product_select']);
          $selected_page_id_field = sanitize_text_field($_POST['resconbop_selected_page_id']);
          update_post_meta( $post_id, 'resconbop_meta_info', $meta_info_field );
          update_post_meta( $post_id, 'resconbop_product_select', $product_select_field );
          update_post_meta( $post_id, 'resconbop_selected_page_id', $selected_page_id_field );
      }
  }

}


/**
 *  Dodanie pola z opcjami w edycji wpisu, strony lub produktu
 *
 */
function resconbop_post_options_metabox() {
    /* Tylko wpisy */
    // add_meta_box( 'post_options', __( 'Restricted options' ), 'resconbop_post_options_code', 'post', 'normal', 'high' );

    /* Wpisy i strony*/
    add_meta_box( 'post_options', __( 'Restricted content options', 'restricted-content-based-on-purchase' ), 'resconbop_post_options_code', array('post', 'page'), 'normal', 'high' );
}

/**
 *  Wyświetlenie pól opcji
 */
function resconbop_post_options_code( $post ) {
    wp_nonce_field( plugin_basename( __FILE__ ), $post->post_type . '_noncename' );
    $resconbop_meta_info = get_post_meta( $post->ID, 'resconbop_meta_info', true) ? get_post_meta( $post->ID, 'resconbop_meta_info', true) : 1; ?>

    <div class="alignleft">
        <input id="resconbop_show_default_meta" type="radio" name="resconbop_meta_info" value="show_default_meta"<?php checked( 'show_default_meta', $resconbop_meta_info ); ?><?php echo ( $resconbop_meta_info == 1 )?' checked="checked"' : ''; ?> /> <label for="show_default_meta" class="selectit"><?php _e( 'Show content', 'restricted-content-based-on-purchase' ); ?></label><br />
        <input id="resconbop_hide_default_meta" type="radio" name="resconbop_meta_info" value="hide_default_meta"<?php checked( 'hide_default_meta', $resconbop_meta_info ); ?> /> <label for="hide_default_meta" class="selectit"><?php _e( 'Hide content and display default placeholder content', 'restricted-content-based-on-purchase' ); ?></label><br />
        <input id="resconbop_hide_excerpt_meta" type="radio" name="resconbop_meta_info" value="hide_excerpt_meta"<?php checked( 'hide_excerpt_meta', $resconbop_meta_info ); ?> /> <label for="hide_excerpt_meta" class="selectit"><?php _e( 'Hide content and display only excerpt and default content', 'restricted-content-based-on-purchase' ); ?></label><br />
        <input id="resconbop_redirect_meta" type="radio" name="resconbop_meta_info" value="redirect_meta"<?php checked( 'redirect_meta', $resconbop_meta_info ); ?> /> <label for="redirect_meta" class="selectit"><?php _e( 'Redirect to another page', 'restricted-content-based-on-purchase' ); ?></label><br />
    </div>
    <div class="alignright">
        <p class="description"><?php _e( 'Set restricted content options', 'restricted-content-based-on-purchase' ); ?></p>
    </div>
    <div class="clear"></div>
    <hr />
    <div class="resconbop-selected-product">
      <h2><?php _e('Required product', 'restricted-content-based-on-purchase'); ?></h2>
      <?php
      echo resconbop_products_dropdown( $post );
      ?>
    </div>
    <div class="resconbop-selected-page">
      <h2><?php _e('Redirect page', 'restricted-content-based-on-purchase'); ?></h2>
      <?php
      wp_dropdown_pages(array('name' => 'resconbop_selected_page_id'));
      ?>
    </div>
    <hr />
    <div class="card">
        <h2>Shortcode</h2>
        <span class="description"><?php _e( 'You can limit the visibility of content in the post options or by using a shortcode - just wrap the content between the opening tag: <br><br><strong>[rescon id="33"]</strong>&nbsp;&nbsp; and closing tag:&nbsp;&nbsp; <strong>[/rescon]</strong>.<br></br> Enter the product id as value of the "id" attribute.', 'restricted-content-based-on-purchase' ); ?></span>
    </div>
    <?php
}


/**
 * lista rozwijana z wyborem produktów
 */
function resconbop_products_dropdown( $post ) {

    ob_start();

    $query = new WP_Query( array(
        'post_type'      => array('product', 'product_variation'),
        'post_status'    => 'publish',
        'posts_per_page' => '-1',
        'depth'          => -1,
    ) );

    if ( $query->have_posts() ) :

      echo '<div class="products-dropdown"><select name="resconbop_product_select" id="resconbop-product-select">
      <option value="">'.__( 'Select product', 'restricted-content-based-on-purchase' ).'</option>';

      while ( $query->have_posts() ) : $query->the_post();

        echo '<option ';
         if ($post->resconbop_product_select == get_the_ID()) {
           echo 'selected="true"';
         }
        echo ' value="'.get_the_ID().'">'.get_the_title().'</option>';

      endwhile;

      echo '</select></div>';

      wp_reset_postdata();

    else:

      echo '<p class="description">'.__( 'You have no products', 'restricted-content-based-on-purchase' ).'</p>';

    endif;

    return ob_get_clean();
}

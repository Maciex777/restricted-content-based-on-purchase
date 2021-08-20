<?php

defined( 'ABSPATH' ) || exit;

/**
  * Dodanie zakładki do menu admina
  */
function woorescon_menu(){
  add_menu_page(__( 'Restricted content for users who have not purchased the indicated product', 'woo-restricted-content' ), 'Restricted content', 'manage_options', 'woorescon_options', 'woorescon_options', $icon_url = plugins_url('woo-restricted-content/images/restriction.png'));
}
add_action('admin_menu', 'woorescon_menu');

/**
  * Funkcja strony opcji
  */
function woorescon_options(){
  // sprawdzenie uprawnień użytkownika
  if (!current_user_can('manage_options')) {
    wp_die( __('You do not have sufficient permissions to access this page') );
  }

  /* Po zapisaniu formularza */
  if ( isset( $_REQUEST['action'] ) ) {
    if ('save' == $_REQUEST['action']) {
      $custom_text_field = sanitize_textarea_field($_REQUEST['woorescon_custom_text']);
      $excerpt_length_field = intval($_REQUEST['woorescon_excerpt_length']);
      $fading_excerpt_info_field = sanitize_key($_REQUEST['woorescon_fading_excerpt_info']);
      update_option('woorescon_custom_text', $custom_text_field);
      update_option('woorescon_excerpt_length', $excerpt_length_field);
      update_option('woorescon_fading_excerpt_info', $fading_excerpt_info_field);
         ?>
        <div class="notice updated">
          <p><?php _e('All changes have been saved', 'woo-restricted-content'); ?></p>
        </div>
        <?php
    }
  }

  ?>
    <div class="woorescon-options-wrapper wrap">
      <h1><?php _e('Restricted content settings', 'woo-restricted-content'); ?></h1>
      <form class="skpp-options" method="post">
        <div class="card">
          <h2><?php _e('Default replacement content', 'woo-restricted-content'); ?></h2>
            <?php
            $custom_text = woorescon_display_custom_text();

            $editor_settings = array( 'textarea_name' => 'woorescon_custom_text', 'textarea_rows' => 5 );
            wp_editor( $custom_text, 'woorescon_custom_text_field', $editor_settings );
            ?>
        </div>

        <div class="card postbox">
          <h2><?php _e('Excerpt settings*', 'woo-restricted-content'); ?></h2>
          <p class="description"><?php _e('*These options are active when the "Hide content and display only excerpt" option is used', 'woo-restricted-content'); ?></p>
          <table class="form-table">
            <tbody>
              <tr>
                <th scope="row">
                  <label for="woorescon_excerpt_length"><?php _e('Excerpt length', 'woo-restricted-content'); ?></label>
                </th>

                <td>
                    <?php
                    $woorescon_excerpt_length = woorescon_excerpt_length();

                    if (get_option("woorescon_fading_excerpt_info")) {
                      $woorescon_fading_excerpt = get_option("woorescon_fading_excerpt_info");
                    } else {
                      $woorescon_fading_excerpt = 1;
                    }
                    ?>
                    <input class="small-text" type="number" min="1" name="woorescon_excerpt_length" value="<?php echo esc_attr($woorescon_excerpt_length) ?>"><span> characters</span>
                    <p class="description"><?php _e('The length of the automatically generated excerpt. It does not apply to custom post excerpt.', 'woo-restricted-content'); ?></p>
                </td>
              </tr>

              <tr>
                <th scope="row">
                  <label for="woorescon_fading_excerpt_info"><?php _e('Excerpt fading', 'woo-restricted-content'); ?></label>
                </th>

                <td>
                  <fieldset>
                    <label>
                      <input type="radio" name="woorescon_fading_excerpt_info" value="hide_fading_excerpt"<?php checked( 'hide_fading_excerpt', $woorescon_fading_excerpt ); ?> /> <label for="hide_fading_excerpt" class="selectit"><?php _e( 'Disable excerpt fading', 'woo-restricted-content' ); ?></label><br />
                    </label><br>
                    <label>
                      <input type="radio" name="woorescon_fading_excerpt_info" value="show_fading_excerpt"<?php checked( 'show_fading_excerpt', $woorescon_fading_excerpt ); ?><?php echo ( $woorescon_fading_excerpt == 1 )?' checked="checked"' : ''; ?> /> <label for="show_fading_excerpt" class="selectit"><?php _e( 'Enable excerpt fading*', 'woo-restricted-content' ); ?></label><br />
                    </label>
                    <p class="description"><?php _e('*works fine when the background color is white', 'woo-restricted-content'); ?></p>
                  </fieldset>
                </td>
              </tr>

            </tbody>
          </table>
        </div>

        <input type="hidden" name="action" value="save" />
        <input type="submit" class="button button-primary" value="<?php _e('Save changes', 'woo-restricted-content'); ?>" />
      </form>
    </div>
    <div class="card">
        <h2>Shortcode</h2>
        <span class="description"><?php _e( 'You can limit the visibility of content in the post options or by using a shortcode - just wrap the content between the opening tag: <br><br><strong>[woorescon product_id="33"]</strong>&nbsp;&nbsp; and closing tag:&nbsp;&nbsp; <strong>[/woorescon]</strong>.<br></br> Enter the product id as value of the "product_id" attribute.', 'woo-restricted-content' ); ?></span>
    </div>
    <?php
}

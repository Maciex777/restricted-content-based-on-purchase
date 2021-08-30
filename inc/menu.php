<?php

defined( 'ABSPATH' ) || exit;

/**
  * Dodanie zakładki do menu admina
  */
function resconbop_menu(){
  add_menu_page(__( 'Restricted content for users who have not purchased the indicated product', 'restricted-content-based-on-purchase' ), 'Restricted content', 'manage_options', 'resconbop_options', 'resconbop_options', $icon_url = RESCONBOP_URL . '/images/restriction.png');
}
add_action('admin_menu', 'resconbop_menu');

/**
  * Funkcja strony opcji
  */
function resconbop_options(){
  // sprawdzenie uprawnień użytkownika
  if (!current_user_can('manage_options')) {
    wp_die( __('You do not have sufficient permissions to access this page') );
  }

  /* Po zapisaniu formularza */
  if ( isset( $_REQUEST['action'] ) ) {
    if ('save' == $_REQUEST['action']) {
      $custom_text_field = sanitize_textarea_field($_REQUEST['resconbop_custom_text']);
      $excerpt_length_field = intval($_REQUEST['resconbop_excerpt_length']);
      $fading_excerpt_info_field = sanitize_key($_REQUEST['resconbop_fading_excerpt_info']);
      update_option('resconbop_custom_text', $custom_text_field);
      update_option('resconbop_excerpt_length', $excerpt_length_field);
      update_option('resconbop_fading_excerpt_info', $fading_excerpt_info_field);
         ?>
        <div class="notice updated">
          <p><?php _e('All changes have been saved', 'restricted-content-based-on-purchase'); ?></p>
        </div>
        <?php
    }
  }

  ?>
    <div class="resconbop-options-wrapper wrap">
      <h1><?php _e('Restricted content settings', 'restricted-content-based-on-purchase'); ?></h1>
      <form class="skpp-options" method="post">
        <div class="card">
          <h2><?php _e('Default replacement content', 'restricted-content-based-on-purchase'); ?></h2>
            <?php
            $custom_text = resconbop_display_custom_text();

            $editor_settings = array( 'textarea_name' => 'resconbop_custom_text', 'textarea_rows' => 5 );
            wp_editor( $custom_text, 'resconbop_custom_text_field', $editor_settings );
            ?>
        </div>

        <div class="card postbox">
          <h2><?php _e('Excerpt settings*', 'restricted-content-based-on-purchase'); ?></h2>
          <p class="description"><?php _e('*These options are active when the "Hide content and display only excerpt" option is used', 'restricted-content-based-on-purchase'); ?></p>
          <table class="form-table">
            <tbody>
              <tr>
                <th scope="row">
                  <label for="resconbop_excerpt_length"><?php _e('Excerpt length', 'restricted-content-based-on-purchase'); ?></label>
                </th>

                <td>
                    <?php
                    $resconbop_excerpt_length = resconbop_excerpt_length();

                    if (get_option("resconbop_fading_excerpt_info")) {
                      $resconbop_fading_excerpt = get_option("resconbop_fading_excerpt_info");
                    } else {
                      $resconbop_fading_excerpt = 1;
                    }
                    ?>
                    <input class="small-text" type="number" min="1" name="resconbop_excerpt_length" value="<?php echo esc_attr($resconbop_excerpt_length) ?>"><span> characters</span>
                    <p class="description"><?php _e('The length of the automatically generated excerpt. It does not apply to custom post excerpt.', 'restricted-content-based-on-purchase'); ?></p>
                </td>
              </tr>

              <tr>
                <th scope="row">
                  <label for="resconbop_fading_excerpt_info"><?php _e('Excerpt fading', 'restricted-content-based-on-purchase'); ?></label>
                </th>

                <td>
                  <fieldset>
                    <label>
                      <input type="radio" name="resconbop_fading_excerpt_info" value="hide_fading_excerpt"<?php checked( 'hide_fading_excerpt', $resconbop_fading_excerpt ); ?> /> <label for="hide_fading_excerpt" class="selectit"><?php _e( 'Disable excerpt fading', 'restricted-content-based-on-purchase' ); ?></label><br />
                    </label><br>
                    <label>
                      <input type="radio" name="resconbop_fading_excerpt_info" value="show_fading_excerpt"<?php checked( 'show_fading_excerpt', $resconbop_fading_excerpt ); ?><?php echo ( $resconbop_fading_excerpt == 1 )?' checked="checked"' : ''; ?> /> <label for="show_fading_excerpt" class="selectit"><?php _e( 'Enable excerpt fading', 'restricted-content-based-on-purchase' ); ?></label><br />
                    </label>
                  </fieldset>
                </td>
              </tr>

            </tbody>
          </table>
        </div>

        <input type="hidden" name="action" value="save" />
        <input type="submit" class="button button-primary" value="<?php _e('Save changes', 'restricted-content-based-on-purchase'); ?>" />
      </form>
    </div>
    <div class="card">
        <h2>Shortcode</h2>
        <span class="description"><?php _e( 'You can limit the visibility of content in the post options or by using a shortcode - just wrap the content between the opening tag: <br><br><strong>[rescon id="33"]</strong>&nbsp;&nbsp; and closing tag:&nbsp;&nbsp; <strong>[/rescon]</strong>.<br></br> Enter the product id as value of the "id" attribute.', 'restricted-content-based-on-purchase' ); ?></span>
    </div>
    <?php
}

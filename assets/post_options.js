jQuery(function($){

  $(document).ready(function(){
    dropdownListVisibility();
  });
  $('input:radio[name="woorescon_meta_info"]').change(function(){
    dropdownListVisibility();
  });

  function dropdownListVisibility() {
    if($('#woorescon_show_default_meta').is(':checked')) {
      $('.woorescon-selected-product').hide();
    } else {
      $('.woorescon-selected-product').show();
    }

    if($('#woorescon_redirect_meta').is(':checked')) {
      $('.woorescon-selected-page').show();
    } else {
      $('.woorescon-selected-page').hide();
    }
  };

    var dropdown = '.products-dropdown', button = dropdown+' button', select = dropdown+' select', value = '';
    $(select).change(function(){
        value = $(this).val();
    });
     $(button).click(function(){
        if( value != '' ) location.href = value;
    });

});

jQuery(function($){

  $(document).ready(function(){
    dropdownListVisibility();
  });
  $('input:radio[name="resconbop_meta_info"]').change(function(){
    dropdownListVisibility();
  });

  function dropdownListVisibility() {
    if($('#resconbop_show_default_meta').is(':checked')) {
      $('.resconbop-selected-product').hide();
    } else {
      $('.resconbop-selected-product').show();
    }

    if($('#resconbop_redirect_meta').is(':checked')) {
      $('.resconbop-selected-page').show();
    } else {
      $('.resconbop-selected-page').hide();
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

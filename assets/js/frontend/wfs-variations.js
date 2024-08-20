jQuery(function($) {

	// Variations Radio Buttons
  	$(document).on('click touch mouseover', '.wfs-variations', function() {
    	$(this).attr('data-click', 1);
  	});

  	$('body').on('click', '.wfs-variation-radio', function() {
    
	    var _this = $(this);
	    var _variations = _this.closest('.wfs-variations');
	    var _click = parseInt(_variations.attr('data-click'));
	    var _variations_form = _this.closest('.variations_form');

	    wfs_variations_select(_this, _variations, _variations_form, _click);
	    _this.find('input[type="radio"]').prop('checked', true);

	    /* Trigger Once Variation is Selected */
	    $( document.body ).trigger( 'wfs_variation_selected' );
  	});
});

jQuery(document).on('found_variation', function(e,t) {
  
  	var variation_id = t['variation_id'];
  	var $variations_default = jQuery(e['target']).find('.wfs-variations-default');

  	if ($variations_default.length) {
    	if (parseInt($variations_default.attr('data-click')) < 1) {
      		$variations_default.find('.wfs-variation-radio[data-id="' + variation_id + '"] input[type="radio"]').prop('checked', true);
    	}
  	}
});

function wfs_variations_select(selected, variations, variations_form, click) {
  
  	if ( click > 0 ) {
   
    	variations_form.find('.reset_variations').trigger('click');
    	
    	if (selected.attr('data-attrs') !== '') {
      		var attrs = jQuery.parseJSON(selected.attr('data-attrs'));
      		if (attrs !== null) {
        		for (var key in attrs) {
          			variations_form.find('select[name="' + key + '"]').val(attrs[key]).trigger('change');
        		}
      		}
    	}
  	}
  	jQuery(document).trigger('wfs_selected', [selected, variations, variations_form]);
}
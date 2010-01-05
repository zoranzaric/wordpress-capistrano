jQuery(document).ready(function(){
	jQuery("#sociable_site_list").sortable({});

	jQuery("#sociable_site_list input:checkbox").change(function() {
		if (jQuery(this).attr('checked')) {
			jQuery(this).parent().removeClass("inactive");
			jQuery(this).parent().addClass("active");
		} else {
			jQuery(this).parent().removeClass('active');
			jQuery(this).parent().addClass('inactive');
		}
	} );
});
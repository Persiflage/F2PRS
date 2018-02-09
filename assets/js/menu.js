$(document).ready(function() {
	$('#skill_dropdown').click(function() {
		$('.skill_menu').slideToggle();
	});

	$('#ellipses').click(function() {
		$('#page_search').css('z-index', '11');
		$('#page_search').focus();
	});
});

jQuery(document).ready(function() {
    jQuery('.tabs .tab-links a').on('click', function(e)  {
        var currentAttrValue = jQuery(this).attr('href');

        // Show/Hide Tabs
		jQuery('.tabs ' + currentAttrValue).fadeIn(400).siblings().hide();
        // Change/remove current tab to active
        jQuery(this).parent('li').addClass('active').siblings().removeClass('active');

        e.preventDefault();
    });
});

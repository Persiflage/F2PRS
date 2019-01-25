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
	// Are we on gains.php? Is there a hash value in URL? Is there valid
	// selector element?
	if (window.location.pathname === '/gains.php') {
		var hash = window.location.hash && window.location.hash.slice(1); // Remove the `#`
		var $selector = jQuery('#tab-selector-' + hash);
		if ($selector.length) {
			$selector.addClass('active');
			jQuery('#tab-' + hash).addClass('active');
		} else {
			// Set the first tab as active
			jQuery('#tab-selector-total').addClass('active');
			jQuery('#tab-total').addClass('active');
		}
	}

	jQuery('.tabs .tab-links a').on('click', function(e)  {
		var currentAttrValue = jQuery(this).attr('href');

		if (window.location.hash !== currentAttrValue) {
			// Show/Hide Tabs
			var skillName = currentAttrValue.slice(1);
			jQuery('#tab-' + skillName).fadeIn(400).siblings().hide();
			// Change/remove current tab to active
			jQuery(this).parent('li').addClass('active').siblings().removeClass('active');

			history.pushState(null, null, window.location.pathname + '#' + skillName);
		}
		e.preventDefault();
	});

	window.addEventListener('popstate', function(e) {
		var hash = e.target && e.target.window && e.target.window.location.hash && e.target.window.location.hash.slice(1);

		// Show/Hide Tabs
		jQuery('#tab-' + hash).fadeIn(400).siblings().hide();
		// Change/remove current tab to active
		jQuery('#tab-selector-' + hash).addClass('active').siblings().removeClass('active');
	});
});

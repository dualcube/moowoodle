jQuery( document ).ready( function( $ ) {
	$(".img_tip").each(function() {
		$(this).qtip({
			content: $(this).attr('data-desc'),
			position: {
				my: 'center left',
				at: 'center right',
				viewport: $(window)
			},
			show: {
				event: 'mouseover',
				solo: true,
			},
			hide: {
				inactive: 6000,
				fixed: true
			},
			style: {
				classes: 'qtip-dark qtip-shadow qtip-rounded qtip-dc-css'
			}
		});
	});
	
	// $("#courses-select").chosen({
	// 	width: "75%",
	// 	no_results_text: "No courses found!"
	// });
});

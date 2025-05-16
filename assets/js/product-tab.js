jQuery(document).ready(function ($) {
	const cohortRadio = $('input[name="link_type"][value="cohort"]');

	if (!moowoodle.khali_dabba) {
		cohortRadio.prop('disabled', true).prop('checked', false);
	}

	function fetchAndRenderLinkedItems(type) {
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'get_linkable_courses_or_cohorts',
				type: type,
				nonce: $('input[name="moowoodle_meta_nonce"]').val(),
				post_id: $('#post_ID').val()
			},
			success: function (response) {
				if (response.success) {
					const select = $('#linked_item');
					const selectedId = response.data.selected_id;

					select.empty().append('<option value="">' + moowoodle.select_text + '</option>');

					response.data.items.forEach(function (item) {
						const isSelected = selectedId == item.id ? 'selected' : '';
						const fullname = item.fullname || '';
						const cohortName = item.cohort_name || '';
					
						// Join only non-empty values with ' || '
						const label = [fullname, cohortName].filter(Boolean).join(' || ');
					
						select.append(`<option value="${item.id}" ${isSelected}>${label}</option>`);
					});
					
					

					$('#dynamic-link-select').show();
				} else {
					console.error('AJAX error:', response.data);
				}
			},
			error: function (xhr, status, error) {
				console.error('AJAX request failed:', status, error);
			}
		});
	}

	$('input[name="link_type"]').on('change', function () {
		const type = $(this).val();
		if (type) {
			fetchAndRenderLinkedItems(type);
		} else {
			$('#dynamic-link-select').hide();
		}
	});

	const defaultType = $('input[name="link_type"]:checked').val();
	if (defaultType) {
		fetchAndRenderLinkedItems(defaultType);
	}
});

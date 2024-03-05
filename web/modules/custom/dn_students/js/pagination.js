(function ($, Drupal, drupalSettings) {
	Drupal.behaviors.dn_students = {
		attach: function (context, settings) {
			console.log("data loaded");

			settings.dn_students = settings.dn_students || {};

			$('.pagination-link').each(function () {
				// Check if the clicked element has the 'active' class
				var isActive = $(this).hasClass('active');

				if (isActive) {
					// If it has the 'active' class, get the href attribute
					var urlString = $(this).attr('href');
					// Split the URL string by "/"
					var urlParts = urlString.split("/");
					// Get the last part of the URL (which is the last value)
					var lastValue = urlParts[urlParts.length - 1];

					// for passing pagination no
					$.ajax({
						url: '/ajax/dn_students/route',
						type: 'POST',
						data: { paginationNo: lastValue },
						success: function (response) {
							// Handle the server response
							console.log(response);

							// Optionally, you can use the response data in your form.
							// For example, update a form element with the received data.
							// $('#your-form-element').val(response.data);
						},
						error: function (error) {
							// Handle errors
							console.error(error);
						}
					});
				}
			});

			$('.pagination-link').click(function () {
				$('.pagination-link').removeClass('active');
				$(this).addClass('active');
			});

		}
	};
})(jQuery, Drupal, drupalSettings);
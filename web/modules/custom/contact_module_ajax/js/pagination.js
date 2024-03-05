(function ($) {
  Drupal.behaviors.ajaxpagination = {
    attach: function (context, settings) { 
	// console.log("data loaded");
		
		$('.pagination-link').click(function(){  
		$('.pagination-link').removeClass('active');
		$(this).addClass('active');
		});

		$('.left-pagination, .right-pagination, .pagination-link').once('ajaxpagination').click(function(e) {
			e.preventDefault();  
			var page_no = $(this).attr('no');
			Drupal.behaviors.contactcrud.getContactData(page_no);
			});

			

		// //jquery click event to change color
		// $('.contact_fname,.contact_email,.contact_phone').click(function(){  
		// 	$(this).css('background-color', 'blue');
		// 	});

	    // //jquery blur event to remove style attribute
		// $('.contact_fname,.contact_email,.contact_phone').blur(function(){  
		// 	$(this).removeAttr('style');
		// 	});

	    // //jquery to add validation on fname on form change event
	    // $('.contact_fname').change(function(){  
		// 	var fname = $(this).val();
		// 	console.log(fname);
		// 	if (fname == '') {
		// 		$text_css = 'red';
		// 		$message = ('FullName not valid some error');
		// 		$('#error-message').html($message);
		// 	}
		// 	else {
		// 		$('#error-message').html('');
		// 	  }
		// });

		//jquery submit event to remove style attribute
		// $('#contact-module-form').once('ajaxpagination').submit(function(){  
		// 	alert("submitted by me");
		// 	});

		

	}
  };
})(jQuery);
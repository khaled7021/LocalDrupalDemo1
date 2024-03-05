(function ($, Drupal, drupalSettings) {
    Drupal.behaviors.contactcrud = {
        attach: function (context, settings) {
            //load contact table
            Drupal.behaviors.contactcrud.getContactData('All');

            //insert operation
            $('.contact-module-ajax-form').once('contactcrud').submit(function (e) {
                e.preventDefault();
                var postData = {};
                $('.txt-class').each(function (index) {
                    var inputVal = $(this).val();
                    if (inputVal != '' && inputVal != 0 && inputVal != 'All' && inputVal != '_none') {
                        postData[$(this).attr('name')] = inputVal;
                    }
                });
                if (postData['fullname'] && postData['email'] && postData['phone']) {
                    Drupal.behaviors.contactcrud.insertContactData(postData);
                  }
            });      

            //update operation
            $('.contact-edit-data').once('contactcrud').click(function (e) {
                e.preventDefault();
                alert("hi");
                // var record_id = this.id;
                // console.log(record_id);
                // // console.log(drupalSettings.path.baseUrl + 'ajax/contact_module_ajax/Contacts/edit/' + record_id);
                // Drupal.behaviors.contactcrud.getEditForm(record_id);
            });
            
            $('.contact-module-ajax-form-edit').once('contactcrud').submit(function (e) {
                e.preventDefault();
                var postData = {};
                $('.txt-class').each(function (index) {
                    var inputVal = $(this).val();
                    if (inputVal != '' && inputVal != 0 && inputVal != 'All' && inputVal != '_none') {
                        postData[$(this).attr('name')] = inputVal;
                    }
                });
                if (postData['fullname'] && postData['email'] && postData['phone']) {
                    Drupal.behaviors.contactcrud.updateContactData(postData);
                  }
            });      

            
            //delete operation
            $('.contact-delete-data').once('contactcrud').click(function (e) {
                e.preventDefault();
                alert("delete");
                // var record_id = this.id;
                // console.log(record_id);
                // // console.log(drupalSettings.path.baseUrl + 'ajax/contact_module_ajax/Contacts/edit/' + record_id);
                // Drupal.behaviors.contactcrud.getEditForm(record_id);
            });
        },

        // editContactData: function editContactData(record_id) {
        //     $.ajax(
        //         {
        //             url: drupalSettings.path.baseUrl + 'ajax/contact_module_ajax/Contacts/edit/' + record_id, 
        //             success: function(data) {
        //               console.log("success");
        //             }
        //         });
        // },

        getContactData: function getContactData(page_no) {
            var page = page_no;
            $.ajax(
                {
                    url: drupalSettings.path.baseUrl + 'call/ajax/contact/get',
                    data: {page : page},
                    success: function(data) {
                        $('.table-data-contact').html('');
                        $('.table-data-contact').html(data);
                        // $('.pagination').html('');
                        // $('.result_message').append(data.table);
                        // $('.pagination').append(getPager_ajax());
                        // $('.pagination-link:first').addClass('active');
                        // $('.pagination-link').removeClass('active');
                        $('.txt-class').val('');
                        $('#error-message').val('');
                    }
                });
        },

        insertContactData: function insertContactData(postData) {
            $.ajax(
                {
                    url: drupalSettings.path.baseUrl + 'call/ajax/contact/insert', 
                    data: postData,
                    method: "POST",
                    success: function(data) {
                        Drupal.behaviors.contactcrud.getContactData('All');
                        // $('.result_message').html('');
                        // $('.pagination').html('');
                        // $('.result_message').html(data);

                        // $('.pagination').append(getPager_ajax());
                        // $('.pagination-link:first').addClass('active');
                        // $('.pagination-link').removeClass('active');
                        $('.txt-class').val('');
                        $('#error-message').val('');
                    }
                });
         },

        // updateContactData: function updateContactData(postData) {
        //     $.post(drupalSettings.path.baseUrl + 'call/ajax/contact/update', postData, function(data) {
        //         $('.result_message').html('');
        //         $('.result_message').append(data);
        //     });
        // },
    }
})(jQuery, Drupal, drupalSettings);
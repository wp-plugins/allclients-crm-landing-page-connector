(function( $ ) {
    'use strict';

    /**
     * Landing pages meta box
     */
    $(function() {
        var $meta_box = $('div#ac_landing_page_select');
        if ($meta_box.length) {

            var $inputFolder = $meta_box.find('select[name="landing_page_folder"]'),
                $spinFolder = $meta_box.find('#spin-folder-select'),
                $inputPage = $meta_box.find('select[name="landing_page_id"]'),
                $spinPage = $meta_box.find('#spin-page-select'),
                $inputTitle = $('input[name="post_title"]');

            var disableForm = function () {
                $inputFolder.attr('disabled', true);
                $inputPage.attr('disabled', true);
                $inputTitle.attr('disabled', true);
            };

            var enableForm = function () {
                $inputFolder.removeAttr('disabled');
                $inputPage.removeAttr('disabled');
                $inputTitle.removeAttr('disabled');
            };

            /**
             * Selecting page sets the post title
             *
             * @param {object|null} data
             */
            var selectPage = function (data) {
                if (data !== null && typeof( data ) === 'object') {
                    $inputTitle.val(data.title);
                    $inputTitle[0].focus();
                } else {
                    $inputTitle.val('');
                }
            };

            // If folders swap page select with XHR
            if ($inputFolder.length) {
                $inputFolder.on('change', function () {

                    // Disable form
                    disableForm();
                    $spinFolder.css('display', 'inline-block');

                    // Get current page selection
                    var currentPage = $inputPage.val();

                    // Request landing page list via XHR
                    $.post(ajaxurl, {
                        'action': 'allclients_get_landing_pages',
                        'folder': $inputFolder.val()
                    }, function (response) {
                        // Ensure a good response from server
                        if (typeof response !== 'object' || !response.hasOwnProperty('success')) {
                            response = {
                                'success': false,
                                'data': 'Error communicating with WordPress.'
                            };
                        }

                        // Set the page select options
                        $inputPage.find('option').remove();
                        if (response.success) {
                            if (response.data.length === 0) {
                                $inputPage.append('<option value="">' + $inputPage.data('none') + '</option>');
                            } else {
                                $inputPage.append('<option value="">' + $inputPage.data('select') + '</option>');
                                $.each(response.data, function (i, page) {
                                    $inputPage.append('<option value="' + parseInt(page.webformid) + '">' + page.name + '</option>');
                                });
                            }
                        }

                        // Attempt to select current page in new folder list, or clear selection
                        if (currentPage.length) {
                            if ($inputPage.find('option[value="' + currentPage + '"]').length) {
                                $inputPage.find('option[value="' + currentPage + '"]').attr('selected', true);
                            } else {
                                selectPage(null);
                            }
                        }

                        // Enable the form
                        enableForm();
                        $spinFolder.css('display', 'none');
                    });
                });
            }

            // On page select
            $inputPage.on('change', function () {
                var landing_page_id = $inputPage.val();
                if (!landing_page_id) {
                    selectPage(null);
                    return;
                }

                // Disable the form
                disableForm();
                $spinPage.css('display', 'inline-block');

                // Request landing page details via XHR
                $.post(ajaxurl, {
                    'action': 'allclients_get_landing_page',
                    'page': landing_page_id
                }, function (response) {

                    // Ensure a good response from server
                    if (typeof response !== 'object' || !response.hasOwnProperty('success')) {
                        response = {
                            'success': false,
                            'data': 'Error communicating with WordPress.'
                        };
                    }

                    // Re-enable form, must happen before selectPage
                    enableForm();
                    $spinPage.css('display', 'none');

                    // Process response
                    if (response.success) {
                        selectPage(response.data);
                    } else {
                        selectPage(null);
                    }

                });

            }); // end on change

        }
    });

})( jQuery );

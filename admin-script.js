 
jQuery(document).ready(function($) {
    var mediaUploader;

    // Open the WordPress media uploader
    $('.bloglogistics-upload-button').on('click', function(e) {
        e.preventDefault();

        // If the uploader already exists, reopen it
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        // Create the media frame
        mediaUploader = wp.media({
            title: 'Choose Maintenance Image',
            button: {
                text: 'Use this image'
            },
            multiple: false // Set to true to allow multiple files to be selected
        });

        // When a file is selected, grab the URL and display it
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#bloglogistics_maintenance_custom_image_url').val(attachment.url);
            $('.bloglogistics-image-preview').html('<img src="' + attachment.url + '" style="max-width:200px; height:auto;" />');
            $('.bloglogistics-remove-button').show(); // Show remove button
        });

        // Open the uploader
        mediaUploader.open();
    });

    // Remove custom image
    $('.bloglogistics-remove-button').on('click', function(e) {
        e.preventDefault();
        $('#bloglogistics_maintenance_custom_image_url').val('');
        $('.bloglogistics-image-preview').empty();
        $(this).hide(); // Hide remove button
    });

    // Initial check for image existence to show/hide remove button
    if ($('#bloglogistics_maintenance_custom_image_url').val() === '') {
        $('.bloglogistics-remove-button').hide();
    }
});
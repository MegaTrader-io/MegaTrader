jQuery(document).ready(function($) {
    $(document).on('click', '#add-repeater-item', function() {
        var newItem = $('.repeater-item:first').clone();
        newItem.find('input').val('');
        $('#repeater-container').append(newItem);
    });

    $(document).on('click', '.remove-repeater-item', function() {
        if ($('.repeater-item').length > 1) {
            $(this).closest('.repeater-item').remove();
        }
    });
});
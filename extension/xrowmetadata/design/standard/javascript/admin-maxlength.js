/**
 * Warn if length of input value exists data-maxlength attribute.
 * Add style '.warn' to the input element.
 * See ../stylesheets/admin-maxlength.css
 */

jQuery(document).ready(function() {
    jQuery('input[data-maxlength]').on('input', function() {
        if (jQuery(this).val().length > jQuery(this).data('maxlength')) {
            jQuery(this).addClass('warn');
        } else {
            jQuery(this).removeClass('warn');
        }
    });
    jQuery('input[data-maxlength]').trigger('input');
});

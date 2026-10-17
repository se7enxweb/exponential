/*
 * The ordering of the sub-items on the node view (ordering.tpl): the Set button is highlighted while the sort
 * field or order differs from the stored one.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/features/6.0/content-security-policy.md).
 */
jQuery('#ezasi-sort-field, #ezasi-sort-order').each( function(){
    jQuery( this ).attr( 'initial', this.value );
} ).on('change', function(){
    var t = $(this), o = $(this.id === 'ezasi-sort-field' ? '#ezasi-sort-order' : '#ezasi-sort-field'), s = $('#ezasi-sort-set');
    // signal in gui if user needs to save this or not
    if ( t.val() === t.attr('initial') && o.val() === o.attr('initial') )
        s.removeClass('defaultbutton').addClass('button');
    else
        s.removeClass('button').addClass('defaultbutton');
});

/*
 * The locations tab of the node view (locations.tpl): the Set main button is highlighted once another location
 * is chosen as the main one.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/en/us/features/6.0/content-security-policy.md).
 */
(function( $ )
{
    $('#tab-locations-list input.main-locations-radio').on('change', function()
    {
        if ( this.className === 'main-locations-radio' )
            $('#tab-locations-list-set-main').removeClass('button').addClass('defaultbutton');
        else
            $('#tab-locations-list-set-main').removeClass('defaultbutton').addClass('button');
    });
})( jQuery );

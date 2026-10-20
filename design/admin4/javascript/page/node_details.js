/*
 * The details tab of the node view (details.tpl): the Set buttons of the states and the section are highlighted
 * once their choice changes.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md).
 */
(function( $ )
{
    $('#tab-details-states-list select, #tab-details-states-subtree').on('change', function()
    {
        var btn = $('#tab-details-set-states');
        if ( !btn.attr('disabled') )
        {
            btn.removeClass('button').addClass('defaultbutton');
        }
    });
    $('#selected-section-id').on('change', function()
    {
        var btn = $('#tab-details-set-section');
        if ( !btn.attr('disabled') )
        {
            btn.removeClass('button').addClass('defaultbutton');
        }
    });
})( jQuery );

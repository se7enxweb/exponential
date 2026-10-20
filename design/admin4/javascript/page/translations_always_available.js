/*
 * admin4 translations tab: Update becomes the default button when the always-available checkbox changes.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/en/us/features/6.0/content-security-policy.md).
 */
(function( $ )
{
    $('#tab-translations-alwaysavailable-checkbox').on('change', function()
    {
        $('#tab-translations-alwaysavailable-btn').removeClass('button').addClass('defaultbutton');
    });
})( jQuery );

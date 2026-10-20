/*
 * admin4 translations tab: Set main becomes the default button when another main language is chosen.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/en/us/features/6.0/content-security-policy.md).
 */
(function( $ )
{
    $('#tab-translations-list input.main-translation-radio').on('change', function()
    {
        if ( this.className === 'main-translation-radio' )
            $('#tab-translations-list-set-main').removeClass('button').addClass('defaultbutton');
        else
            $('#tab-translations-list-set-main').removeClass('defaultbutton').addClass('button');
    });
})( jQuery );

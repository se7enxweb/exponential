/*
 * The language choice of the edit form (content/edit_languages.tpl): choosing an existing translation disables
 * the source translations, choosing a new one enables them.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/features/6.0/content-security-policy.md).
 */
(function( $ )
{
    if ( document.getElementById('ezcoeditlanguages-sourcets') )
    {
        // setup onchange events
        jQuery( '#ezcoeditlanguages-existingts input[type=radio]' ).on('change', function()
        {
            jQuery( '#ezcoeditlanguages-sourcets input[type=radio]' ).prop( 'disabled', true );
        });
        jQuery( '#ezcoeditlanguages-newts input[type=radio]' ).on('change', function()
        {
            jQuery( '#ezcoeditlanguages-sourcets input[type=radio]' ).prop( 'disabled', false );
        });

        // disable source translations if existing translation is selected
        if ( jQuery( '#ezcoeditlanguages-existingts input[checked=checked]' ).length > 0 )
        {
            jQuery( '#ezcoeditlanguages-sourcets input[type=radio]' ).prop( 'disabled', true );
        }
    }
})( jQuery );

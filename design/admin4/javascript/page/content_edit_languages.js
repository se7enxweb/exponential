/*
 * The language choice of the edit form (content/edit_languages.tpl): choosing an existing translation disables
 * the source translations, choosing a new one enables them.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md).
 */
(function()
{
    var disableSources = function( disabled )
    {
        Array.prototype.forEach.call( document.querySelectorAll( '#ezcoeditlanguages-sourcets input[type=radio]' ), function( radio )
        {
            radio.disabled = disabled;
        });
    };
    var onChange = function( selector, disabled )
    {
        Array.prototype.forEach.call( document.querySelectorAll( selector ), function( radio )
        {
            radio.addEventListener( 'change', function()
            {
                disableSources( disabled );
            });
        });
    };

    if ( document.getElementById('ezcoeditlanguages-sourcets') )
    {
        // setup onchange events
        onChange( '#ezcoeditlanguages-existingts input[type=radio]', true );
        onChange( '#ezcoeditlanguages-newts input[type=radio]', false );

        // disable source translations if existing translation is selected
        if ( document.querySelector( '#ezcoeditlanguages-existingts input[checked=checked]' ) )
        {
            disableSources( true );
        }
    }
})();

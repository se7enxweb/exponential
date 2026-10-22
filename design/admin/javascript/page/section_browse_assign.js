/*
 * section/assign, the content browser (section/browse_assign.tpl): which objects get the section is chosen above the
 * browser, but the tabs, the tree, the list and the pager reload the page, which would preselect "every object below
 * it" again. The choice is kept per section for the browser tab in sessionStorage, put back on every page of the
 * browser and forgotten when the browse form is sent. A file of its own, so the page runs under a Content Security
 * Policy without 'unsafe-inline' (doc/eng-us/features/6.0/content-security-policy.md). The same file is in
 * design/admin and design/admin4.
 */
(function( fn )
{
    if ( document.readyState !== 'loading' ) fn(); else document.addEventListener( 'DOMContentLoaded', fn );
})(function()
{
    var box = document.querySelector( 'fieldset.section-assign-scope' );
    if ( !box )
        return;
    var key = 'exp-section-assign-scope-' + ( box.getAttribute( 'data-section-id' ) || '' );
    var radios = box.querySelectorAll( 'input[name="SectionScope"]' );
    function storage( value )
    {
        try
        {
            if ( value === undefined )
                return window.sessionStorage.getItem( key );
            if ( value === null )
                window.sessionStorage.removeItem( key );
            else
                window.sessionStorage.setItem( key, value );
        }
        catch ( e ) {}
        return null;
    }
    var kept = storage();
    for ( var i = 0; i < radios.length; i++ )
    {
        if ( kept !== null && radios[i].value === kept )
            radios[i].checked = true;
        radios[i].addEventListener( 'change', function()
        {
            if ( this.checked )
                storage( this.value );
        });
    }
    // every way the form is sent leaves the browser (Select without an item goes back to the section list)
    var form = document.getElementById( 'browse-form' );
    if ( form )
        form.addEventListener( 'submit', function() { storage( null ); } );
});

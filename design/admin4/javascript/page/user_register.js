/*
 * admin4 user/register: focuses the first text field of the registration form and disables the OK and Cancel
 * buttons just after one of them is clicked (a second click does not send the form twice). A file of its own, so
 * the page runs under a Content Security Policy without 'unsafe-inline' (doc/eng-us/features/6.0/content-security-policy.md).
 */
(function( fn )
{
    if ( document.readyState !== 'loading' ) fn(); else document.addEventListener( 'DOMContentLoaded', fn );
})(function()//called on document.ready
{
    var form = document.registerform;
    if ( !form )
        return;
    for( var i = 0, l = form.elements.length; i < l; i++ )
    {
        if( form.elements[i].type == 'text' )
        {
            form.elements[i].focus();
            return;
        }
    }
});

(function()
{
    function disableButtons()
    {
        document.getElementById( 'PublishButton' ).disabled = true;
        document.getElementById( 'CancelButton' ).disabled = true;
    }

    // after the click, so the button's own name and value are still sent with the form
    document.addEventListener( 'click', function( e )
    {
        var el = e.target;
        if ( el && ( el.id === 'PublishButton' || el.id === 'CancelButton' ) )
            window.setTimeout( disableButtons, 1 );
    } );
})();

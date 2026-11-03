/*
 * "Choose an existing image" of the image field's edit form (content/datatype/edit/ezimage.tpl): Enter in the
 * search field searches, as a click on the field's own Search button, instead of sending the form with its
 * default button (which publishes). The search itself is a post of the form, so it works without this file.
 * Plain JavaScript, no library.
 */
(function( document )
{
    function onKeyDown( e )
    {
        var input = e.target;
        if ( e.key !== 'Enter' || e.isComposing || !input || !input.matches || !input.matches( 'input.exp-image-pick-text' ) )
            return;
        var button = document.getElementById( input.id + '_button' );
        if ( !button )
            return;
        e.preventDefault();
        e.stopPropagation();
        button.click();
    }
    document.addEventListener( 'keydown', onKeyDown, true );
})( document );

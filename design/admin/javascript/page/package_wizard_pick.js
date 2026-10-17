/*
 * admin and admin4 package/create, the pickers of the wizard (create/wizard_pick_script.tpl): the selection count and
 * the filter of the rows. The same file is in design/admin and design/admin4. A file of its own, so the page runs
 * under a Content Security Policy without 'unsafe-inline' (doc/features/6.0/content-security-policy.md).
 */
(function()
{
    var picks = document.querySelectorAll( '.a4-package-pick' );
    Array.prototype.forEach.call( picks, function( pick )
    {
        var boxes = pick.querySelectorAll( 'tbody input[type="checkbox"]' ),
            count = pick.querySelector( '.a4-package-pick-count' ),
            filterBar = pick.querySelector( '.a4-package-pick-filter' ),
            none = pick.querySelector( '.a4-package-pick-none' );
        function updateCount()
        {
            if ( !count || !count.getAttribute( 'data-template' ) )
                return;
            var checked = 0, total = 0;
            Array.prototype.forEach.call( boxes, function( box )
            {
                if ( box.disabled )
                    return;
                total++;
                if ( box.checked )
                    checked++;
            } );
            count.textContent = count.getAttribute( 'data-template' ).replace( '%count', checked ).replace( '%total', total );
        }
        pick.addEventListener( 'change', function( event )
        {
            if ( event.target && event.target.type === 'checkbox' )
                updateCount();
        } );
        if ( filterBar && boxes.length > 8 )
        {
            var input = filterBar.querySelector( 'input' ),
                rows = pick.querySelectorAll( 'tbody tr[data-filter]' );
            filterBar.hidden = false;
            // Enter in the filter filters; it does not submit the step
            input.addEventListener( 'keydown', function( event )
            {
                if ( event.key === 'Enter' )
                    event.preventDefault();
            } );
            input.addEventListener( 'input', function()
            {
                var text = input.value.toLowerCase().trim(), shown = 0;
                Array.prototype.forEach.call( rows, function( row )
                {
                    var match = text === '' || row.getAttribute( 'data-filter' ).indexOf( text ) !== -1;
                    row.hidden = !match;
                    // a details row belongs to the row before it
                    var next = row.nextElementSibling;
                    if ( next && !next.hasAttribute( 'data-filter' ) )
                        next.hidden = !match;
                    if ( match )
                        shown++;
                } );
                if ( none )
                    none.hidden = shown !== 0;
            } );
        }
    } );
})();

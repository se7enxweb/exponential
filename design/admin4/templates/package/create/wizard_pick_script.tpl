{* The script of the pickers of the package creation wizard (.a4-package-pick: the classes, the extensions): it keeps
   "%count of %total selected" (.a4-package-pick-count, data-template) up to date and shows the filter
   (.a4-package-pick-filter, hidden without the script), which hides the rows whose data-filter does not hold the
   text typed. It posts nothing and changes no checkbox: a row hidden by the filter keeps its choice. Without the
   script the list is complete and the count is the one the page was drawn with. The same file is in design/admin
   and design/admin4. Guide: doc/features/6.0/package-wizard.md *}
{literal}
<script type="text/javascript">
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
</script>
{/literal}

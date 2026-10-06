{* "Select all on this page" and the selection count of the PDF export list (pdf/list). Without javascript the box
   is not shown and every button still submits the form. The search, the filter and the order are links and a form,
   so they need no script. Parameter: text_selected ("%count selected.").
   The same file is in design/admin and design/admin4. *}
{default text_selected=''}
<script type="text/javascript">
var expPdfListText = {ldelim} selected: '{$text_selected|wash( javascript )}' {rdelim};
{literal}
(function () {
    function each( selector, fn, root ) {
        var nodes = ( root || document ).querySelectorAll( selector ), i;
        for ( i = 0; i < nodes.length; i++ ) fn( nodes[i] );
    }
    each( '.exp-lists .exp-js-only', function ( el ) { el.hidden = false; } );

    function update() {
        each( '.exp-lists [data-select-all]', function ( all ) {
            var name = all.getAttribute( 'data-select-all' ), total = 0, ticked = 0;
            each( 'input[type="checkbox"]', function ( box ) {
                if ( box.name !== name ) return;
                total++;
                if ( box.checked ) ticked++;
                var card = box.closest( '.exp-sec' );
                if ( card ) card.classList.toggle( 'is-selected', box.checked );
            }, all.form || document );
            all.checked = total > 0 && ticked === total;
            all.indeterminate = ticked > 0 && ticked < total;
        } );
        each( '.exp-lists .exp-selected-count', function ( out ) {
            var name = out.getAttribute( 'data-for' ), n = 0;
            each( 'input[type="checkbox"]', function ( box ) { if ( box.name === name && box.checked ) n++; }, out.closest( 'form' ) || document );
            out.textContent = n ? expPdfListText.selected.split( '%count' ).join( n ) : '';
        } );
    }
    each( '.exp-lists [data-select-all]', function ( all ) {
        all.addEventListener( 'change', function () {
            var name = all.getAttribute( 'data-select-all' );
            each( 'input[type="checkbox"]', function ( box ) { if ( box.name === name ) box.checked = all.checked; }, all.form || document );
            update();
        } );
    } );
    each( '.exp-lists [data-list]', function ( list ) { list.addEventListener( 'change', update ); } );
    update();
})();
{/literal}
</script>

{* The search, the filter and the selection of the redesigned lists (rss/list, workflow/grouplist,
   class/grouplist, trigger/list), in one place per module so that each page keeps working on its own.

   Works on what the page marks: the lists (data-list) with one card per row (data-search, data-<flag>="1|0"), a
   search field (input[type=search]), filter chips (input[data-filter], value "" for all, "flag" for data-flag="1",
   "!flag" for data-flag="0"), "select all" boxes (data-select-all="<checkbox name>") and selection counts
   (.exp-selected-count[data-for="<checkbox name>"]). Without javascript none of the controls are shown, every
   card is, and every button still submits its form.

   Parameters: text_shown ("%shown of %count ... shown"), text_all ("%count ..."), text_selected ("%count selected."). *}
{default text_shown='' text_all='' text_selected=''}
<script type="text/javascript">
var expListText = {ldelim}
    shown: '{$text_shown|wash( javascript )}',
    allShown: '{$text_all|wash( javascript )}',
    selected: '{$text_selected|wash( javascript )}'
{rdelim};
{literal}
(function () {
    function each( selector, fn, root ) {
        var nodes = ( root || document ).querySelectorAll( selector ), i;
        for ( i = 0; i < nodes.length; i++ ) fn( nodes[i], i );
    }
    function tr( name, values ) {
        var s = expListText[name] || '', key;
        for ( key in values || {} ) s = s.split( '%' + key ).join( values[key] );
        return s;
    }
    each( '.exp-lists .exp-js-only', function ( el ) { el.hidden = false; } );

    var searchEl = document.querySelector( '.exp-lists input[type="search"]' );
    var countEl = document.querySelector( '.exp-lists .exp-filter-count' );

    function chosen() {
        var picked = document.querySelector( '.exp-lists input[data-filter]:checked' );
        return picked ? picked.value : '';
    }

    // The search and the filter together decide which cards are shown; hidden cards keep their tick.
    function applyFilter() {
        var words = searchEl ? searchEl.value.toLowerCase().split( /\s+/ ).filter( Boolean ) : [];
        var state = chosen(), shown = 0, total = 0;
        var negate = state.charAt( 0 ) === '!', flag = negate ? state.slice( 1 ) : state;
        each( '.exp-lists [data-list]', function ( list ) {
            var listShown = 0;
            each( ':scope > [data-search]', function ( card ) {
                total++;
                var hay = card.getAttribute( 'data-search' ) || '', ok = true, i;
                if ( flag !== '' ) ok = card.getAttribute( 'data-' + flag ) === ( negate ? '0' : '1' );
                for ( i = 0; ok && i < words.length; i++ ) ok = hay.indexOf( words[i] ) !== -1;
                card.hidden = !ok;
                if ( ok ) { shown++; listShown++; }
            }, list );
            var noMatch = list.nextElementSibling;
            if ( noMatch && noMatch.classList.contains( 'exp-no-match' ) ) noMatch.hidden = listShown !== 0;
        } );
        each( '.exp-lists .exp-no-match[data-global]', function ( el ) { el.hidden = shown !== 0; } );
        if ( countEl ) countEl.textContent = shown === total ? tr( 'allShown', { count: total } ) : tr( 'shown', { shown: shown, count: total } );
        updateSelection();
    }

    // The selection: a count beside the remove button, and a tick for every card shown.
    function updateSelection() {
        each( '.exp-lists [data-select-all]', function ( all ) {
            var name = all.getAttribute( 'data-select-all' ), visible = 0, ticked = 0;
            var form = all.form || document;
            each( 'input[type="checkbox"]', function ( box ) {
                if ( box.name !== name ) return;
                var card = box.closest( '[data-search]' );
                if ( card ) card.classList.toggle( 'is-selected', box.checked );
                if ( card && !card.hidden ) { visible++; if ( box.checked ) ticked++; }
            }, form );
            all.checked = visible > 0 && ticked === visible;
            all.indeterminate = ticked > 0 && ticked < visible;
        } );
        each( '.exp-lists .exp-selected-count', function ( out ) {
            var name = out.getAttribute( 'data-for' ), n = 0;
            var form = out.closest( 'form' ) || document;
            each( 'input[type="checkbox"]', function ( box ) { if ( box.name === name && box.checked ) n++; }, form );
            out.textContent = n ? tr( 'selected', { count: n } ) : '';
        } );
    }

    each( '.exp-lists [data-select-all]', function ( all ) {
        all.addEventListener( 'change', function () {
            var name = all.getAttribute( 'data-select-all' );
            each( 'input[type="checkbox"]', function ( box ) {
                if ( box.name !== name ) return;
                var card = box.closest( '[data-search]' );
                if ( card && !card.hidden ) box.checked = all.checked;
            }, all.form || document );
            updateSelection();
        } );
    } );
    each( '.exp-lists [data-list]', function ( list ) { list.addEventListener( 'change', updateSelection ); } );
    if ( searchEl ) searchEl.addEventListener( 'input', applyFilter );
    each( '.exp-lists input[data-filter]', function ( radio ) { radio.addEventListener( 'change', applyFilter ); } );
    applyFilter();
})();
{/literal}
</script>

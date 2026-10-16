/*
 * admin4 lists (rss/list, pdf/list, workflow/grouplist, class/grouplist, class/classlist, trigger/list): the search,
 * the filter and the selection, loaded by <module>/exp_list_script.tpl. A file of its own, so the pages run under a
 * Content Security Policy without 'unsafe-inline' (doc/features/6.0/content-security-policy.md).
 *
 * The template puts a hidden element with data-exp-list right before the script; its attributes configure it:
 *   data-exp-list-scope="<selector>"   the part of the page the script works on (".a4-page", ".a4-trigger-lists")
 *   data-exp-list-prefix="<prefix>"    the class prefix of <prefix>-js-only, <prefix>-no-match and
 *                                      <prefix>-selected-count ("exp", "a4-trigger", "a4-workflow")
 *   data-exp-list-filter="1|0"         1: search and filter chips; a card is a [data-search] row, and the selection
 *                                      only counts and ticks the cards shown. 0: selection only (pdf/list); the card
 *                                      of a box is its .a4-card, and every box counts
 *   data-text-shown, data-text-all, data-text-selected   "%shown of %count ... shown", "%count ...", "%count selected."
 */
(function () {
    var configs = document.querySelectorAll( '[data-exp-list]' );
    if ( !configs.length ) return;
    var config = configs[configs.length - 1];

    var scope = config.getAttribute( 'data-exp-list-scope' ) || '.a4-page';
    var prefix = config.getAttribute( 'data-exp-list-prefix' ) || 'exp';
    var filtered = config.getAttribute( 'data-exp-list-filter' ) !== '0';
    var expListText = {
        shown: config.getAttribute( 'data-text-shown' ) || '',
        allShown: config.getAttribute( 'data-text-all' ) || '',
        selected: config.getAttribute( 'data-text-selected' ) || ''
    };

    function each( selector, fn, root ) {
        var nodes = ( root || document ).querySelectorAll( selector ), i;
        for ( i = 0; i < nodes.length; i++ ) fn( nodes[i], i );
    }
    function tr( name, values ) {
        var s = expListText[name] || '', key;
        for ( key in values || {} ) s = s.split( '%' + key ).join( values[key] );
        return s;
    }
    each( scope + ' .' + prefix + '-js-only', function ( el ) { el.hidden = false; } );

    var searchEl = filtered ? document.querySelector( scope + ' input[type="search"]' ) : null;
    var countEl = filtered ? document.querySelector( scope + ' .a4-filter-count' ) : null;

    function chosen() {
        var picked = document.querySelector( scope + ' input[data-filter]:checked' );
        return picked ? picked.value : '';
    }

    // The search and the filter together decide which cards are shown; hidden cards keep their tick.
    function applyFilter() {
        var words = searchEl ? searchEl.value.toLowerCase().split( /\s+/ ).filter( Boolean ) : [];
        var state = chosen(), shown = 0, total = 0;
        var negate = state.charAt( 0 ) === '!', flag = negate ? state.slice( 1 ) : state;
        each( scope + ' [data-list]', function ( list ) {
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
            if ( noMatch && noMatch.classList.contains( prefix + '-no-match' ) ) noMatch.hidden = listShown !== 0;
        } );
        each( scope + ' .' + prefix + '-no-match[data-global]', function ( el ) { el.hidden = shown !== 0; } );
        if ( countEl ) countEl.textContent = shown === total ? tr( 'allShown', { count: total } ) : tr( 'shown', { shown: shown, count: total } );
        updateSelection();
    }

    // The selection: a count beside the remove button, and a tick for every card shown.
    function updateSelection() {
        each( scope + ' [data-select-all]', function ( all ) {
            var name = all.getAttribute( 'data-select-all' ), visible = 0, ticked = 0;
            var form = all.form || document;
            each( 'input[type="checkbox"]', function ( box ) {
                if ( box.name !== name ) return;
                var card = box.closest( filtered ? '[data-search]' : '.a4-card' );
                if ( card ) card.classList.toggle( 'is-selected', box.checked );
                if ( !filtered || ( card && !card.hidden ) ) { visible++; if ( box.checked ) ticked++; }
            }, form );
            all.checked = visible > 0 && ticked === visible;
            all.indeterminate = ticked > 0 && ticked < visible;
        } );
        each( scope + ' .' + prefix + '-selected-count', function ( out ) {
            var name = out.getAttribute( 'data-for' ), n = 0;
            var form = out.closest( 'form' ) || document;
            each( 'input[type="checkbox"]', function ( box ) { if ( box.name === name && box.checked ) n++; }, form );
            out.textContent = n ? tr( 'selected', { count: n } ) : '';
        } );
    }

    each( scope + ' [data-select-all]', function ( all ) {
        all.addEventListener( 'change', function () {
            var name = all.getAttribute( 'data-select-all' );
            each( 'input[type="checkbox"]', function ( box ) {
                if ( box.name !== name ) return;
                var card = box.closest( '[data-search]' );
                if ( !filtered || ( card && !card.hidden ) ) box.checked = all.checked;
            }, all.form || document );
            updateSelection();
        } );
    } );
    each( scope + ' [data-list]', function ( list ) { list.addEventListener( 'change', updateSelection ); } );
    if ( !filtered )
    {
        updateSelection();
        return;
    }
    if ( searchEl ) searchEl.addEventListener( 'input', applyFilter );
    each( scope + ' input[data-filter]', function ( radio ) { radio.addEventListener( 'change', applyFilter ); } );
    applyFilter();
})();

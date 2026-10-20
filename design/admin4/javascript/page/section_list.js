/*
 * admin4 section/list: the filter and the selection of the section list.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md). The texts come from #section-list-data.
 */
var expSectionListData = document.getElementById( 'section-list-data' );
var expSectionText = {
    shown: expSectionListData.getAttribute( 'data-section-text-shown' ),
    allShown: expSectionListData.getAttribute( 'data-section-text-all-shown' ),
    selected: expSectionListData.getAttribute( 'data-section-text-selected' )
};
(function () {
    var list = document.getElementById( 'section-list' );
    function each( selector, fn ) {
        var nodes = document.querySelectorAll( selector ), i;
        for ( i = 0; i < nodes.length; i++ ) fn( nodes[i], i );
    }
    function tr( name, values ) {
        var s = expSectionText[name], key;
        for ( key in values || {} ) s = s.split( '%' + key ).join( values[key] );
        return s;
    }
    each( '.a4-page .exp-js-only', function ( el ) { el.hidden = false; } );
    if ( !list ) return;

    var searchEl = document.getElementById( 'section-search' );
    var countEl = document.getElementById( 'section-filter-count' );
    var noMatchEl = document.getElementById( 'section-no-match' );

    function chosen() {
        var picked = document.querySelector( 'input[name="SectionFilter"]:checked' );
        return picked ? picked.value : '';
    }

    // The search and the filter together decide which cards are shown; hidden cards keep their tick.
    function applyFilter() {
        var words = searchEl ? searchEl.value.toLowerCase().split( /\s+/ ).filter( Boolean ) : [];
        var state = chosen(), shown = 0, total = 0;
        each( '#section-list > .a4-card', function ( card ) {
            total++;
            var hay = card.getAttribute( 'data-search' ) || '', ok = true, i;
            if ( state === 'content' ) ok = card.getAttribute( 'data-published' ) !== '0';
            else if ( state === 'empty' ) ok = card.getAttribute( 'data-published' ) === '0';
            else if ( state === 'roles' ) ok = card.getAttribute( 'data-roles' ) === '1';
            else if ( state === 'removable' ) ok = card.getAttribute( 'data-removable' ) === '1';
            else if ( state === 'attention' ) ok = card.getAttribute( 'data-attention' ) === '1';
            for ( i = 0; ok && i < words.length; i++ ) ok = hay.indexOf( words[i] ) !== -1;
            card.hidden = !ok;
            if ( ok ) shown++;
        } );
        if ( countEl ) countEl.textContent = shown === total ? tr( 'allShown', { count: total } ) : tr( 'shown', { shown: shown, count: total } );
        if ( noMatchEl ) noMatchEl.hidden = shown !== 0;
    }
    if ( searchEl ) searchEl.addEventListener( 'input', applyFilter );
    each( 'input[name="SectionFilter"]', function ( radio ) { radio.addEventListener( 'change', applyFilter ); } );
    applyFilter();

    // The selection: a count beside Remove selected, and a tick for every card shown.
    var selectAll = document.getElementById( 'section-select-all' );
    var selectedEl = document.getElementById( 'section-selected-count' );
    function boxes() { return list.querySelectorAll( 'input[name="SectionIDArray[]"]' ); }
    function updateSelection() {
        var all = boxes(), n = 0, visible = 0, visibleTicked = 0, i;
        for ( i = 0; i < all.length; i++ ) {
            var card = all[i].closest( '.a4-card' );
            if ( card ) card.classList.toggle( 'is-selected', all[i].checked );
            if ( all[i].checked ) n++;
            if ( card && !card.hidden ) { visible++; if ( all[i].checked ) visibleTicked++; }
        }
        if ( selectedEl ) selectedEl.textContent = n ? tr( 'selected', { count: n } ) : '';
        if ( selectAll ) { selectAll.checked = visible > 0 && visibleTicked === visible; selectAll.indeterminate = visibleTicked > 0 && visibleTicked < visible; }
    }
    list.addEventListener( 'change', updateSelection );
    if ( selectAll ) selectAll.addEventListener( 'change', function () {
        var all = boxes(), i;
        for ( i = 0; i < all.length; i++ ) {
            var card = all[i].closest( '.a4-card' );
            if ( card && !card.hidden ) all[i].checked = selectAll.checked;
        }
        updateSelection();
    } );
    if ( searchEl ) searchEl.addEventListener( 'input', updateSelection );
    each( 'input[name="SectionFilter"]', function ( radio ) { radio.addEventListener( 'change', updateSelection ); } );
    updateSelection();
})();

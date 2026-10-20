/*
 * The global URL aliases (content/urlalias_global.tpl): the parts that need javascript are shown, the field with
 * an error or the alias field gets the focus, and ticking aliases updates the selection count and the select all box.
 * The text of the count is data-text-selected of #alias-selected-count.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md).
 */
(function () {
    var nodes = document.querySelectorAll( '.a4-url .a4-url-js-only' ), i;
    for ( i = 0; i < nodes.length; i++ ) nodes[i].hidden = false;
    var list = document.getElementById( 'alias-list' );
    var source = document.getElementById( 'ezcontent_urlalias_global_source' );
    // the field with the error, else the alias field when the page is about creating one
    var invalid = document.querySelector( '.a4-url [aria-invalid="true"]' );
    if ( invalid ) invalid.focus();
    else if ( source ) source.focus();
    if ( !list ) return;
    var selectAll = document.getElementById( 'alias-select-all' );
    var countEl = document.getElementById( 'alias-selected-count' );
    function boxes() { return list.querySelectorAll( 'input[name="ElementList[]"]' ); }
    function update() {
        var all = boxes(), n = 0, j;
        for ( j = 0; j < all.length; j++ ) {
            var card = all[j].closest( '.a4-card' );
            if ( card ) card.classList.toggle( 'is-selected', all[j].checked );
            if ( all[j].checked ) n++;
        }
        if ( countEl ) countEl.textContent = n ? countEl.getAttribute( 'data-text-selected' ).split( '%count' ).join( n ) : '';
        if ( selectAll ) { selectAll.checked = n > 0 && n === all.length; selectAll.indeterminate = n > 0 && n < all.length; }
    }
    list.addEventListener( 'change', update );
    if ( selectAll ) selectAll.addEventListener( 'change', function () {
        var all = boxes(), j;
        for ( j = 0; j < all.length; j++ ) all[j].checked = selectAll.checked;
        update();
    } );
    update();
})();

/*
 * The URL wildcards (content/urlalias_wildcard.tpl): the parts that need javascript are shown, the field with an
 * error gets the focus, and ticking wildcards updates the selection count and the select all box. The text of the
 * count is data-text-selected of #wildcard-selected-count.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/en/us/features/6.0/content-security-policy.md).
 */
(function () {
    var nodes = document.querySelectorAll( '.a4-url .a4-url-js-only' ), i;
    for ( i = 0; i < nodes.length; i++ ) nodes[i].hidden = false;
    var invalid = document.querySelector( '.a4-url [aria-invalid="true"]' );
    if ( invalid ) invalid.focus();
    var list = document.getElementById( 'wildcard-list' );
    if ( !list ) return;
    var selectAll = document.getElementById( 'wildcard-select-all' );
    var countEl = document.getElementById( 'wildcard-selected-count' );
    function boxes() { return list.querySelectorAll( 'input[name="WildcardIDList[]"]' ); }
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

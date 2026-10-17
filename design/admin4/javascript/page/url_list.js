/*
 * admin4 url/list: the selection count of the address list.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/features/6.0/content-security-policy.md). The text comes from #url-list-data.
 */
var expUrlListData = document.getElementById( 'url-list-data' );
var expUrlListText = {
    selected: expUrlListData.getAttribute( 'data-url-list-text-selected' )
};
(function () {
    var list = document.getElementById( 'url-list' );
    var nodes = document.querySelectorAll( '.a4-url .a4-url-js-only' ), i;
    for ( i = 0; i < nodes.length; i++ ) nodes[i].hidden = false;
    if ( !list ) return;
    var selectAll = document.getElementById( 'url-select-all' );
    var countEl = document.getElementById( 'url-selected-count' );
    function boxes() { return list.querySelectorAll( 'input[name="URLSelection[]"]' ); }
    function update() {
        var all = boxes(), n = 0, j;
        for ( j = 0; j < all.length; j++ ) {
            var card = all[j].closest( '.a4-card' );
            if ( card ) card.classList.toggle( 'is-selected', all[j].checked );
            if ( all[j].checked ) n++;
        }
        if ( countEl ) countEl.textContent = n ? expUrlListText.selected.split( '%count' ).join( n ) : '';
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

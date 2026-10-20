/*
 * admin4 role/list: the "select all" box and the selection count of the role list.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/en/us/features/6.0/content-security-policy.md). The texts come from #role-list-data.
 */
var expRoleListData = document.getElementById( 'role-list-data' );
var expRoleListText = {
    selected: expRoleListData.getAttribute( 'data-role-list-text-selected' ),
    none: expRoleListData.getAttribute( 'data-role-list-text-none' )
};
(function () {
    var list = document.getElementById( 'role-list' );
    var nodes = document.querySelectorAll( '.a4-page .exp-js-only' ), i;
    for ( i = 0; i < nodes.length; i++ ) nodes[i].hidden = false;
    if ( !list ) return;
    var selectAll = document.getElementById( 'role-select-all' );
    var countEl = document.getElementById( 'role-selected-count' );
    function boxes() { return list.querySelectorAll( 'input[name="DeleteIDArray[]"]' ); }
    function update() {
        var all = boxes(), n = 0, j;
        for ( j = 0; j < all.length; j++ ) {
            var card = all[j].closest( '.a4-card' );
            if ( card ) card.classList.toggle( 'is-selected', all[j].checked );
            if ( all[j].checked ) n++;
        }
        if ( countEl ) countEl.textContent = n ? expRoleListText.selected.split( '%count' ).join( n ) : expRoleListText.none;
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

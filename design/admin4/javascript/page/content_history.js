/*
 * The version history (content/history.tpl): the parts that need javascript are shown, the comparison switches its
 * view mode, and ticking versions updates the selection count, the list of versions the Remove button removes, and the
 * select all box. The texts are data-text-* attributes of #history-texts.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/en/us/features/6.0/content-security-policy.md).
 */
(function () {
    var texts = document.getElementById( 'history-texts' );
    var expHistoryText = {
        selected: texts ? texts.getAttribute( 'data-text-selected' ) : '',
        removeList: texts ? texts.getAttribute( 'data-text-remove-list' ) : '',
        removeNone: texts ? texts.getAttribute( 'data-text-remove-none' ) : ''
    };
    var nodes = document.querySelectorAll( '.a4-content-history .a4-content-history-js-only' ), i;
    for ( i = 0; i < nodes.length; i++ ) nodes[i].hidden = false;
    var modes = document.getElementById( 'history-diff-modes' ), view = document.getElementById( 'diffview' );
    if ( modes && view ) modes.addEventListener( 'click', function ( e ) {
        var b = e.target.closest( 'button[data-mode]' ); if ( !b ) return;
        view.className = b.getAttribute( 'data-mode' );
        var all = modes.querySelectorAll( 'button' ), j;
        for ( j = 0; j < all.length; j++ ) { all[j].classList.toggle( 'is-current', all[j] === b ); all[j].setAttribute( 'aria-pressed', all[j] === b ? 'true' : 'false' ); }
    } );
    var list = document.getElementById( 'history-list' );
    if ( !list ) return;
    var selectAll = document.getElementById( 'history-select-all' );
    var countEl = document.getElementById( 'history-selected-count' );
    var removeList = document.getElementById( 'history-remove-list' );
    var removeButton = document.getElementById( 'history-remove-button' );
    function boxes() { return list.querySelectorAll( 'input[name="DeleteIDArray[]"]' ); }
    function update() {
        var all = boxes(), ticked = [], j;
        for ( j = 0; j < all.length; j++ ) {
            var card = all[j].closest( '.a4-card' );
            if ( card ) card.classList.toggle( 'is-selected', all[j].checked );
            if ( all[j].checked ) ticked.push( all[j].getAttribute( 'data-version' ) );
        }
        if ( countEl ) countEl.textContent = ticked.length ? expHistoryText.selected.split( '%count' ).join( ticked.length ) : '';
        if ( removeList ) removeList.textContent = ticked.length ? expHistoryText.removeList.split( '%list' ).join( ticked.join( ', ' ) ) : expHistoryText.removeNone;
        if ( removeButton ) removeButton.disabled = !ticked.length;
        if ( selectAll ) { selectAll.checked = ticked.length > 0 && ticked.length === all.length; selectAll.indeterminate = ticked.length > 0 && ticked.length < all.length; }
    }
    list.addEventListener( 'change', update );
    if ( selectAll ) selectAll.addEventListener( 'change', function () {
        var all = boxes(), j;
        for ( j = 0; j < all.length; j++ ) all[j].checked = selectAll.checked;
        update();
    } );
    update();
})();

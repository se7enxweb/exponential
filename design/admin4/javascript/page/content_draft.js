/*
 * The drafts page (content/draft.tpl): the parts that need javascript are shown, and ticking drafts updates the
 * selection count, the list of drafts the Remove button removes, and the select all box. The texts are data-text-*
 * attributes of #draft-texts.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/en/us/features/6.0/content-security-policy.md).
 */
(function () {
    var texts = document.getElementById( 'draft-texts' );
    var expDraftText = {
        selected: texts ? texts.getAttribute( 'data-text-selected' ) : '',
        removeList: texts ? texts.getAttribute( 'data-text-remove-list' ) : '',
        removeNone: texts ? texts.getAttribute( 'data-text-remove-none' ) : ''
    };
    var nodes = document.querySelectorAll( '.a4-content-drafts .a4-content-history-js-only' ), i;
    for ( i = 0; i < nodes.length; i++ ) nodes[i].hidden = false;
    var list = document.getElementById( 'draft-list' );
    if ( !list ) return;
    var selectAll = document.getElementById( 'draft-select-all' );
    var countEl = document.getElementById( 'draft-selected-count' );
    var removeList = document.getElementById( 'draft-remove-list' );
    var removeButton = document.getElementById( 'draft-remove-button' );
    function boxes() { return list.querySelectorAll( 'input[name="DeleteIDArray[]"]' ); }
    function update() {
        var all = boxes(), ticked = [], j;
        for ( j = 0; j < all.length; j++ ) {
            var card = all[j].closest( '.a4-card' );
            if ( card ) card.classList.toggle( 'is-selected', all[j].checked );
            if ( all[j].checked ) ticked.push( all[j].getAttribute( 'data-name' ) );
        }
        if ( countEl ) countEl.textContent = ticked.length ? expDraftText.selected.split( '%count' ).join( ticked.length ) : '';
        if ( removeList ) removeList.textContent = ticked.length ? expDraftText.removeList.split( '%list' ).join( ticked.join( ', ' ) ) : expDraftText.removeNone;
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

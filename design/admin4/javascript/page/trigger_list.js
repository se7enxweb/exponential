/*
 * admin4 trigger/list: counts the unsaved changes of the trigger list.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md). The text comes from #trigger-list-data.
 */
var expTriggerListData = document.getElementById( 'trigger-list-data' );
var expTriggerChanged = expTriggerListData.getAttribute( 'data-trigger-changed' );
(function () {
    var out = document.getElementById( 'trigger-changed-count' );
    var selects = document.querySelectorAll( '.a4-trigger-lists select[data-initial]' );
    function update() {
        var n = 0, i;
        for ( i = 0; i < selects.length; i++ ) {
            var changed = selects[i].value !== selects[i].getAttribute( 'data-initial' );
            var row = selects[i].closest( 'tr' );
            if ( row ) row.classList.toggle( 'is-changed', changed );
            if ( changed ) n++;
        }
        if ( out ) out.textContent = n ? expTriggerChanged.split( '%count' ).join( n ) : '';
    }
    for ( var i = 0; i < selects.length; i++ ) selects[i].addEventListener( 'change', update );
})();

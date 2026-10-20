/*
 * admin4 user/unactivated: the selection count of the unactivated accounts.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/en/us/features/6.0/content-security-policy.md). The text comes from #user-unactivated-data.
 */
var expUserUnactivatedData = document.getElementById( 'user-unactivated-data' );
var expUnactText = {
    selected: expUserUnactivatedData.getAttribute( 'data-unact-text-selected' )
};
(function () {
    var table = document.getElementById( 'unact-list' ), all = document.getElementById( 'unact-select-all' ), out = document.getElementById( 'unact-selected-count' );
    if ( !table ) return;
    function boxes() { return table.querySelectorAll( 'input[name="DeleteIDArray[]"]' ); }
    function update() {
        var b = boxes(), n = 0, i;
        for ( i = 0; i < b.length; i++ ) if ( b[i].checked ) n++;
        if ( out ) out.textContent = n ? expUnactText.selected.split( '%count' ).join( n ) : '';
        if ( all ) { all.checked = n > 0 && n === b.length; all.indeterminate = n > 0 && n < b.length; }
    }
    if ( all ) { all.hidden = false; all.addEventListener( 'change', function () { var b = boxes(), i; for ( i = 0; i < b.length; i++ ) b[i].checked = all.checked; update(); } ); }
    table.addEventListener( 'change', update );
})();

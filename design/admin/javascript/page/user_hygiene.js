/*
 * admin4 user/hygiene: the selection count of the account list.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md). The text comes from #user-hygiene-data.
 */
var expUserHygieneData = document.getElementById( 'user-hygiene-data' );
var expHygieneText = {
    selected: expUserHygieneData.getAttribute( 'data-hygiene-text-selected' )
};
(function () {
    var table = document.getElementById( 'hygiene-list' ), all = document.getElementById( 'hygiene-select-all' ), out = document.getElementById( 'hygiene-selected-count' );
    if ( !table ) return;
    function boxes() { return table.querySelectorAll( 'input[name="UserIDArray[]"]' ); }
    function update() {
        var b = boxes(), n = 0, i;
        for ( i = 0; i < b.length; i++ ) if ( b[i].checked ) n++;
        if ( out ) out.textContent = n ? expHygieneText.selected.split( '%count' ).join( n ) : '';
        if ( all ) { all.checked = n > 0 && n === b.length; all.indeterminate = n > 0 && n < b.length; }
    }
    if ( all ) { all.hidden = false; all.addEventListener( 'change', function () { var b = boxes(), i; for ( i = 0; i < b.length; i++ ) b[i].checked = all.checked; update(); } ); }
    table.addEventListener( 'change', update );
})();

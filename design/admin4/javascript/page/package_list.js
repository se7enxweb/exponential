/*
 * admin4 package/list: "Select all" over the package checkboxes, and the selected cards marked. A file of its own, so
 * the page runs under a Content Security Policy without 'unsafe-inline' (doc/en/us/features/6.0/content-security-policy.md).
 */
(function () {
    var all = document.getElementById( 'package-select-all' );
    var list = document.getElementById( 'package-list' );
    if ( !all || !list ) return;
    var boxes = list.querySelectorAll( 'input[name="PackageSelection[]"]:not([disabled])' );
    if ( !boxes.length ) return;
    all.parentNode.hidden = false;
    function sync() {
        var n = 0;
        for ( var i = 0; i < boxes.length; i++ ) { if ( boxes[i].checked ) n++; boxes[i].closest( '.a4-card' ).classList.toggle( 'is-selected', boxes[i].checked ); }
        all.checked = n === boxes.length; all.indeterminate = n > 0 && n < boxes.length;
    }
    all.addEventListener( 'change', function () { for ( var i = 0; i < boxes.length; i++ ) boxes[i].checked = all.checked; sync(); } );
    for ( var i = 0; i < boxes.length; i++ ) boxes[i].addEventListener( 'change', sync );
    sync();
})();

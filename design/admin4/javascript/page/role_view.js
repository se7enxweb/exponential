/*
 * admin4 role/view: the "select all" checkbox of the assignments.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md).
 */
(function () {
    var all = document.getElementById( 'role-assign-all' ), table = document.getElementById( 'role-assignments' );
    if ( !all || !table ) return;
    all.hidden = false;
    all.addEventListener( 'change', function () {
        var boxes = table.querySelectorAll( 'input[name="IDArray[]"]' ), i;
        for ( i = 0; i < boxes.length; i++ ) boxes[i].checked = all.checked;
    } );
})();

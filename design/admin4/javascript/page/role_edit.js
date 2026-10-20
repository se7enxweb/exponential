/*
 * admin4 role/edit: shows when the draft has unsaved changes and asks before the page is left with them.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/en/us/features/6.0/content-security-policy.md). The text and the draft state come from data attributes on #roleedit.
 */
(function () {
    var form = document.getElementById( 'roleedit' ), name = document.getElementById( 'roleName' );
    if ( !form || !name ) return;
    var expRoleEditText = { leave: form.getAttribute( 'data-text-leave' ) };
    var expRoleEditDiffers = form.getAttribute( 'data-differs' ) === '1';
    var initial = name.value, submitting = false, dirtyEl = document.getElementById( 'role-edit-dirty' );
    function dirty() { return expRoleEditDiffers || name.value !== initial; }
    name.addEventListener( 'input', function () { if ( dirtyEl ) dirtyEl.hidden = !dirty(); } );
    form.addEventListener( 'submit', function () { submitting = true; } );
    // Leaving by a link, the address bar or closing the tab, not by Save, Cancel or another button of the form
    window.addEventListener( 'beforeunload', function ( e ) {
        if ( submitting || !dirty() ) return;
        e.preventDefault();
        e.returnValue = expRoleEditText.leave;
        return expRoleEditText.leave;
    } );
    if ( !expRoleEditDiffers && !document.getElementById( 'role-edit-moved' ) ) { name.focus(); name.select(); }
})();

/*
 * moderation/recent (Dashboard > Recent content): "Select all on this page", the number of selected items, and only the
 * fields the chosen action needs (the reason for a hold or a release, the section, the state). Without it every field
 * is shown and every box is ticked by hand; the form works the same. A file of its own, so the page runs under a
 * Content Security Policy without 'unsafe-inline' (doc/eng-us/features/6.0/content-security-policy.md); the same file
 * in design/admin and design/admin4.
 */
(function () {
    'use strict';
    var form = document.getElementById( 'moderation-form' );
    if ( !form ) return;
    var boxes = function () { return form.querySelectorAll( 'input[type=checkbox][name="ObjectIDList[]"]' ); };
    var all = form.querySelector( '[data-moderation-all]' );
    var count = form.querySelector( '[data-moderation-count]' );
    var action = document.getElementById( 'moderation-action' );

    function update() {
        var list = boxes(), ticked = 0, i;
        for ( i = 0; i < list.length; i++ ) if ( list[i].checked ) ticked++;
        if ( all ) {
            all.checked = list.length > 0 && ticked === list.length;
            all.indeterminate = ticked > 0 && ticked < list.length;
        }
        if ( count ) count.textContent = ( count.getAttribute( 'data-text' ) || '%count' ).replace( '%count', String( ticked ) );
    }

    function fields() {
        var chosen = action ? action.value : '';
        var parts = form.querySelectorAll( '[data-moderation-for]' ), i;
        for ( i = 0; i < parts.length; i++ ) {
            var names = ( parts[i].getAttribute( 'data-moderation-for' ) || '' ).split( ' ' );
            parts[i].hidden = names.indexOf( chosen ) < 0;
        }
    }

    var shown = form.querySelectorAll( '.exp-js-only' ), i;
    for ( i = 0; i < shown.length; i++ ) shown[i].hidden = false;
    if ( all ) all.addEventListener( 'change', function () {
        var list = boxes(), j;
        for ( j = 0; j < list.length; j++ ) list[j].checked = all.checked;
        update();
    } );
    form.addEventListener( 'change', function ( e ) {
        if ( e.target && e.target.name === 'ObjectIDList[]' ) update();
    } );
    if ( action ) action.addEventListener( 'change', fields );
    update();
    fields();
})();

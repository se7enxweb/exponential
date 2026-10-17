/*
 * admin4 notification settings, subtree handler (notification/handler/ezsubtree/settings/edit.tpl): "Select all on
 * this page" (data-nf-select-all) ticks or clears every rule of the list as it is itself ticked or cleared. A file of
 * its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/features/6.0/content-security-policy.md).
 */
document.addEventListener( 'click', function ( event )
{
    var all = event.target;
    if ( !all || !all.hasAttribute || !all.hasAttribute( 'data-nf-select-all' ) ) return;
    var b = document.querySelectorAll( '#nf-rules .a4-notification-pick' );
    for ( var i = 0; i < b.length; i++ ) b[i].checked = all.checked;
} );

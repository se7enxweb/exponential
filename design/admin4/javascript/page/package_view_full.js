/*
 * admin4 package/view/full: "No wrap" (data-wrap-toggle) switches the long lines of the file viewer between wrapped
 * and scrolled, and says so in aria-pressed. A file of its own, so the page runs under a Content Security Policy
 * without 'unsafe-inline' (doc/en/us/features/6.0/content-security-policy.md).
 */
document.addEventListener( 'click', function ( event )
{
    var b = event.target.closest ? event.target.closest( '[data-wrap-toggle]' ) : null;
    if ( !b ) return;
    var p = b.closest( '.pvf-viewer' ).querySelectorAll( 'pre.pvf-code' ), on = b.getAttribute( 'aria-pressed' ) !== 'true';
    for ( var i = 0; i < p.length; i++ ) p[i].classList.toggle( 'pvf-nowrap', on );
    b.setAttribute( 'aria-pressed', on ? 'true' : 'false' );
} );

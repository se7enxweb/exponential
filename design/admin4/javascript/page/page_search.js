/*
 * The header search (page_search.tpl): the scope popup of each search box on the page.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/en/us/features/6.0/content-security-policy.md).
 */
/* One handler for every header search on the page (the header's and the narrow screen menu's): each works on its
   own field and popup. The popup opens from the scope button, under the field; Escape, a click outside it or the
   close button shut it; choosing where to search shows in the field's hint, the typed text stays. */
(function() {
    if ( window.expHeaderSearch ) return;
    window.expHeaderSearch = true;
    function blockOf( el ) { return el && el.closest ? el.closest( '[data-searchblock]' ) : null; }
    function pane( block ) { return block.querySelector( '.searchscope-pane' ); }
    function toggle( block ) { return block.querySelector( '.searchscope' ); }
    function close( block, focusToggle ) {
        var p = pane( block ), t = toggle( block );
        if ( !p || p.hidden ) return;
        p.hidden = true;
        if ( t ) { t.setAttribute( 'aria-expanded', 'false' ); if ( focusToggle ) t.focus(); }
    }
    function closeAll( except ) {
        document.querySelectorAll( '[data-searchblock]' ).forEach( function( b ) { if ( b !== except ) close( b, false ); } );
    }
    function open( block ) {
        var p = pane( block ), t = toggle( block );
        if ( !p ) return;
        closeAll( block );
        p.hidden = false;
        if ( t ) t.setAttribute( 'aria-expanded', 'true' );
        var first = p.querySelector( 'input:checked:not([disabled]), select:not([disabled]), input:not([disabled])' );
        if ( first ) first.focus();
    }
    document.addEventListener( 'click', function( e ) {
        var block = blockOf( e.target );
        if ( e.target.closest && e.target.closest( '.searchscope' ) && block ) {
            e.preventDefault();
            if ( pane( block ).hidden ) open( block ); else close( block, true );
            return;
        }
        if ( e.target.closest && e.target.closest( '.searchscope-pane .close' ) && block ) {
            e.preventDefault();
            close( block, true );
            return;
        }
        if ( !block || !( e.target.closest && e.target.closest( '.searchscope-pane' ) ) )
            closeAll( null );
    } );
    document.addEventListener( 'keydown', function( e ) {
        if ( e.key !== 'Escape' ) return;
        var block = blockOf( document.activeElement );
        if ( block ) close( block, true ); else closeAll( null );
    } );
    document.addEventListener( 'change', function( e ) {
        if ( !e.target.matches || !e.target.matches( '[data-searchblock] input[name=SubTreeArray]' ) ) return;
        var field = blockOf( e.target ).querySelector( 'input.searchtext' ), label = e.target.getAttribute( 'data-label' );
        if ( field && label ) { if ( !field.hasAttribute( 'data-short-placeholder' ) ) field.placeholder = label; field.title = label; field.setAttribute( 'aria-label', label ); }
    } );
})();

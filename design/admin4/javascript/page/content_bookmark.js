/*
 * The bookmarks page (content/bookmark.tpl): select all and the selection count, and moving bookmarks and
 * folders by drag and drop or with the keyboard. The text of the count is data-text-selected of #bm-selected-count.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md).
 */
(function () {
    'use strict';
    var list = document.getElementById( 'bm-list' );
    var nav = document.getElementById( 'bm-folder-nav' );
    var dropForm = document.getElementById( 'bm-drop-form' ), orderForm = document.getElementById( 'bm-order-form' ), shiftForm = document.getElementById( 'bm-shift-form' );
    var arrange = !!document.getElementById( 'bm-grip-help' );
    var KEY = 'exp-bm-grip';
    function all( root, sel ) { return root ? Array.prototype.slice.call( root.querySelectorAll( sel ) ) : []; }
    function remember( value ) { try { sessionStorage.setItem( KEY, value ); } catch ( e ) {} }
    function submit( form ) { if ( form.requestSubmit ) form.requestSubmit(); else form.submit(); }

    // ---- Select all and the selection count ----
    var selectAll = document.getElementById( 'bm-select-all' );
    var countEl = document.getElementById( 'bm-selected-count' );
    var initialNote = countEl ? countEl.textContent : '';
    function boxes() { return all( list, 'input[name="DeleteIDArray[]"]' ); }
    function selected() { return boxes().filter( function ( b ) { return b.checked; } ); }
    function update() {
        var n = 0;
        boxes().forEach( function ( b ) {
            var card = b.closest( '.a4-card' );
            if ( card ) card.classList.toggle( 'is-selected', b.checked );
            if ( b.checked ) n++;
        } );
        if ( countEl ) countEl.textContent = n ? countEl.getAttribute( 'data-text-selected' ).split( '%count' ).join( n ) : initialNote;
        if ( selectAll ) { selectAll.checked = n > 0 && n === boxes().length; selectAll.indeterminate = n > 0 && n < boxes().length; }
    }
    if ( list ) {
        if ( selectAll ) selectAll.parentNode.hidden = false;
        list.addEventListener( 'change', update );
        if ( selectAll ) selectAll.addEventListener( 'change', function () { boxes().forEach( function ( b ) { b.checked = selectAll.checked; } ); update(); } );
        update();
    }
    if ( !nav || !dropForm || !orderForm || !( 'draggable' in document.createElement( 'span' ) ) ) return;

    // ---- The grips: visible with javascript; bookmark grips only in the user's own order ----
    all( list, '.a4-grip' ).forEach( function ( g ) { g.hidden = !arrange; } );
    all( nav, '.a4-content-bookmark-fgrip' ).forEach( function ( g ) { g.hidden = false; } );
    all( nav, 'li[data-folder]' ).forEach( function ( li ) { li.setAttribute( 'data-drop-label', nav.getAttribute( 'data-drop-label' ) || '' ); } );
    var hint = document.querySelector( '.a4-content-bookmark .a4-content-bookmark-draghint' );
    if ( hint ) hint.hidden = false;
    all( list, '.a4-card[data-bookmark]' ).forEach( function ( card ) { card.setAttribute( 'draggable', 'true' ); } );

    // ---- Dragging bookmarks: within their folder to arrange them, onto a folder of the list to move them into it ----
    var drag = null; // { card, ul, ids, start, reorder, dropped }
    function idsOf( ul ) { return all( ul, ':scope > .a4-card' ).map( function ( c ) { return c.getAttribute( 'data-bookmark' ); } ); }
    function clearNav() { all( nav, '.is-drop, .is-before, .is-after' ).forEach( function ( li ) { li.classList.remove( 'is-drop', 'is-before', 'is-after' ); } ); }
    if ( list ) {
        list.addEventListener( 'dragstart', function ( e ) {
            var card = e.target.closest ? e.target.closest( '.a4-card[data-bookmark]' ) : null;
            if ( !card ) return;
            var box = card.querySelector( 'input[name="DeleteIDArray[]"]' );
            var ids = ( box && box.checked ) ? selected().map( function ( b ) { return b.value; } ) : [ card.getAttribute( 'data-bookmark' ) ];
            var ul = card.parentNode;
            drag = { card: card, ul: ul, ids: ids, start: idsOf( ul ).join( ',' ), reorder: arrange && ids.length === 1, dropped: false };
            card.classList.add( 'is-dragged' );
            if ( drag.reorder ) ul.classList.add( 'is-arranging' );
            try { e.dataTransfer.effectAllowed = 'move'; e.dataTransfer.setData( 'text/plain', ids.join( ',' ) ); e.dataTransfer.setDragImage( card, 24, 20 ); } catch ( x ) {}
        } );
        list.addEventListener( 'dragover', function ( e ) {
            clearNav();
            if ( !drag || !drag.card || !drag.reorder ) return;
            var over = e.target.closest ? e.target.closest( '.a4-card[data-bookmark]' ) : null;
            if ( !over || over.parentNode !== drag.ul ) return;
            e.preventDefault();
            if ( over === drag.card ) return;
            var box = over.getBoundingClientRect();
            drag.ul.insertBefore( drag.card, e.clientY > box.top + box.height / 2 ? over.nextSibling : over );
        } );
        list.addEventListener( 'drop', function ( e ) { if ( drag && drag.reorder ) e.preventDefault(); } );
        list.addEventListener( 'dragend', function () {
            if ( !drag || !drag.card ) return;
            var d = drag; drag = null;
            d.card.classList.remove( 'is-dragged' );
            d.ul.classList.remove( 'is-arranging' );
            clearNav();
            if ( d.dropped || !d.reorder ) return;
            var now = idsOf( d.ul );
            if ( now.join( ',' ) === d.start ) return;
            orderForm.elements.OrderType.value = 'bookmark';
            orderForm.elements.OrderFolderID.value = d.ul.getAttribute( 'data-folder' );
            orderForm.elements.OrderIDs.value = now.join( ',' );
            remember( 'b:' + d.card.getAttribute( 'data-bookmark' ) );
            window.setTimeout( function () { submit( orderForm ); }, 30 );
        } );
        // the arrow keys on a grip: the card's own Move up or Move down button
        list.addEventListener( 'keydown', function ( e ) {
            var grip = e.target.closest ? e.target.closest( '.a4-grip' ) : null;
            if ( !grip || ( e.key !== 'ArrowUp' && e.key !== 'ArrowDown' ) ) return;
            e.preventDefault();
            var id = grip.getAttribute( 'data-bookmark' );
            var b = grip.closest( '.a4-card' ).querySelector( 'button[name="BookmarkShiftButton"][value="' + ( e.key === 'ArrowUp' ? 'up-' : 'down-' ) + id + '"]' );
            if ( !b || b.disabled ) return;
            remember( 'b:' + id );
            var form = document.getElementById( 'bm-list-form' );
            if ( form.requestSubmit ) form.requestSubmit( b ); else b.click();
        } );
    }

    // ---- The folder list: drop bookmarks into a folder; arrange folders among their neighbours ----
    var folderDrag = null; // { li, parent }
    function navItem( e ) { return e.target.closest ? e.target.closest( 'li[data-folder]' ) : null; }
    function siblings( parent ) { return all( nav, 'li[data-parent="' + parent + '"]' ); }
    nav.addEventListener( 'dragstart', function ( e ) {
        var grip = e.target.closest ? e.target.closest( '.a4-content-bookmark-fgrip' ) : null;
        if ( !grip ) return;
        var li = grip.closest( 'li[data-folder]' );
        folderDrag = { li: li, parent: li.getAttribute( 'data-parent' ) };
        li.classList.add( 'is-dragged' );
        try { e.dataTransfer.effectAllowed = 'move'; e.dataTransfer.setData( 'text/plain', 'folder:' + li.getAttribute( 'data-folder' ) ); } catch ( x ) {}
    } );
    nav.addEventListener( 'dragover', function ( e ) {
        var li = navItem( e );
        clearNav();
        if ( !li ) return;
        if ( drag ) {
            e.preventDefault();
            li.classList.add( 'is-drop' );
        } else if ( folderDrag && li !== folderDrag.li && li.getAttribute( 'data-parent' ) === folderDrag.parent ) {
            e.preventDefault();
            var box = li.getBoundingClientRect();
            li.classList.add( e.clientY > box.top + box.height / 2 ? 'is-after' : 'is-before' );
        }
    } );
    // leaving a child of the list also fires dragleave, often without relatedTarget (and at 0,0): clear only when
    // the pointer is known to be outside the list; dragging over the bookmarks or the end of a drag clears it too
    nav.addEventListener( 'dragleave', function ( e ) { if ( e.relatedTarget && !nav.contains( e.relatedTarget ) ) clearNav(); } );
    nav.addEventListener( 'drop', function ( e ) {
        var li = navItem( e );
        if ( !li ) return;
        if ( drag ) {
            e.preventDefault();
            drag.dropped = true;
            dropForm.elements.FolderID.value = li.getAttribute( 'data-folder' );
            drag.ids.forEach( function ( id ) {
                var input = document.createElement( 'input' );
                input.type = 'hidden'; input.name = 'BookmarkIDArray[]'; input.value = id;
                dropForm.appendChild( input );
            } );
            clearNav();
            window.setTimeout( function () { submit( dropForm ); }, 30 );
        } else if ( folderDrag && li !== folderDrag.li && li.getAttribute( 'data-parent' ) === folderDrag.parent ) {
            e.preventDefault();
            var after = li.classList.contains( 'is-after' );
            var ids = siblings( folderDrag.parent ).filter( function ( s ) { return s !== folderDrag.li; } ).map( function ( s ) { return s.getAttribute( 'data-folder' ); } );
            var at = ids.indexOf( li.getAttribute( 'data-folder' ) ) + ( after ? 1 : 0 );
            ids.splice( at, 0, folderDrag.li.getAttribute( 'data-folder' ) );
            orderForm.elements.OrderType.value = 'folder';
            orderForm.elements.OrderFolderID.value = folderDrag.parent;
            orderForm.elements.OrderIDs.value = ids.join( ',' );
            remember( 'f:' + folderDrag.li.getAttribute( 'data-folder' ) );
            clearNav();
            window.setTimeout( function () { submit( orderForm ); }, 30 );
        }
    } );
    nav.addEventListener( 'dragend', function () { if ( folderDrag ) folderDrag.li.classList.remove( 'is-dragged' ); folderDrag = null; clearNav(); } );
    nav.addEventListener( 'keydown', function ( e ) {
        var grip = e.target.closest ? e.target.closest( '.a4-content-bookmark-fgrip' ) : null;
        if ( !grip || ( e.key !== 'ArrowUp' && e.key !== 'ArrowDown' ) || !shiftForm ) return;
        e.preventDefault();
        var li = grip.closest( 'li[data-folder]' ), sib = siblings( li.getAttribute( 'data-parent' ) ), i = sib.indexOf( li );
        if ( ( e.key === 'ArrowUp' && i <= 0 ) || ( e.key === 'ArrowDown' && i === sib.length - 1 ) ) return;
        shiftForm.elements.BookmarkShiftButton.value = ( e.key === 'ArrowUp' ? 'fup-' : 'fdown-' ) + li.getAttribute( 'data-folder' );
        remember( 'f:' + li.getAttribute( 'data-folder' ) );
        submit( shiftForm );
    } );

    // ---- Back from a move: the moved entry's grip has the focus again ----
    var last = null;
    try { last = sessionStorage.getItem( KEY ); sessionStorage.removeItem( KEY ); } catch ( e ) {}
    if ( last ) {
        var kind = last.charAt( 0 ), id = last.slice( 2 ), target = null;
        if ( kind === 'b' && list ) target = list.querySelector( '.a4-grip[data-bookmark="' + id + '"]' );
        if ( kind === 'f' ) target = nav.querySelector( '.a4-content-bookmark-fgrip[data-folder="' + id + '"]' );
        if ( target && !target.hidden ) {
            target.focus();
            var holder = target.closest( '.a4-card, li[data-folder]' );
            if ( holder ) holder.classList.add( 'is-moved' );
        }
    }
})();

/*
 * admin4 role/edit: moves the policies by drag and drop and with the arrow keys on the grip.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md). The offset of the page comes from #role-edit-order.
 */
/* Drag and drop and the arrow keys on the grip. Both send the move the buttons send (MovePolicyTo with the place in
   the whole list, or MovePolicyUp / MovePolicyDown), so the server makes it in the draft; nothing is reordered only
   on screen. */
(function () {
    var form = document.getElementById( 'roleedit' ), list = document.getElementById( 'role-edit-policies' );
    var data = document.getElementById( 'role-edit-order' );
    if ( !form || !list || !data ) return;
    var expRoleOrder = { offset: parseInt( data.getAttribute( 'data-offset' ), 10 ) || 0, moved: data.getAttribute( 'data-text-moved' ) };
    var grips = list.querySelectorAll( '.a4-grip' ), i, dragged = null, startIndex = -1;
    for ( i = 0; i < grips.length; i++ ) grips[i].hidden = false;
    function items() { return Array.prototype.slice.call( list.querySelectorAll( ':scope > li.a4-role-policy' ) ); }
    function send( name, value, position ) {
        var b = document.createElement( 'button' );
        b.type = 'submit'; b.name = name; b.value = value; b.hidden = true;
        if ( position ) {
            var field = form.querySelector( 'input[name="MovePolicyPosition[' + value + ']"]' );
            if ( !field ) { field = document.createElement( 'input' ); field.type = 'hidden'; field.name = 'MovePolicyPosition[' + value + ']'; form.appendChild( field ); }
            field.value = position;
            try { sessionStorage.setItem( 'exp-role-grip', String( position ) ); } catch ( e ) {}
        }
        form.appendChild( b );
        if ( form.requestSubmit ) form.requestSubmit( b ); else b.click();
    }
    list.addEventListener( 'dragstart', function ( e ) {
        var grip = e.target.closest ? e.target.closest( '.a4-grip' ) : null;
        if ( !grip ) { e.preventDefault(); return; }
        dragged = grip.closest( 'li.a4-role-policy' );
        startIndex = items().indexOf( dragged );
        dragged.classList.add( 'is-dragging' );
        e.dataTransfer.effectAllowed = 'move';
        try { e.dataTransfer.setData( 'text/plain', grip.getAttribute( 'data-policy' ) ); e.dataTransfer.setDragImage( dragged, 20, 20 ); } catch ( x ) {}
    } );
    list.addEventListener( 'dragover', function ( e ) {
        if ( !dragged ) return;
        e.preventDefault();
        var over = e.target.closest ? e.target.closest( 'li.a4-role-policy' ) : null;
        if ( !over || over === dragged ) return;
        var box = over.getBoundingClientRect();
        list.insertBefore( dragged, e.clientY > box.top + box.height / 2 ? over.nextSibling : over );
    } );
    list.addEventListener( 'drop', function ( e ) { if ( dragged ) e.preventDefault(); } );
    list.addEventListener( 'dragend', function () {
        if ( !dragged ) return;
        var index = items().indexOf( dragged ), grip = dragged.querySelector( '.a4-grip' );
        dragged.classList.remove( 'is-dragging' );
        var moved = dragged; dragged = null;
        if ( index !== startIndex && index >= 0 ) send( 'MovePolicyTo', grip.getAttribute( 'data-policy' ), expRoleOrder.offset + index + 1 );
    } );
    list.addEventListener( 'keydown', function ( e ) {
        var grip = e.target.closest ? e.target.closest( '.a4-grip' ) : null;
        if ( !grip || ( e.key !== 'ArrowUp' && e.key !== 'ArrowDown' ) ) return;
        e.preventDefault();
        var row = grip.closest( 'li.a4-role-policy' ), b = row.querySelector( e.key === 'ArrowUp' ? '.exp-move-up' : '.exp-move-down' );
        if ( !b || b.disabled ) return;
        var position = parseInt( grip.getAttribute( 'data-position' ), 10 ) + ( e.key === 'ArrowUp' ? -1 : 1 );
        try { sessionStorage.setItem( 'exp-role-grip', String( position ) ); } catch ( x ) {}
        if ( form.requestSubmit ) form.requestSubmit( b ); else b.click();
    } );
    // Back from a move: the grip of the moved policy gets the focus again
    var last = null;
    try { last = sessionStorage.getItem( 'exp-role-grip' ); sessionStorage.removeItem( 'exp-role-grip' ); } catch ( e ) {}
    if ( last ) {
        var target = list.querySelector( '.a4-grip[data-position="' + last + '"]' );
        if ( target ) { target.focus(); target.closest( 'li.a4-role-policy' ).classList.add( 'is-moved' ); }
    }
})();

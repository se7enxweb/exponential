/*
 * admin4 visual/templateview: Enter in a condition field, the order of the overrides (drag, arrows) and the card
 * filter. A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md).
 */
(function()
{
    var form = document.forms['templateview'];
    if ( !form )
        return;

    // Enter in a condition field saves the conditions, instead of the first
    // submit button of the form (Remove selected).
    form.addEventListener( 'keydown', function( e )
    {
        if ( e.key !== 'Enter' )
            return;
        var target = e.target;
        if ( target && ( target.tagName === 'SELECT' || ( target.tagName === 'INPUT' && ( target.type === 'text' || target.type === 'number' ) ) ) )
        {
            e.preventDefault();
            var update = form.querySelector( 'input[name="UpdateOverrideButton"]' );
            if ( update ) update.click();
        }
    } );

    // The order: drag by the handle (or the arrows), saved on every change.
    var list = document.getElementById( 'override-order' );
    if ( !list )
        return;

    // Filter the cards as you type. Moving is off while filtered: a move
    // among the cards shown would say nothing of where it lands among the
    // hidden ones.
    var filter = document.getElementById( 'override-filter-input' );
    var filterNote = document.getElementById( 'override-filter-note' );
    if ( filter )
    {
        filter.addEventListener( 'input', function()
        {
            var q = this.value.replace( /^\s+|\s+$/g, '' ).toLowerCase();
            Array.prototype.forEach.call( list.querySelectorAll( '.override-card' ), function( card )
            {
                var text = card.getAttribute( 'data-name' ) + ' ' + card.querySelector( '.override-file' ).textContent;
                Array.prototype.forEach.call( card.querySelectorAll( '.override-conditions input[type=text]' ), function( i ) { text += ' ' + i.value; } );
                card.hidden = q !== '' && text.toLowerCase().indexOf( q ) === -1;
            } );
            list.classList.toggle( 'is-filtered', q !== '' );
            if ( filterNote ) filterNote.hidden = q === '';
        } );
    }
    var status = document.getElementById( 'override-order-status' );
    var csrfMeta = document.querySelector( 'meta[name="csrf-token"]' );
    var tokenField = form.querySelector( 'input[name="ezxform_token"]' );
    var dragged = null, before = null, saving = false, pending = false;
    var start = parseInt( list.getAttribute( 'data-start' ), 10 ) || 0;
    var previousName = list.getAttribute( 'data-previous' ) || '';
    var nextName = list.getAttribute( 'data-next' ) || '';

    function items() { return Array.prototype.slice.call( list.querySelectorAll( '.override-card' ) ); }
    function order() { return items().map( function( li ) { return li.getAttribute( 'data-name' ); } ); }
    function say( text, kind )
    {
        status.textContent = text || '';
        status.className = 'override-order-status' + ( kind ? ' is-' + kind : '' );
    }
    function renumber()
    {
        items().forEach( function( li, i ) { li.querySelector( '.override-position' ).textContent = start + i + 1; } );
    }
    function restore( names )
    {
        var byName = {};
        items().forEach( function( li ) { byName[li.getAttribute( 'data-name' )] = li; } );
        names.forEach( function( name ) { if ( byName[name] ) list.appendChild( byName[name] ); } );
        renumber();
    }

    // across: a move over the edge of the page, sent as its own stretch of the
    // order; the page is loaded again after it, since an override left it.
    function save( across )
    {
        if ( saving ) { if ( !across ) pending = true; return; }
        saving = true; pending = false;
        list.classList.add( 'is-saving' );
        say( list.getAttribute( 'data-saving' ), 'busy' );
        var body = 'ReorderOverrides=1&OverrideStart=' + ( across ? across.start : start );
        ( across ? across.order : order() ).forEach( function( name ) { body += '&OverrideOrder%5B%5D=' + encodeURIComponent( name ); } );
        if ( tokenField ) body += '&ezxform_token=' + encodeURIComponent( tokenField.value );
        var request = new XMLHttpRequest();
        request.open( 'POST', list.getAttribute( 'data-url' ), true );
        request.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8' );
        request.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
        if ( csrfMeta ) request.setRequestHeader( 'X-CSRF-Token', csrfMeta.getAttribute( 'content' ) );
        request.onload = function()
        {
            var data = null;
            try { data = JSON.parse( request.responseText ); } catch ( e ) {}
            saving = false;
            list.classList.remove( 'is-saving' );
            if ( data && data.ok )
            {
                say( data.message, 'ok' );
                if ( across ) { window.location.reload(); return; }
                if ( pending ) save();
            }
            else
            {
                pending = false;
                if ( data && data.order ) restore( data.order );
                say( ( data && data.error ) || list.getAttribute( 'data-failed' ) + ' (HTTP ' + request.status + ')', 'error' );
            }
        };
        request.onerror = function()
        {
            saving = false; pending = false;
            list.classList.remove( 'is-saving' );
            say( list.getAttribute( 'data-failed' ), 'error' );
        };
        request.send( body );
    }

    // Only the handle starts a drag, so the fields in a card can still be
    // selected and typed in.
    list.addEventListener( 'mousedown', function( e )
    {
        if ( list.classList.contains( 'is-filtered' ) ) return;
        var card = e.target.closest( '.override-card' );
        if ( card ) card.setAttribute( 'draggable', e.target.closest( '.override-grip' ) ? 'true' : 'false' );
    } );
    list.addEventListener( 'dragstart', function( e )
    {
        var card = e.target.closest ? e.target.closest( '.override-card' ) : null;
        if ( !card || card.getAttribute( 'draggable' ) !== 'true' ) { e.preventDefault(); return; }
        dragged = card;
        before = order().join( '\n' );
        e.dataTransfer.effectAllowed = 'move';
        try { e.dataTransfer.setData( 'text/plain', card.getAttribute( 'data-name' ) ); } catch ( err ) {}
        setTimeout( function() { card.classList.add( 'is-dragging' ); }, 0 );
    } );
    list.addEventListener( 'dragover', function( e )
    {
        if ( !dragged ) return;
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        var over = e.target.closest ? e.target.closest( '.override-card' ) : null;
        if ( !over || over === dragged ) return;
        var box = over.getBoundingClientRect();
        list.insertBefore( dragged, ( e.clientY - box.top ) > box.height / 2 ? over.nextSibling : over );
    } );
    list.addEventListener( 'drop', function( e ) { if ( dragged ) e.preventDefault(); } );
    list.addEventListener( 'dragend', function()
    {
        if ( !dragged ) return;
        dragged.classList.remove( 'is-dragging' );
        dragged.setAttribute( 'draggable', 'false' );
        dragged = null;
        renumber();
        if ( order().join( '\n' ) !== before ) save();
    } );

    // The arrows, for the keyboard and touch screens.
    list.addEventListener( 'click', function( e )
    {
        var button = e.target.closest ? e.target.closest( 'button' ) : null;
        if ( !button || list.classList.contains( 'is-filtered' ) ) return;
        var card = button.closest( '.override-card' );
        var name = card.getAttribute( 'data-name' );
        if ( button.classList.contains( 'override-up' ) && !card.previousElementSibling && previousName )
        {
            save( { start: start - 1, order: [ name, previousName ] } );
            return;
        }
        if ( button.classList.contains( 'override-down' ) && !card.nextElementSibling && nextName )
        {
            save( { start: start + items().length - 1, order: [ nextName, name ] } );
            return;
        }
        if ( button.classList.contains( 'override-up' ) && card.previousElementSibling )
            list.insertBefore( card, card.previousElementSibling );
        else if ( button.classList.contains( 'override-down' ) && card.nextElementSibling )
            list.insertBefore( card.nextElementSibling, card );
        else
            return;
        button.focus();
        renumber();
        save();
    } );
})();

/*
 * admin4 setup/maintenance: the end time, the message count and preview, the address check and the copy buttons.
 * The texts are data-text-* attributes of #maintenance-script. A file of its own, so the page runs under a Content
 * Security Policy without 'unsafe-inline' (doc/eng-us/features/6.0/content-security-policy.md).
 */
var expMaintenanceText = ( function ( el ) {
    return {
        back: el.getAttribute( 'data-text-back' ),
        tooLong: el.getAttribute( 'data-text-too-long' ),
        chars: el.getAttribute( 'data-text-chars' ),
        notAddresses: el.getAttribute( 'data-text-not-addresses' ),
        copied: el.getAttribute( 'data-text-copied' ),
        copiedLong: el.getAttribute( 'data-text-copied-long' ),
        copyFailed: el.getAttribute( 'data-text-copy-failed' )
    };
} )( document.getElementById( 'maintenance-script' ) );
(function () {
    var root = document.querySelector( '.a4-setup-maint' );
    if ( !root ) return;
    function each( selector, fn ) { var n = root.querySelectorAll( selector ), i; for ( i = 0; i < n.length; i++ ) fn( n[i] ); }
    function tr( name, values ) { var s = expMaintenanceText[name], k; for ( k in values || {} ) s = s.split( '%' + k ).join( values[k] ); return s; }
    function text( el, value ) { if ( el ) el.textContent = value; }
    function pad( n ) { return ( n < 10 ? '0' : '' ) + n; }
    function escapeHtml( s ) { return String( s ).replace( /&/g, '&amp;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' ).replace( /"/g, '&quot;' ).replace( /'/g, '&#039;' ); }

    each( '.a4-setup-js-only', function ( el ) { el.hidden = false; } );

    var message = document.getElementById( 'MaintenanceMessage' );
    var minutes = document.getElementById( 'MaintenanceMinutes' );
    var minutesResult = document.getElementById( 'maintenance-minutes-result' );
    var count = document.getElementById( 'maintenance-message-count' );
    var ips = document.getElementById( 'MaintenanceAllowIPs' );
    var ipCheck = document.getElementById( 'maintenance-ip-check' );
    var frame = document.getElementById( 'maintenance-preview-frame' );
    var preview = frame && frame.hasAttribute( 'data-template' ) ? {
        template: frame.getAttribute( 'data-template' ), messageSlot: frame.getAttribute( 'data-message-slot' ),
        untilSlot: frame.getAttribute( 'data-until-slot' ), defaultMessage: frame.getAttribute( 'data-default-message' ),
        defaultUntil: frame.getAttribute( 'data-default-until' ) } : null;

    function minutesValue() {
        var m = minutes ? parseInt( minutes.value, 10 ) : 0;
        return isNaN( m ) || m < 0 ? 0 : m;
    }

    // The preview: the real page with what is typed in the message and the expected end
    var timer = null;
    function renderPreview() {
        if ( !frame || !preview ) return;
        var msg = message ? message.value.trim() : '';
        var m = Math.min( minutesValue(), 10080 );
        var until = preview.defaultUntil;
        if ( m > 0 ) {
            var d = new Date( Date.now() + m * 60000 );
            until = 'Expected back at ' + pad( d.getUTCHours() ) + ':' + pad( d.getUTCMinutes() ) + ' UTC.';
        }
        msg = msg !== '' ? msg : preview.defaultMessage;
        // In place when the frame's page can be reached (it is same-origin and runs no script): loading it again
        // for every key would abort its font downloads and flash. The page shows the default texts at first.
        var doc = null;
        try { doc = frame.contentDocument; } catch ( e ) {}
        if ( doc && doc.body && doc.readyState === 'complete' && doc.URL === 'about:srcdoc' ) {
            replaceText( doc.body, shown.msg, msg );
            replaceText( doc.body, shown.until, until );
        } else {
            frame.setAttribute( 'srcdoc', preview.template.split( preview.messageSlot ).join( escapeHtml( msg ) )
                                                          .split( preview.untilSlot ).join( escapeHtml( until ) ) );
        }
        shown = { msg: msg, until: until };
    }
    var shown = { msg: preview ? preview.defaultMessage : '', until: preview ? preview.defaultUntil : '' };
    function replaceText( node, from, to ) {
        if ( from === to || from === '' ) return;
        var walker = node.ownerDocument.createTreeWalker( node, 4 /* NodeFilter.SHOW_TEXT */ );
        while ( walker.nextNode() ) {
            var t = walker.currentNode;
            if ( t.nodeValue.indexOf( from ) !== -1 ) t.nodeValue = t.nodeValue.split( from ).join( to );
        }
    }
    function schedulePreview() { window.clearTimeout( timer ); timer = window.setTimeout( renderPreview, 120 ); }

    function updateMinutes() {
        var m = minutesValue();
        each( '.a4-setup-preset', function ( b ) { b.setAttribute( 'aria-pressed', String( parseInt( b.getAttribute( 'data-minutes' ), 10 ) === m ) ); } );
        if ( !minutesResult ) return;
        if ( m > 10080 ) { text( minutesResult, tr( 'tooLong' ) ); return; }
        if ( m === 0 ) { text( minutesResult, '' ); return; }
        var d = new Date( Date.now() + m * 60000 );
        text( minutesResult, tr( 'back', { local: d.toLocaleString( document.documentElement.lang || undefined, { weekday: 'short', hour: '2-digit', minute: '2-digit' } ),
                                           utc: pad( d.getUTCHours() ) + ':' + pad( d.getUTCMinutes() ) } ) );
    }
    if ( minutes ) minutes.addEventListener( 'input', function () { updateMinutes(); schedulePreview(); } );
    each( '.a4-setup-preset', function ( b ) {
        b.addEventListener( 'click', function () {
            if ( !minutes ) return;
            minutes.value = b.getAttribute( 'data-minutes' );
            updateMinutes(); schedulePreview();
        } );
    } );

    function updateCount() { if ( message && count ) text( count, tr( 'chars', { count: message.value.length } ) ); }
    if ( message ) message.addEventListener( 'input', function () { updateCount(); schedulePreview(); } );

    // The same check as the server's: what is not an address is left out, so it is said before sending
    var v4 = /^(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}$/;
    function isAddress( s ) {
        if ( v4.test( s ) ) return true;
        if ( s.indexOf( ':' ) === -1 || !/^[0-9a-fA-F:.]+$/.test( s ) ) return false;
        var tail = s.match( /:(\d+\.\d+\.\d+\.\d+)$/ );
        if ( tail && !v4.test( tail[1] ) ) return false;
        var parts = s.split( '::' );
        if ( parts.length > 2 ) return false;
        var groups = s.replace( /:\d+\.\d+\.\d+\.\d+$/, ':0:0' ).split( ':' ).filter( function ( g ) { return g !== ''; } );
        for ( var i = 0; i < groups.length; i++ ) if ( !/^[0-9a-fA-F]{1,4}$/.test( groups[i] ) ) return false;
        return parts.length === 2 ? groups.length < 8 : groups.length === 8;
    }
    function checkAddresses() {
        if ( !ips || !ipCheck ) return;
        var bad = ips.value.split( /[\s,;]+/ ).filter( function ( e ) { return e !== '' && !isAddress( e ); } );
        ipCheck.hidden = bad.length === 0;
        ips.setAttribute( 'aria-invalid', bad.length ? 'true' : 'false' );
        text( ipCheck, bad.length ? tr( 'notAddresses', { list: bad.join( ', ' ) } ) : '' );
    }
    if ( ips ) ips.addEventListener( 'input', checkAddresses );

    updateMinutes(); updateCount(); checkAddresses();

    // Copy buttons
    var copyStatus = document.getElementById( 'maintenance-copy-status' );
    function copyText( value, button ) {
        function done( ok ) {
            text( copyStatus, ok ? tr( 'copiedLong' ) : tr( 'copyFailed' ) );
            if ( !ok ) return;
            var label = button.textContent;
            text( button, tr( 'copied' ) );
            window.setTimeout( function () { text( button, label ); }, 1600 );
        }
        function fallback() {
            var area = document.createElement( 'textarea' );
            area.value = value; area.setAttribute( 'readonly', '' );
            area.style.position = 'fixed'; area.style.opacity = '0';
            document.body.appendChild( area ); area.select();
            var ok = false;
            try { ok = document.execCommand( 'copy' ); } catch ( e ) {}
            document.body.removeChild( area );
            button.focus();
            return ok;
        }
        if ( navigator.clipboard && window.isSecureContext ) {
            navigator.clipboard.writeText( value ).then( function () { done( true ); }, function () { done( fallback() ); } );
            return;
        }
        done( fallback() );
    }
    each( '.a4-setup-copy', function ( button ) {
        button.addEventListener( 'click', function () {
            var from = document.getElementById( button.getAttribute( 'data-copy-from' ) );
            copyText( from ? from.textContent : '', button );
        } );
    } );
})();

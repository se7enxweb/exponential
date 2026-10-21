/*
 * admin4: the check every script applies before it sends the browser to an address that came from data (a
 * data-* attribute, a JSON answer, a menu declaration). Loaded in the page head before the scripts that call it
 * (popupmenu/ezpopupmenu.js, the page scripts). The admin and admin4 designs carry the same file.
 *
 * window.a4SafeURL( url [, label] ) is true for an address of this site: resolved against the page, http or https and
 * the page's origin. Anything else (javascript:, data:, //other.host, an address that does not parse) is refused with
 * an error in the console, named by label.
 *
 * window.a4ViewParameter.encode( value ) writes a value of a view parameter, /(name)/<value>, as the kernel's
 * expViewParameters::encode() does (and the template operator view_parameter): "/", "(", ")", "~", "%", "+", "\",
 * "?", "#" and control characters as "~" and two hexadecimal digits ("~2F" for "/"), a value of only "." or ".." as
 * "~2E" each, the rest percent-encoded with , : @ = ! * kept readable. a4ViewParameter.decode( value ) is the inverse
 * of the "~XX" part (the browser and the kernel undo the percent-encoding).
 */
(function () {
    'use strict';
    window.a4SafeURL = function ( url, label ) {
        var safe = false;
        try {
            if ( typeof URL === 'function' ) {
                var page = new URL( window.location.href ), target = new URL( String( url ), page.href );
                safe = ( target.protocol === 'http:' || target.protocol === 'https:' ) && target.origin === page.origin;
            } else {
                // no URL parser: only a path of this site
                safe = /^\/(?![\/\\])/.test( String( url ) );
            }
        } catch ( e ) {
            safe = false;
        }
        if ( !safe && window.console )
            window.console.error( ( label || 'a4SafeURL' ) + ': refused the address ' + url + ' (not of this site)' );
        return safe;
    };

    var ESCAPED = /[~\/()%+\\?#\x00-\x1F\x7F]/g;
    var hex = function ( c ) { return '~' + ( '0' + c.charCodeAt( 0 ).toString( 16 ).toUpperCase() ).slice( -2 ); };
    window.a4ViewParameter = {
        encode: function ( value ) {
            var text = value === null || value === undefined ? '' : String( value );
            text = text === '.' || text === '..' ? text.replace( /\./g, '~2E' ) : text.replace( ESCAPED, hex );
            var out;
            try {
                out = encodeURIComponent( text );
            } catch ( e ) {
                // a lone surrogate (no UTF-8 for it): U+FFFD in its place
                out = encodeURIComponent( typeof text.toWellFormed === 'function' ? text.toWellFormed() : text.replace( /[\uD800-\uDFFF]/g, '\uFFFD' ) );
            }
            return out.replace( /'/g, '%27' ).replace( /%(2C|3A|40|3D)/g, function ( all ) { return decodeURIComponent( all ); } );
        },
        decode: function ( value ) {
            var text = value === null || value === undefined ? '' : String( value );
            try {
                return decodeURIComponent( text.replace( /%/g, '%25' ).replace( /~([0-9A-Fa-f]{2})/g, '%$1' ) );
            } catch ( e ) {
                // bytes that are not UTF-8: one character each
                return text.replace( /~([0-9A-Fa-f]{2})/g, function ( all, h ) { return String.fromCharCode( parseInt( h, 16 ) ); } );
            }
        }
    };
}());

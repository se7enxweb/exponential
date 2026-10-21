/*
 * admin4: the check every script applies before it sends the browser to an address that came from data (a
 * data-* attribute, a JSON answer, a menu declaration). Loaded in the page head before the scripts that call it
 * (popupmenu/ezpopupmenu.js, the page scripts). The admin and admin4 designs carry the same file.
 *
 * window.a4SafeURL( url [, label] ) is true for an address of this site: resolved against the page, http or https and
 * the page's origin. Anything else (javascript:, data:, //other.host, an address that does not parse) is refused with
 * an error in the console, named by label.
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
}());

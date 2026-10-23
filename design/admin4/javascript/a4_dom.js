/*
 * admin4: the few helpers the scripts that run on plain JavaScript share, so each of them does not carry its own copy.
 * Loaded in the page head right after a4_url.js (page_head_script.tpl), before the page scripts that call it. No
 * library, nothing else in the page's global scope than window.a4Dom.
 *
 *   a4Dom.ready( fn )                 fn once the document is parsed (at once when it already is)
 *   a4Dom.each( list, fn )            fn for each item of a NodeList, an HTMLCollection or an array (null: none)
 *   a4Dom.on( root, type, sel, fn )   a delegated listener: fn runs with this = the closest element matching sel inside
 *                                     root, for elements added after the call too; returns a function that removes it
 *   a4Dom.formToken()                 the page's form token (#ezxform_token_js), '' without one
 *   a4Dom.headers( [more] )           X-Requested-With, and X-CSRF-Token from <meta name="csrf-token"> when the page
 *                                     has one, merged with more
 *   a4Dom.post( url, fields )         a POST as a form (application/x-www-form-urlencoded; charset=UTF-8) of fields
 *                                     (an object or [[name, value], ...]) plus the form token, with the headers above
 *                                     and the session cookie; resolves to the fetch Response
 *   a4Dom.getJSON( url )              a GET for JSON as an XMLHttpRequest asks for it; resolves to the parsed answer,
 *                                     rejects with the HTTP status (0 without an answer;
 *                                     a 2xx answer that is not JSON rejects with its status)
 *
 * The page runs under a Content Security Policy without 'unsafe-inline' or 'unsafe-eval'
 * (doc/eng-us/features/6.0/content-security-policy.md): no eval, no inline handlers.
 */
(function ( window, document ) {
    'use strict';
    if ( window.a4Dom )
        return;

    function ready( fn ) {
        if ( document.readyState === 'loading' )
            document.addEventListener( 'DOMContentLoaded', function () { fn(); } );
        else
            fn();
    }

    function each( list, fn ) {
        if ( list )
            Array.prototype.forEach.call( list, fn );
    }

    function on( root, type, selector, fn ) {
        var listener = function ( e ) {
            var target = e.target;
            if ( target && target.nodeType !== 1 )
                target = target.parentElement;
            var el = target && target.closest ? target.closest( selector ) : null;
            if ( el && ( el === root || root.contains( el ) ) )
                fn.call( el, e );
        };
        root.addEventListener( type, listener );
        return function () { root.removeEventListener( type, listener ); };
    }

    function formToken() {
        var token = document.getElementById( 'ezxform_token_js' );
        return token ? token.getAttribute( 'title' ) || '' : '';
    }

    function headers( more ) {
        var out = { 'X-Requested-With': 'XMLHttpRequest' }, meta = document.querySelector( 'meta[name="csrf-token"]' );
        if ( meta && meta.getAttribute( 'content' ) )
            out['X-CSRF-Token'] = meta.getAttribute( 'content' );
        if ( more )
            Object.keys( more ).forEach( function ( name ) { out[name] = more[name]; } );
        return out;
    }

    function post( url, fields ) {
        var data = new URLSearchParams(), token = formToken();
        if ( Array.isArray( fields ) )
            fields.forEach( function ( pair ) { data.append( pair[0], pair[1] ); } );
        else if ( fields )
            Object.keys( fields ).forEach( function ( name ) { data.append( name, fields[name] ); } );
        if ( token && !data.has( 'ezxform_token' ) )
            data.append( 'ezxform_token', token );
        return window.fetch( url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: headers( { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' } ),
            body: data.toString()
        } );
    }

    function getJSON( url ) {
        return window.fetch( url, {
            method: 'GET',
            credentials: 'same-origin',
            // the wildcard type in two parts: whole, it would read as the start of a comment to tools that strip them
            headers: headers( { 'Accept': 'application/json, text/javascript, */' + '*; q=0.01' } )
        } ).then( function ( response ) {
            return response.text().then( function ( text ) {
                if ( !response.ok )
                    throw response.status;
                try { return JSON.parse( text ); } catch ( e ) { throw response.status; }
            } );
        }, function () {
            throw 0;
        } );
    }

    window.a4Dom = Object.freeze( {
        ready: ready,
        each: each,
        on: on,
        formToken: formToken,
        headers: headers,
        post: post,
        getJSON: getJSON
    } );
})( window, document );

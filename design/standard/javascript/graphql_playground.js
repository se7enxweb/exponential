/**
 * The playground of Setup > GraphQL (design:graphql/playground.tpl): posts the query and its variables as JSON to
 * the session end point graphql/query and shows the answer. The form token of the page goes in the header
 * X-CSRF-Token, as every POST of a signed-in session needs it. Ctrl+Enter (Cmd+Enter) runs the query.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 */
( function () {
    'use strict';
    var form = document.getElementById( 'exp-graphql-form' );
    if ( !form || !window.fetch ) {
        return;
    }
    var query = document.getElementById( 'exp-graphql-query' ),
        variables = document.getElementById( 'exp-graphql-variables' ),
        answer = document.getElementById( 'exp-graphql-answer' ),
        button = document.getElementById( 'exp-graphql-run' );

    function token() {
        var field = form.querySelector( 'input[name="ezxform_token"]' ),
            meta = document.querySelector( 'meta[name="csrf-token"]' );
        if ( field && field.value ) {
            return field.value;
        }
        return meta ? meta.getAttribute( 'content' ) : '';
    }

    function run( event ) {
        if ( event ) {
            event.preventDefault();
        }
        var vars = null, text = variables.value.trim();
        if ( text !== '' ) {
            try {
                vars = JSON.parse( text );
            } catch ( e ) {
                answer.textContent = e.message;
                return;
            }
        }
        var headers = { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' };
        if ( token() ) {
            headers[ 'X-CSRF-Token' ] = token();
        }
        button.disabled = true;
        answer.textContent = form.getAttribute( 'data-running' );
        fetch( form.getAttribute( 'data-endpoint' ), {
            method: 'POST',
            credentials: 'same-origin',
            headers: headers,
            body: JSON.stringify( { query: query.value, variables: vars } )
        } ).then( function ( response ) {
            return response.text();
        } ).then( function ( body ) {
            try {
                answer.textContent = JSON.stringify( JSON.parse( body ), null, 2 );
            } catch ( e ) {
                answer.textContent = form.getAttribute( 'data-unreadable' ) + '\n\n' + body.slice( 0, 2000 );
            }
        } ).catch( function ( e ) {
            answer.textContent = form.getAttribute( 'data-unreadable' ) + '\n\n' + e.message;
        } ).then( function () {
            button.disabled = false;
        } );
    }

    form.addEventListener( 'submit', run );
    query.addEventListener( 'keydown', function ( event ) {
        if ( event.key === 'Enter' && ( event.ctrlKey || event.metaKey ) ) {
            run( event );
        }
    } );
}() );

/**
 * eZ Online Editor - TinyMCE 8 prototype: shared helpers for the ezoe dialogs
 *
 * Server calls (ezjscore / ezoe module) and the content list of the TinyMCE 3 ezoe dialogs:
 * a table with radio button, name and class, 10 entries per page with << Previous / Next >>,
 * a clickable path and containers that open on a click on their name (browse).
 * TinyMCE dialogs can not bind events in html panels, so the list uses data-ezoe-* attributes
 * and one delegated click handler per dialog, see bindList().
 *
 * Licensed under the GNU General Public License v2.0, like the rest of ezoe.
 */
window.eZOe8Dialog = (function () {
    'use strict';

    var PAGE_SIZE = 10;

    tinymce.addI18n( 'de', {
        'Previous': 'Zurück',
        'Next': 'Weiter',
        'No results': 'Keine Treffer',
        'Name': 'Name',
        'Type': 'Typ',
        '%1 to %2 of %3': '%1 bis %2 von %3'
    } );

    // ezurl() strips the trailing slash
    var serverUrl = function ( settings, name ) {
        return String( settings[name] || '' ).replace( /\/?$/, '/' );
    };

    var request = function ( url, options ) {
        // ezjscore picks the response format from the Accept header
        options = Object.assign( {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json, text/html;q=0.9', 'X-Requested-With': 'XMLHttpRequest' }
        }, options || {} );
        return fetch( url, options ).then( function ( response ) {
            if ( !response.ok )
                throw new Error( 'HTTP ' + response.status + ' – ' + url );
            return response;
        } );
    };

    var ezjscoreCall = function ( settings, functionArguments, params ) {
        var body = new URLSearchParams( params || {} );
        body.append( 'ezjscServer_function_arguments', functionArguments );
        body.append( 'ezxform_token', settings.form_token || '' );
        return request( serverUrl( settings, 'ezjscore_url' ) + 'call', { method: 'POST', body: body } )
            .then( function ( r ) { return r.json(); } )
            .then( function ( data ) {
                if ( data.error_text )
                    throw new Error( data.error_text );
                return data.content;
            } );
    };

    // All list functions resolve to { items, total, offset, node (browse only) }
    var search = function ( settings, text, offset ) {
        return ezjscoreCall( settings, 'ezjsc::search', {
            SearchStr: text, SearchOffset: String( offset || 0 ), SearchLimit: String( PAGE_SIZE )
        } ).then( function ( c ) {
            return { items: c.SearchResult || [], total: c.SearchCount || 0, offset: c.SearchOffset || 0 };
        } );
    };

    var browse = function ( settings, nodeId, offset ) {
        return ezjscoreCall( settings, 'ezoe::browse::' + nodeId + '::' + ( offset || 0 ) + '::' + PAGE_SIZE ).then( function ( c ) {
            return { items: c.list || [], total: c.total_count || 0, offset: c.offset || 0, node: c.node };
        } );
    };

    var bookmarks = function ( settings, offset ) {
        return ezjscoreCall( settings, 'ezoe::bookmarks::' + ( offset || 0 ) + '::' + PAGE_SIZE ).then( function ( c ) {
            return { items: c.list || [], total: c.total_count || 0, offset: c.offset || 0 };
        } );
    };

    var loadObject = function ( settings, embedId, size ) {
        return request( serverUrl( settings, 'extension_url' ) + 'load/' + embedId + ( size ? '/0/' + encodeURIComponent( size ) : '' ) )
            .then( function ( r ) { return r.json(); } );
    };

    var loadEmbedView = function ( settings, embedId, params ) {
        return request( serverUrl( settings, 'extension_url' ) + 'embed_view/' + embedId + '?' + new URLSearchParams( params ).toString() )
            .then( function ( r ) { return r.text(); } );
    };

    var escapeHtml = function ( value ) {
        return String( value === undefined || value === null ? '' : value )
            .replace( /&/g, '&amp;' ).replace( /"/g, '&quot;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' );
    };

    // Names from ezjscAjaxContent are html encoded already
    var decodeHtml = function ( value ) {
        var el = document.createElement( 'textarea' );
        el.innerHTML = String( value === undefined || value === null ? '' : value );
        return el.value;
    };

    /**
     * Html of a content list like the TinyMCE 3 ezoe dialogs.
     *
     * @param {Object} list   result of search() / browse() / bookmarks()
     * @param {Object} options selected: value of the checked row, value: function( item ) -> row value,
     *                          browse: true to open containers on a click on the name, t: translate function
     */
    var renderList = function ( list, options ) {
        var t = options.t, html = '';

        if ( list.node )
        {
            html += '<div class="ezoe-list-path">';
            ( list.node.path || [] ).concat( [ list.node ] ).forEach( function ( n, i, all ) {
                html += i === all.length - 1
                    ? '<strong>' + escapeHtml( decodeHtml( n.name ) ) + '</strong>'
                    : '<a href="#" data-ezoe-action="open" data-ezoe-value="' + n.node_id + '">' + escapeHtml( decodeHtml( n.name ) ) + '</a> / ';
            } );
            html += '</div>';
        }

        if ( !list.items.length )
            return html + '<p class="ezoe-list-empty">' + escapeHtml( t( 'No results' ) ) + '</p>';

        html += '<table class="ezoe-list"><tbody>';
        list.items.forEach( function ( item ) {
            var value = options.value( item ), name = escapeHtml( decodeHtml( item.name ) );
            html += '<tr data-ezoe-action="select" data-ezoe-value="' + escapeHtml( value ) + '"' + ( value === options.selected ? ' class="ezoe-list-selected"' : '' ) + '>' +
                    '<td class="ezoe-list-radio"><input type="radio" tabindex="-1"' + ( value === options.selected ? ' checked' : '' ) + ' /></td>' +
                    '<td class="ezoe-list-name">' +
                    ( options.browse && item.children_count
                        ? '<a href="#" data-ezoe-action="open" data-ezoe-value="' + item.node_id + '" title="' + escapeHtml( item.children_count ) + '">' + name + '</a>'
                        : name ) +
                    '</td><td class="ezoe-list-class">' + escapeHtml( decodeHtml( item.class_name ) ) + '</td></tr>';
        } );
        html += '</tbody></table>';

        if ( list.total > PAGE_SIZE )
        {
            var from = list.offset + 1, to = Math.min( list.offset + PAGE_SIZE, list.total );
            html += '<div class="ezoe-list-paging">' +
                    ( list.offset > 0 ? '<a href="#" data-ezoe-action="page" data-ezoe-value="' + Math.max( 0, list.offset - PAGE_SIZE ) + '">&lt;&lt; ' + escapeHtml( t( 'Previous' ) ) + '</a>' : '<span></span>' ) +
                    '<span>' + escapeHtml( t( '%1 to %2 of %3' ).replace( '%1', from ).replace( '%2', to ).replace( '%3', list.total ) ) + '</span>' +
                    ( to < list.total ? '<a href="#" data-ezoe-action="page" data-ezoe-value="' + ( list.offset + PAGE_SIZE ) + '">' + escapeHtml( t( 'Next' ) ) + ' &gt;&gt;</a>' : '<span></span>' ) +
                    '</div>';
        }
        return html;
    };

    /**
     * Delegated click handler for the lists of the currently open dialog.
     * Returns a function that removes the handler, call it in the dialog onClose.
     *
     * @param {Function} handler function( action, value, element ) with action select / open / page
     */
    var bindList = function ( handler ) {
        var listener = function ( e ) {
            var el = e.target.closest( '[data-ezoe-action]' );
            if ( !el || !el.closest( '.tox-dialog' ) )
                return;
            e.preventDefault();
            // a click on the name of a container opens it, a click elsewhere in the row selects it
            handler( el.getAttribute( 'data-ezoe-action' ), el.getAttribute( 'data-ezoe-value' ), el );
        };
        document.addEventListener( 'click', listener, true );
        return function () {
            document.removeEventListener( 'click', listener, true );
        };
    };

    return {
        PAGE_SIZE: PAGE_SIZE,
        request: request,
        ezjscoreCall: ezjscoreCall,
        search: search,
        browse: browse,
        bookmarks: bookmarks,
        loadObject: loadObject,
        loadEmbedView: loadEmbedView,
        escapeHtml: escapeHtml,
        decodeHtml: decodeHtml,
        renderList: renderList,
        bindList: bindList
    };
}());

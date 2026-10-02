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
        'Preview': 'Vorschau',
        'All': 'Alle',
        'Content classes': 'Inhaltsklassen',
        '%1 to %2 of %3': '%1 bis %2 von %3',
        'Please fill in: %s': 'Bitte ausfüllen: %s',
        'Please enter a whole number: %s': 'Bitte eine ganze Zahl eingeben: %s',
        'Please enter a number: %s': 'Bitte eine Zahl eingeben: %s',
        'Please enter an e-mail address: %s': 'Bitte eine E-Mail-Adresse eingeben: %s',
        'Value too small: %s': 'Wert zu klein: %s',
        'Value too large: %s': 'Wert zu groß: %s'
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
        var body = params instanceof URLSearchParams ? params : new URLSearchParams( params || {} );
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
    // classIds: content class ids to limit the search to, empty for all (SearchContentClassID[] of the old dialog)
    var search = function ( settings, text, offset, classIds ) {
        var params = new URLSearchParams( {
            SearchStr: text, SearchOffset: String( offset || 0 ), SearchLimit: String( PAGE_SIZE ), EncodingLoadImages: '1'
        } );
        ( classIds || [] ).forEach( function ( id ) {
            if ( id )
                params.append( 'SearchContentClassID[]', id );
        } );
        return ezjscoreCall( settings, 'ezjsc::search', params ).then( function ( c ) {
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

    /**
     * Storage locations offered by the TinyMCE 3 upload dialog (upload.ini [LocationSettings] and
     * can_create checks), read from the select of the existing ezoe/upload page.
     * Resolves to [ { text, value } ], value 'auto' is the automatic location.
     */
    var uploadLocations = function ( settings ) {
        return request( serverUrl( settings, 'extension_url' ) + 'upload/' + settings.contentobject_id + '/' + settings.contentobject_version + '/objects' )
            .then( function ( r ) { return r.text(); } )
            .then( function ( html ) {
                var doc = new DOMParser().parseFromString( html, 'text/html' ), select = doc.querySelector( 'select[name="location"]' );
                return select ? Array.prototype.map.call( select.options, function ( o ) {
                    return { text: o.textContent.replace( /\u00a0/g, '  ' ), value: o.value };
                } ) : [ { text: 'Automatic', value: 'auto' } ];
            } );
    };

    /**
     * Uploads a file like the TinyMCE 3 upload dialog: ezoe/upload creates and publishes the object
     * and adds it as embed relation to the edited draft. The view answers with a small html page for a
     * hidden iframe, it calls eZOEPopupUtils.selectByEmbedId( objectId, nodeId, name ) on success and
     * shows the errors as red paragraphs otherwise.
     *
     * @param {Object} fields name, location, description, alternativeText
     * @return Promise resolving to { objectId, nodeId, name }
     */
    var upload = function ( settings, file, fields ) {
        var body = new FormData();
        body.append( 'uploadButton', '1' );
        body.append( 'ezxform_token', settings.form_token || '' );
        body.append( 'fileName', file, file.name );
        body.append( 'objectName', fields.name || '' );
        body.append( 'ContentObjectAttribute_name', fields.name || '' );
        body.append( 'location', fields.location || 'auto' );
        // only attributes the class of the new object has are used by the upload view
        body.append( 'ContentObjectAttribute_description', fields.description || '' );
        body.append( 'ContentObjectAttribute_caption', fields.description || '' );
        body.append( 'ContentObjectAttribute_image', fields.alternativeText || '' );

        return request( serverUrl( settings, 'extension_url' ) + 'upload/' + settings.contentobject_id + '/' + settings.contentobject_version + '/auto/1',
                        { method: 'POST', body: body, headers: { 'X-Requested-With': 'XMLHttpRequest' } } )
            .then( function ( r ) { return r.text(); } )
            .then( function ( html ) {
                var m = html.match( /selectByEmbedId\(\s*(\d+)\s*,\s*(\d+)\s*,\s*("(?:[^"\\]|\\.)*")\s*\)/ );
                if ( m )
                    return { objectId: parseInt( m[1], 10 ), nodeId: parseInt( m[2], 10 ), name: JSON.parse( m[3] ) };
                var doc = new DOMParser().parseFromString( html, 'text/html' ),
                    errors = Array.prototype.map.call( doc.querySelectorAll( 'p' ), function ( p ) { return p.textContent.trim(); } ).filter( Boolean );
                throw new Error( errors.join( ' ' ) || doc.body.textContent.trim().slice( 0, 300 ) || 'Upload failed' );
            } );
    };

    // ---- custom attributes (customattributes="name|valueattribute_separationname2|value2")

    var ATTRIBUTE_SEPARATOR = 'attribute_separation';
    var FIELD_PREFIX = 'attr_';

    var parseCustomAttributes = function ( value ) {
        var result = {};
        ( value || '' ).split( ATTRIBUTE_SEPARATOR ).forEach( function ( part ) {
            var pos = part.indexOf( '|' );
            if ( pos > 0 )
                result[ part.slice( 0, pos ) ] = part.slice( pos + 1 );
        } );
        return result;
    };

    var serializeCustomAttributes = function ( values ) {
        return Object.keys( values ).map( function ( key ) {
            return key + '|' + values[key];
        } ).join( ATTRIBUTE_SEPARATOR );
    };

    var attributeFieldName = function ( attribute ) {
        return FIELD_PREFIX + attribute.id;
    };

    // Dialog component for a custom attribute definition of eZOEXMLInput::getCustomAttributeDefinitions()
    var attributeField = function ( attribute ) {
        var label = attribute.name + ( attribute.required ? ' *' : '' ),
            field = { name: attributeFieldName( attribute ), label: label, enabled: !attribute.disabled };

        switch ( attribute.type )
        {
            case 'select':
                field.type = 'listbox';
                field.items = Object.keys( attribute.selection || {} ).map( function ( key ) {
                    return { text: attribute.selection[key], value: key };
                } );
                if ( !field.items.length )
                    field.items = [ { text: '', value: '' } ];
                break;
            case 'checkbox':
                field.type = 'checkbox';
                break;
            case 'textarea':
                field.type = 'textarea';
                field.maximized = false;
                break;
            case 'color':
                field.type = 'colorinput';
                break;
            default:
                // text, int, number, email, link and the css/html size types
                field.type = 'input';
                if ( attribute.title )
                    field.placeholder = attribute.title;
        }
        return field;
    };

    // Dialog value of an attribute from the stored string value
    var toAttributeFieldValue = function ( attribute, value ) {
        if ( attribute.type === 'checkbox' )
            return value !== undefined && value !== '' && value !== 'false';
        if ( attribute.type === 'select' && value === undefined )
        {
            var keys = Object.keys( attribute.selection || {} );
            return keys.indexOf( attribute['default'] ) !== -1 ? attribute['default'] : ( keys[0] || '' );
        }
        return value === undefined ? ( attribute['default'] || '' ) : value;
    };

    // Stored string value of an attribute from the dialog value, null means not set
    var fromAttributeFieldValue = function ( attribute, value ) {
        if ( attribute.type === 'checkbox' )
            return value ? ( String( attribute['default'] || '' ).trim() || '1' ) : null;
        value = String( value === undefined || value === null ? '' : value ).trim();
        return value === '' ? null : value;
    };

    // Validation like popup_validate.js of the TinyMCE 3 dialogs, returns the message or null
    var validateAttribute = function ( attribute, value, t ) {
        var tr = function ( text ) {
            return t( text ).replace( '%s', attribute.name );
        };
        if ( attribute.required && ( value === null || ( attribute.type === 'select' && value === Object.keys( attribute.selection || {} )[0] ) ) )
            return tr( 'Please fill in: %s' );
        if ( value === null )
            return ( attribute.type === 'int' || attribute.type === 'number' ) && !attribute.allowEmpty ? tr( 'Please fill in: %s' ) : null;
        if ( attribute.type === 'int' && !/^-?\d+$/.test( value ) )
            return tr( 'Please enter a whole number: %s' );
        if ( attribute.type === 'number' && !/^-?\d+([.,]\d+)?$/.test( value ) )
            return tr( 'Please enter a number: %s' );
        if ( attribute.type === 'email' && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test( value ) )
            return tr( 'Please enter an e-mail address: %s' );
        if ( attribute.type === 'int' || attribute.type === 'number' )
        {
            var number = parseFloat( value.replace( ',', '.' ) );
            if ( attribute.minimum !== null && number < parseFloat( attribute.minimum ) )
                return tr( 'Value too small: %s' );
            if ( attribute.maximum !== null && number > parseFloat( attribute.maximum ) )
                return tr( 'Value too large: %s' );
        }
        return null;
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

    // Url of the preview image of a list entry in the given alias, see eZOEPopupUtils.indexOfImage()
    var previewUrl = function ( item, alias, rootUrl ) {
        var attributes = item.image_attributes || [], i, content;
        for ( i = 0; i < attributes.length; i++ )
        {
            content = item.data_map && item.data_map[ attributes[i] ] && item.data_map[ attributes[i] ].content;
            if ( content && content[alias] && content[alias].url )
                return /^(https?:)?\//.test( content[alias].url ) ? content[alias].url : ( rootUrl || '/' ) + content[alias].url;
        }
        return null;
    };

    /**
     * Class filter of the search like the TinyMCE 3 dialog: a multiple select with "All" and the content classes.
     * TinyMCE dialogs have no multiple select component, so it is plain html read with readClassFilter().
     */
    var renderClassFilter = function ( classes, selected, t ) {
        selected = selected || [];
        var html = '<select class="ezoe-class-filter" multiple size="4" title="' + escapeHtml( t( 'Content classes' ) ) + '">' +
                   '<option value=""' + ( selected.length ? '' : ' selected' ) + '>' + escapeHtml( t( 'All' ) ) + '</option>';
        ( classes || [] ).forEach( function ( c ) {
            html += '<option value="' + escapeHtml( c.id ) + '"' + ( selected.indexOf( String( c.id ) ) !== -1 ? ' selected' : '' ) + '>' + escapeHtml( decodeHtml( c.name ) ) + '</option>';
        } );
        return html + '</select>';
    };

    // Selected class ids of the open dialog, empty for all
    var readClassFilter = function () {
        var select = document.querySelector( '.tox-dialog select.ezoe-class-filter' );
        if ( !select )
            return null;
        return Array.prototype.filter.call( select.options, function ( o ) {
            return o.selected && o.value;
        } ).map( function ( o ) {
            return o.value;
        } );
    };

    /**
     * Html of a content list like the TinyMCE 3 ezoe dialogs.
     *
     * @param {Object} list   result of search() / browse() / bookmarks()
     * @param {Object} options selected: value of the checked row, value: function( item ) -> row value,
     *                          browse: true to open containers on a click on the name, t: translate function,
     *                          previewAlias / rootUrl: image alias and root url for the preview column
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

        // like the old dialog: a preview link that shows the image on hover
        var previewHtml = function ( item ) {
            var url = options.previewAlias ? previewUrl( item, options.previewAlias, options.rootUrl ) : null;
            return url ? '<span class="ezoe-list-preview-link">' + escapeHtml( t( 'Preview' ) ) + '<img src="' + escapeHtml( encodeURI( url ) ) + '" alt="" /></span>' : '';
        };

        html += '<table class="ezoe-list"><tbody>';
        list.items.forEach( function ( item ) {
            var value = options.value( item ), name = escapeHtml( decodeHtml( item.name ) );
            html += '<tr data-ezoe-action="select" data-ezoe-value="' + escapeHtml( value ) + '"' + ( value === options.selected ? ' class="ezoe-list-selected"' : '' ) + '>' +
                    '<td class="ezoe-list-radio"><input type="radio" tabindex="-1"' + ( value === options.selected ? ' checked' : '' ) + ' /></td>' +
                    '<td class="ezoe-list-name">' +
                    ( options.browse && item.children_count
                        ? '<a href="#" data-ezoe-action="open" data-ezoe-value="' + item.node_id + '" title="' + escapeHtml( item.children_count ) + '">' + name + '</a>'
                        : name ) +
                    '</td><td class="ezoe-list-class">' + escapeHtml( decodeHtml( item.class_name ) ) + '</td>' +
                    '<td class="ezoe-list-preview">' + previewHtml( item ) + '</td></tr>';
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
        uploadLocations: uploadLocations,
        upload: upload,
        escapeHtml: escapeHtml,
        decodeHtml: decodeHtml,
        renderList: renderList,
        parseCustomAttributes: parseCustomAttributes,
        serializeCustomAttributes: serializeCustomAttributes,
        attributeFieldName: attributeFieldName,
        attributeField: attributeField,
        toAttributeFieldValue: toAttributeFieldValue,
        fromAttributeFieldValue: fromAttributeFieldValue,
        validateAttribute: validateAttribute,
        renderClassFilter: renderClassFilter,
        readClassFilter: readClassFilter,
        previewUrl: previewUrl,
        bindList: bindList
    };
}());

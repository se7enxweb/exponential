/**
 * eZ Online Editor - TinyMCE 8 prototype: embed / embed-inline plugin
 *
 * Produces the same XHTML markup as eZOEXMLInput::inputTagXML() so that the
 * existing eZOEInputParser can convert it back to ezxml unchanged:
 *
 *   images:  <img id="eZObject_12" src="..." alt="<size>" view="embed" inline="false" align="..." />
 *   others:  <div|span id="eZObject_12" class="ezoeItemNonEditable ezoeItemContentTypeObjects"
 *                 alt="<size>" view="embed" inline="false">rendered embed template</div|span>
 *
 * Server endpoints used (all existing ezoe / ezjscore views):
 *   ezoe/load/<EmbedID>/0/<size>    JSON data of the object (class, image aliases)
 *   ezoe/embed_view/<EmbedID>       rendered embed template for the editor preview
 *   ezjscore/call (ezjsc::search)   search for objects
 *
 * Licensed under the GNU General Public License v2.0, like the rest of ezoe.
 */
(function () {
    'use strict';

    var EMBED_ID_REGEX = /^eZ(Object|Node)_(\d+)$/;

    tinymce.addI18n( 'de', {
        'Embed object': 'Objekt einbetten',
        'Edit embedded object': 'Eingebettetes Objekt bearbeiten',
        'Remove embedded object': 'Eingebettetes Objekt entfernen',
        'Search': 'Suchen',
        'Name of the object': 'Name des Objekts',
        'Search result': 'Suchergebnis',
        'Search first or enter an ID below': 'Erst suchen oder unten eine ID eingeben',
        'No results': 'Keine Treffer',
        'Object': 'Objekt',
        'Object ID (12, eZObject_12 or eZNode_34)': 'Objekt-ID (12, eZObject_12 oder eZNode_34)',
        'Inline (embed-inline)': 'Im Fließtext (embed-inline)',
        'Image size': 'Bildgröße',
        'Alignment': 'Ausrichtung',
        'View': 'Ansicht',
        'Class': 'Klasse',
        'Default': 'Standard',
        'Please choose an object.': 'Bitte ein Objekt auswählen.',
        'Loading preview…': 'Vorschau wird geladen …',
        'Searching…': 'Suche läuft …',
        'Object not found or access denied': 'Objekt nicht gefunden oder kein Zugriff'
    } );

    tinymce.PluginManager.add( 'ezembed', function ( editor ) {

        editor.options.register( 'ez_settings', { processor: 'object', default: {} } );

        var settings = function () {
            return editor.options.get( 'ez_settings' );
        };

        // ezurl() strips the trailing slash
        var serverUrl = function ( name ) {
            return String( settings()[name] || '' ).replace( /\/?$/, '/' );
        };

        var t = function ( text ) {
            return editor.translate( text );
        };

        // Returns the embed element node is part of, or null
        var getEmbed = function ( node ) {
            return editor.dom.getParent( node, function ( n ) {
                return n.nodeType === 1 && EMBED_ID_REGEX.test( n.id || '' );
            }, editor.getBody() );
        };

        // Accepts "12", "eZObject_12", "eZNode_34", "object 12" and "node 34"
        var parseEmbedId = function ( value ) {
            var s = String( value || '' ).trim(), m;
            if ( /^\d+$/.test( s ) )
                return 'eZObject_' + s;
            if ( EMBED_ID_REGEX.test( s ) )
                return s;
            if ( ( m = s.match( /^(object|node)\s*[_:#\s]\s*(\d+)$/i ) ) )
                return ( m[1].toLowerCase() === 'node' ? 'eZNode_' : 'eZObject_' ) + m[2];
            return null;
        };

        var contentType = function ( classIdentifier ) {
            var groups = settings().relation_groups || {}, group;
            for ( group in groups )
            {
                if ( groups.hasOwnProperty( group ) && groups[group].indexOf( classIdentifier ) !== -1 )
                    return group;
            }
            return settings().relation_default_group || 'objects';
        };

        var absoluteUrl = function ( url ) {
            if ( !url || /^(https?:)?\//.test( url ) )
                return url;
            return ( settings().root_url || '/' ) + url;
        };

        var escapeAttr = function ( value ) {
            return String( value ).replace( /&/g, '&amp;' ).replace( /"/g, '&quot;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' );
        };

        var buildTag = function ( name, attrs, inner ) {
            var html = '<' + name, key;
            for ( key in attrs )
            {
                if ( attrs.hasOwnProperty( key ) && attrs[key] !== '' && attrs[key] !== null && attrs[key] !== undefined )
                    html += ' ' + key + '="' + escapeAttr( attrs[key] ) + '"';
            }
            return inner === null ? html + ' />' : html + '>' + inner + '</' + name + '>';
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

        var loadObject = function ( embedId, size ) {
            return request( serverUrl( 'extension_url' ) + 'load/' + embedId + '/0/' + encodeURIComponent( size ) )
                .then( function ( r ) { return r.json(); } );
        };

        var loadEmbedView = function ( embedId, params ) {
            var query = new URLSearchParams( params ).toString();
            return request( serverUrl( 'extension_url' ) + 'embed_view/' + embedId + '?' + query )
                .then( function ( r ) { return r.text(); } );
        };

        var search = function ( text ) {
            var body = new URLSearchParams();
            body.append( 'ezjscServer_function_arguments', 'ezjsc::search' );
            body.append( 'ezxform_token', settings().form_token || '' );
            body.append( 'SearchStr', text );
            body.append( 'SearchLimit', '15' );
            return request( serverUrl( 'ezjscore_url' ) + 'call', { method: 'POST', body: body } )
                .then( function ( r ) { return r.json(); } )
                .then( function ( data ) {
                    if ( data.error_text )
                        throw new Error( data.error_text );
                    return ( data.content && data.content.SearchResult ) || [];
                } );
        };

        var imageAlias = function ( object, size ) {
            var attr = object.image_attributes && object.image_attributes[0], content;
            if ( !attr || !object.data_map || !object.data_map[attr] )
                return null;
            content = object.data_map[attr].content;
            if ( !content || typeof content !== 'object' )
                return null;
            return content[size] || content.original || null;
        };

        // Builds the editor XHTML for an embed tag, see eZOEXMLInput::inputTagXML()
        var buildEmbedHtml = function ( embedId, data ) {
            var inline = !!data.inline,
                size = data.size || settings().default_size || 'medium',
                view = data.view || ( inline ? 'embed-inline' : 'embed' );

            return loadObject( embedId, size ).then( function ( object ) {
                if ( !object )
                    throw new Error( t( 'Object not found or access denied' ) + ': ' + embedId );

                var type = contentType( object.class_identifier ),
                    classes = ( data.cssClass || '' ).trim(),
                    attrs = {
                        id: embedId,
                        title: object.name,
                        alt: size,
                        view: view,
                        inline: inline ? 'true' : 'false'
                    };

                if ( type === 'images' && !settings().compatibility_mode )
                {
                    var alias = imageAlias( object, size ) || {};
                    attrs.src = absoluteUrl( alias.url ) || settings().attachment_icon;
                    attrs.width = alias.width || 32;
                    attrs.height = alias.height || 32;
                    if ( data.align === 'center' )
                    {
                        attrs.align = 'middle';
                        classes = ( classes + ' ezoeAlignmiddle' ).trim();
                    }
                    else
                        attrs.align = data.align;
                    attrs['class'] = classes;
                    return buildTag( 'img', attrs, null );
                }

                attrs.align = data.align;
                attrs['class'] = ( 'ezoeItemNonEditable ' + classes + ' ezoeItemContentType' +
                                   type.charAt( 0 ).toUpperCase() + type.slice( 1 ) ).replace( /\s+/g, ' ' );

                return loadEmbedView( embedId, {
                    inline: inline ? 'true' : 'false',
                    size: size,
                    view: view,
                    align: data.align || 'none',
                    'class': data.cssClass || ''
                } ).then( function ( inner ) {
                    return buildTag( inline ? 'span' : 'div', attrs, inner || object.name );
                } );
            } );
        };

        // Reads the dialog data from an existing embed element
        var readEmbed = function ( el ) {
            var dom = editor.dom,
                align = dom.getAttrib( el, 'align' ),
                cssClass = ( dom.getAttrib( el, 'class' ) || '' )
                    .replace( /\b(ezoeItem\w+|ezoeAlign\w+|mceItem\w+)\b/g, '' ).replace( /\s+/g, ' ' ).trim();
            return {
                query: '',
                result: '',
                embedId: el.id,
                inline: dom.getAttrib( el, 'inline' ) === 'true',
                size: dom.getAttrib( el, 'alt' ) || settings().default_size || 'medium',
                align: align === 'middle' ? 'center' : align,
                view: dom.getAttrib( el, 'view' ),
                cssClass: cssClass
            };
        };

        var listItems = function ( values, emptyText ) {
            var items = emptyText !== undefined ? [ { text: emptyText, value: '' } ] : [];
            ( values || [] ).forEach( function ( v ) {
                items.push( { text: v, value: v } );
            } );
            return items;
        };

        var openDialog = function ( target ) {
            var results = [ { text: t( 'Search first or enter an ID below' ), value: '' } ];

            var spec = function ( initialData ) {
                return {
                    title: target ? t( 'Edit embedded object' ) : t( 'Embed object' ),
                    size: 'medium',
                    body: {
                        type: 'panel',
                        items: [
                            {
                                type: 'bar',
                                items: [
                                    { type: 'input', name: 'query', label: t( 'Search' ), placeholder: t( 'Name of the object' ) },
                                    { type: 'button', name: 'search', text: t( 'Search' ), buttonType: 'secondary' }
                                ]
                            },
                            { type: 'listbox', name: 'result', label: t( 'Search result' ), items: results },
                            { type: 'input', name: 'embedId', label: t( 'Object ID (12, eZObject_12 or eZNode_34)' ) },
                            { type: 'checkbox', name: 'inline', label: t( 'Inline (embed-inline)' ) },
                            {
                                type: 'grid',
                                columns: 2,
                                items: [
                                    { type: 'listbox', name: 'size', label: t( 'Image size' ), items: listItems( settings().image_sizes ) },
                                    {
                                        type: 'listbox', name: 'align', label: t( 'Alignment' ), items: [
                                            { text: t( 'None' ), value: '' },
                                            { text: t( 'Left' ), value: 'left' },
                                            { text: t( 'Center' ), value: 'center' },
                                            { text: t( 'Right' ), value: 'right' }
                                        ]
                                    },
                                    { type: 'listbox', name: 'view', label: t( 'View' ), items: listItems( settings().view_modes, t( 'Default' ) ) },
                                    { type: 'input', name: 'cssClass', label: t( 'Class' ) }
                                ]
                            }
                        ]
                    },
                    initialData: initialData,
                    buttons: [
                        { type: 'cancel', text: t( 'Cancel' ) },
                        { type: 'submit', text: t( 'Save' ), primary: true }
                    ],
                    onChange: function ( api, details ) {
                        if ( details.name === 'result' && api.getData().result )
                            api.setData( { embedId: api.getData().result } );
                    },
                    onAction: function ( api, details ) {
                        if ( details.name === 'search' )
                            runSearch( api );
                    },
                    onSubmit: function ( api ) {
                        var data = api.getData(), embedId = parseEmbedId( data.embedId );

                        // Enter in the search field submits the dialog, treat it as search
                        if ( !embedId && String( data.query ).trim() )
                            return runSearch( api );

                        if ( !embedId )
                        {
                            editor.notificationManager.open( { text: t( 'Please choose an object.' ), type: 'warning', timeout: 4000 } );
                            return;
                        }

                        api.block( t( 'Loading preview…' ) );
                        buildEmbedHtml( embedId, data ).then( function ( html ) {
                            api.close();
                            if ( target )
                                editor.selection.select( target );
                            editor.insertContent( html );
                        } ).catch( function ( e ) {
                            api.unblock();
                            editor.notificationManager.open( { text: e.message, type: 'error' } );
                        } );
                    }
                };
            };

            var runSearch = function ( api ) {
                var data = api.getData(), text = String( data.query ).trim();
                if ( !text )
                    return;
                api.block( t( 'Searching…' ) );
                search( text ).then( function ( list ) {
                    results = list.length
                        ? list.map( function ( n ) {
                            return {
                                text: n.name + ' (' + n.class_name + ', ' + t( 'Object' ) + ' ' + n.contentobject_id + ')',
                                value: 'eZObject_' + n.contentobject_id
                            };
                        } )
                        : [ { text: t( 'No results' ), value: '' } ];
                    data.result = results[0].value;
                    if ( results[0].value )
                        data.embedId = results[0].value;
                    api.redial( spec( data ) );
                    api.focus( 'result' );
                } ).catch( function ( e ) {
                    api.unblock();
                    editor.notificationManager.open( { text: e.message, type: 'error' } );
                } );
            };

            editor.windowManager.open( spec( target ? readEmbed( target ) : {
                query: '',
                result: '',
                embedId: '',
                inline: false,
                size: settings().default_size || 'medium',
                align: '',
                view: '',
                cssClass: ''
            } ) );
        };

        var openForSelection = function () {
            openDialog( getEmbed( editor.selection.getNode() ) );
        };

        editor.addCommand( 'ezEmbed', openForSelection );

        editor.ui.registry.addToggleButton( 'ezembed', {
            icon: 'embed',
            tooltip: t( 'Embed object' ),
            onAction: openForSelection,
            onSetup: function ( api ) {
                var handler = function ( e ) {
                    api.setActive( !!getEmbed( e.element ) );
                };
                editor.on( 'NodeChange', handler );
                return function () {
                    editor.off( 'NodeChange', handler );
                };
            }
        } );

        editor.ui.registry.addButton( 'ezembedremove', {
            icon: 'remove',
            tooltip: t( 'Remove embedded object' ),
            onAction: function () {
                var el = getEmbed( editor.selection.getNode() );
                if ( el )
                    editor.undoManager.transact( function () {
                        editor.dom.remove( el );
                    } );
            }
        } );

        editor.ui.registry.addMenuItem( 'ezembed', {
            icon: 'embed',
            text: t( 'Embed object' ) + '…',
            onAction: openForSelection
        } );

        editor.ui.registry.addContextToolbar( 'ezembed', {
            predicate: function ( node ) {
                return !!getEmbed( node );
            },
            items: 'ezembed ezembedremove',
            position: 'node',
            scope: 'node'
        } );

        editor.on( 'dblclick', function ( e ) {
            var el = getEmbed( e.target );
            if ( el )
            {
                e.preventDefault();
                openDialog( el );
            }
        } );

        // Like the TinyMCE 3 ez theme: only the textarea content is cleaned up, not the editor.
        // The preview markup inside embed tags is replaced by 'ezembed' (see issue 18264) and
        // ezoeAlign* helper classes are removed (see EZP-22487).
        editor.on( 'SaveContent', function ( e ) {
            if ( !e.content )
                return;

            var doc = new DOMParser().parseFromString( '<!DOCTYPE html><html><body>' + e.content + '</body></html>', 'text/html' );

            doc.body.querySelectorAll( 'div.ezoeItemNonEditable, span.ezoeItemNonEditable' ).forEach( function ( node ) {
                node.textContent = 'ezembed';
            } );

            doc.body.querySelectorAll( '[class*="ezoeAlign"]' ).forEach( function ( node ) {
                node.classList.remove( 'ezoeAlign' + node.getAttribute( 'align' ) );
                if ( !node.getAttribute( 'class' ) )
                    node.removeAttribute( 'class' );
            } );

            e.content = doc.body.innerHTML;
        } );
    } );
}());

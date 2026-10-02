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
 * The dialog works like the TinyMCE 3 ezoe object dialog: Upload creates a new object,
 * Search, Browse and Bookmarks list the content as table, choosing or uploading continues on
 * the Properties tab.
 * Server calls and the list come from ezoe_dialog.js (eZOe8Dialog).
 *
 * Licensed under the GNU General Public License v2.0, like the rest of ezoe.
 */
(function () {
    'use strict';

    var EMBED_ID_REGEX = /^eZ(Object|Node)_(\d+)$/;
    var D = window.eZOe8Dialog;

    tinymce.addI18n( 'de', {
        'Embed object': 'Objekt einbetten',
        'Edit embedded object': 'Eingebettetes Objekt bearbeiten',
        'Remove embedded object': 'Eingebettetes Objekt entfernen',
        'Search': 'Suchen',
        'Browse': 'Durchsuchen',
        'Bookmarks': 'Lesezeichen',
        'Upload': 'Hochladen',
        'File': 'Datei',
        'Location': 'Speicherort',
        'Alternative text (images)': 'Alternativtext (Bilder)',
        'Description': 'Beschreibung',
        'Upload local file': 'Datei hochladen',
        'Uploading…': 'Wird hochgeladen …',
        'Please choose a file.': 'Bitte eine Datei auswählen.',
        'Drop a file here': 'Datei hierher ziehen',
        'Browse for a file': 'Datei auswählen',
        'This file type can not be uploaded, allowed are:': 'Dieser Dateityp kann nicht hochgeladen werden, erlaubt sind:',
        'The file name is used if no name is given.': 'Ohne Namen wird der Dateiname verwendet.',
        'Properties': 'Eigenschaften',
        'Name of the object': 'Name des Objekts',
        'Selected': 'Ausgewählt',
        'Nothing selected yet, choose an object in Search, Browse or Bookmarks.': 'Noch nichts ausgewählt, bitte ein Objekt unter Suchen, Durchsuchen oder Lesezeichen wählen.',
        'Object ID (12, eZObject_12 or eZNode_34)': 'Objekt-ID (12, eZObject_12 oder eZNode_34)',
        'Inline (embed-inline)': 'Im Fließtext (embed-inline)',
        'Image size': 'Bildgröße',
        'Alignment': 'Ausrichtung',
        'View': 'Ansicht',
        'Class': 'Klasse',
        'Default': 'Standard',
        'Please choose an object.': 'Bitte ein Objekt auswählen.',
        'Loading…': 'Wird geladen …',
        'Loading preview…': 'Vorschau wird geladen …',
        'Searching…': 'Suche läuft …',
        'Object not found or access denied': 'Objekt nicht gefunden oder kein Zugriff'
    } );

    tinymce.PluginManager.add( 'ezembed', function ( editor ) {

        editor.options.register( 'ez_settings', { processor: 'object', default: {} } );

        var settings = function () {
            return editor.options.get( 'ez_settings' );
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

        var buildTag = function ( name, attrs, inner ) {
            var html = '<' + name, key;
            for ( key in attrs )
            {
                if ( attrs.hasOwnProperty( key ) && attrs[key] !== '' && attrs[key] !== null && attrs[key] !== undefined )
                    html += ' ' + key + '="' + D.escapeHtml( attrs[key] ) + '"';
            }
            return inner === null ? html + ' />' : html + '>' + inner + '</' + name + '>';
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

            return D.loadObject( settings(), embedId, size ).then( function ( object ) {
                if ( !object )
                    throw new Error( t( 'Object not found or access denied' ) + ': ' + embedId );

                var type = contentType( object.class_identifier ),
                    classes = ( data.cssClass || '' ).trim(),
                    attrs = {
                        id: embedId,
                        title: D.decodeHtml( object.name ),
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

                return D.loadEmbedView( settings(), embedId, {
                    inline: inline ? 'true' : 'false',
                    size: size,
                    view: view,
                    align: data.align || 'none',
                    'class': data.cssClass || ''
                } ).then( function ( inner ) {
                    return buildTag( inline ? 'span' : 'div', attrs, inner || D.escapeHtml( attrs.title ) );
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
            var data = Object.assign( target ? readEmbed( target ) : {
                    query: '', embedId: '', inline: false, size: settings().default_size || 'medium', align: '', view: '', cssClass: ''
                }, { uploadName: '', uploadFile: [], uploadLocation: 'auto', uploadAlt: '', uploadDescription: '' } ),
                state = {
                    tab: target ? 'properties' : 'upload',
                    search: null, searchText: '',
                    browse: null,
                    bookmarks: null,
                    selectedName: target ? target.getAttribute( 'title' ) : ''
                },
                unbind, api;

            var rowValue = function ( item ) {
                return 'eZObject_' + item.contentobject_id;
            };

            var listHtml = function ( list, browse ) {
                if ( !list )
                    return '<p class="ezoe-list-empty">' + D.escapeHtml( t( 'Loading…' ) ) + '</p>';
                return D.renderList( list, { t: t, value: rowValue, previewAlias: settings().browse_image_alias, rootUrl: settings().root_url, selected: data.embedId, browse: browse } );
            };

            var selectedHtml = function () {
                return '<p class="ezoe-selected">' + ( data.embedId
                    ? D.escapeHtml( t( 'Selected' ) ) + ': <strong>' + D.escapeHtml( state.selectedName || data.embedId ) + '</strong> (' + D.escapeHtml( data.embedId ) + ')'
                    : D.escapeHtml( t( 'Nothing selected yet, choose an object in Search, Browse or Bookmarks.' ) ) ) + '</p>';
            };

            var spec = function () {
                return {
                    title: target ? t( 'Edit embedded object' ) : t( 'Embed object' ),
                    size: 'medium',
                    body: {
                        type: 'tabpanel',
                        tabs: [
                            {
                                name: 'upload',
                                title: t( 'Upload' ),
                                items: [
                                    { type: 'input', name: 'uploadName', label: t( 'Name' ), placeholder: t( 'The file name is used if no name is given.' ) },
                                    {
                                        type: 'dropzone', name: 'uploadFile', label: t( 'File' ),
                                        dropAreaLabel: t( 'Drop a file here' ), buttonLabel: t( 'Browse for a file' ),
                                        // any file like the TinyMCE 3 upload dialog, the class is chosen by upload.ini
                                        allowedFileTypes: '*/*',
                                        allowedFileExtensions: settings().upload_file_extensions || [],
                                        onInvalidFiles: function () {
                                            editor.notificationManager.open( {
                                                text: t( 'This file type can not be uploaded, allowed are:' ) + ' ' + ( settings().upload_file_extensions || [] ).join( ', ' ),
                                                type: 'warning', timeout: 6000
                                            } );
                                            return Promise.resolve();
                                        }
                                    },
                                    { type: 'listbox', name: 'uploadLocation', label: t( 'Location' ), items: locations || [ { text: t( 'Loading…' ), value: 'auto' } ] },
                                    { type: 'input', name: 'uploadAlt', label: t( 'Alternative text (images)' ) },
                                    { type: 'input', name: 'uploadDescription', label: t( 'Description' ) },
                                    { type: 'bar', items: [ { type: 'button', name: 'uploadRun', text: t( 'Upload local file' ), buttonType: 'secondary' } ] }
                                ]
                            },
                            {
                                name: 'search',
                                title: t( 'Search' ),
                                items: [
                                    {
                                        type: 'bar', items: [
                                            { type: 'input', name: 'query', label: t( 'Search' ), placeholder: t( 'Name of the object' ) },
                                            { type: 'button', name: 'searchRun', text: t( 'Search' ), buttonType: 'secondary' }
                                        ]
                                    },
                                    { type: 'htmlpanel', html: D.renderClassFilter( settings().search_classes, state.searchClasses, t ) },
                                    { type: 'htmlpanel', html: state.search ? listHtml( state.search ) : '' }
                                ]
                            },
                            {
                                name: 'browse',
                                title: t( 'Browse' ),
                                items: [ { type: 'htmlpanel', html: listHtml( state.browse, true ) } ]
                            },
                            {
                                name: 'bookmarks',
                                title: t( 'Bookmarks' ),
                                items: [ { type: 'htmlpanel', html: listHtml( state.bookmarks ) } ]
                            },
                            {
                                name: 'properties',
                                title: t( 'Properties' ),
                                items: [
                                    { type: 'htmlpanel', html: selectedHtml() },
                                    { type: 'input', name: 'embedId', label: t( 'Object ID (12, eZObject_12 or eZNode_34)' ) },
                                    { type: 'checkbox', name: 'inline', label: t( 'Inline (embed-inline)' ) },
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
                    initialData: data,
                    buttons: [
                        { type: 'cancel', text: t( 'Cancel' ) },
                        { type: 'submit', text: 'OK', primary: true }
                    ],
                    onTabChange: function ( dialogApi, details ) {
                        state.tab = details.newTabName;
                        if ( state.tab === 'browse' && !state.browse )
                            load( dialogApi, 'browse', D.browse( settings(), settings().root_node || 2, 0 ) );
                        else if ( state.tab === 'bookmarks' && !state.bookmarks )
                            load( dialogApi, 'bookmarks', D.bookmarks( settings(), 0 ) );
                    },
                    onAction: function ( dialogApi, details ) {
                        if ( details.name === 'searchRun' )
                            runSearch( dialogApi, 0 );
                        else if ( details.name === 'uploadRun' )
                            runUpload( dialogApi );
                    },
                    onSubmit: function ( dialogApi ) {
                        var d = dialogApi.getData(), embedId = parseEmbedId( d.embedId );

                        // Enter in the search field submits the dialog, treat it as search
                        if ( state.tab === 'search' && String( d.query ).trim() )
                            return runSearch( dialogApi, 0 );
                        // OK on the upload tab with a chosen file uploads it first
                        if ( state.tab === 'upload' && d.uploadFile && d.uploadFile.length )
                            return runUpload( dialogApi );

                        if ( !embedId )
                        {
                            editor.notificationManager.open( { text: t( 'Please choose an object.' ), type: 'warning', timeout: 4000 } );
                            return;
                        }

                        dialogApi.block( t( 'Loading preview…' ) );
                        buildEmbedHtml( embedId, d ).then( function ( html ) {
                            dialogApi.unblock();
                            dialogApi.close();
                            if ( target )
                                editor.selection.select( target );
                            editor.insertContent( html );
                        } ).catch( function ( e ) {
                            fail( dialogApi, e );
                        } );
                    },
                    onClose: function () {
                        if ( unbind )
                            unbind();
                    }
                };
            };

            var fail = function ( dialogApi, e ) {
                dialogApi.unblock();
                editor.notificationManager.open( { text: e.message, type: 'error' } );
            };

            // Rebuilds the dialog with the current data and state; redial() shows the first tab
            // and fires onTabChange, so the tab is remembered before and the block is lifted first
            // the class filter is plain html, keep its selection over a rebuild of the dialog
            var rememberClassFilter = function () {
                var classes = D.readClassFilter();
                if ( classes !== null )
                    state.searchClasses = classes;
            };

            var redial = function ( dialogApi, changes ) {
                var tab = state.tab;
                rememberClassFilter();
                data = Object.assign( dialogApi.getData(), changes || {} );
                dialogApi.unblock();
                dialogApi.redial( spec() );
                state.tab = tab;
                dialogApi.showTab( tab );
            };

            var load = function ( dialogApi, key, promise ) {
                dialogApi.block( t( 'Loading…' ) );
                promise.then( function ( list ) {
                    state[key] = list;
                    redial( dialogApi );
                } ).catch( function ( e ) {
                    fail( dialogApi, e );
                } );
            };

            var runSearch = function ( dialogApi, offset ) {
                var text = String( dialogApi.getData().query || state.searchText ).trim();
                if ( !text )
                    return;
                state.searchText = text;
                rememberClassFilter();
                dialogApi.block( t( 'Searching…' ) );
                D.search( settings(), text, offset, state.searchClasses ).then( function ( list ) {
                    state.search = list;
                    // the query stays in the field, but Enter should not search again after a selection
                    redial( dialogApi );
                } ).catch( function ( e ) {
                    fail( dialogApi, e );
                } );
            };

            var runUpload = function ( dialogApi ) {
                var d = dialogApi.getData(), file = d.uploadFile && d.uploadFile[0];
                if ( !file )
                {
                    editor.notificationManager.open( { text: t( 'Please choose a file.' ), type: 'warning', timeout: 4000 } );
                    return;
                }
                dialogApi.block( t( 'Uploading…' ) );
                D.upload( settings(), file, {
                    name: d.uploadName,
                    location: d.uploadLocation,
                    alternativeText: d.uploadAlt,
                    description: d.uploadDescription
                } ).then( function ( result ) {
                    // like eZOEPopupUtils.selectByEmbedId(): select the new object and continue on Properties
                    state.selectedName = result.name;
                    state.tab = 'properties';
                    redial( dialogApi, {
                        embedId: 'eZObject_' + result.objectId,
                        uploadName: '', uploadFile: [], uploadAlt: '', uploadDescription: ''
                    } );
                } ).catch( function ( e ) {
                    fail( dialogApi, e );
                } );
            };

            var findItem = function ( value ) {
                return [ state.search, state.browse, state.bookmarks ].reduce( function ( found, list ) {
                    return found || ( list && list.items.filter( function ( i ) {
                        return rowValue( i ) === value;
                    } )[0] );
                }, null );
            };

            unbind = D.bindList( function ( action, value ) {
                if ( action === 'select' )
                {
                    var item = findItem( value );
                    state.selectedName = item ? D.decodeHtml( item.name ) : '';
                    state.tab = 'properties';
                    redial( api, { embedId: value, query: '' } );
                }
                else if ( action === 'open' )
                {
                    load( api, 'browse', D.browse( settings(), value, 0 ) );
                }
                else if ( action === 'page' )
                {
                    if ( state.tab === 'search' )
                        runSearch( api, parseInt( value, 10 ) );
                    else if ( state.tab === 'browse' )
                        load( api, 'browse', D.browse( settings(), state.browse.node.node_id, parseInt( value, 10 ) ) );
                    else
                        load( api, 'bookmarks', D.bookmarks( settings(), parseInt( value, 10 ) ) );
                }
            } );

            api = editor.windowManager.open( spec() );
            api.showTab( state.tab );

            if ( !locations )
            {
                locationsPromise = locationsPromise || D.uploadLocations( settings() );
                locationsPromise.then( function ( list ) {
                    locations = list;
                    // only rebuild while nothing is chosen in the dropzone, files can not be put back into it
                    var d = api.getData();
                    if ( !d.uploadFile || !d.uploadFile.length )
                        redial( api );
                } ).catch( function () {
                    locations = [ { text: 'auto', value: 'auto' } ];
                } );
            }
        };

        // storage locations for uploads, loaded once per editor
        var locations = null, locationsPromise = null;

        var openForSelection = function () {
            openDialog( getEmbed( editor.selection.getNode() ) );
        };

        editor.addCommand( 'ezEmbed', openForSelection );

        editor.ui.registry.addToggleButton( 'ezembed', {
            icon: 'embed',
            tooltip: t( 'Embed object' ),
            onAction: openForSelection,
            onSetup: function ( buttonApi ) {
                var handler = function ( e ) {
                    buttonApi.setActive( !!getEmbed( e.element ) );
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

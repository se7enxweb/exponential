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
        'Uploaded, press OK to embed it:': 'Hochgeladen, mit OK einbetten:',
        'Chosen file': 'Gewählte Datei',
        'Relation': 'Relation',
        'Object': 'Objekt',
        'Node': 'Knoten',
        'Size': 'Größe',
        'Align': 'Ausrichtung',
        'Inline': 'Im Fließtext',
        'Left': 'Links',
        'Center': 'Mitte',
        'Right': 'Rechts',
        'None': 'Keine',
        'Preview': 'Vorschau',
        'Switch embed object': 'Anderes Objekt wählen',
        'Edit object': 'Objekt bearbeiten',
        'Edit <embed> tag': 'Tag <embed> bearbeiten',
        'New <embed> tag': 'Neues Tag <embed>',
        'Drop a file here': 'Datei hierher ziehen',
        'Browse for a file': 'Datei auswählen',
        'This file type can not be uploaded, allowed are:': 'Dieser Dateityp kann nicht hochgeladen werden, erlaubt sind:',
        'The file name is used if no name is given.': 'Ohne Namen wird der Dateiname verwendet.',
        'Properties': 'Eigenschaften',
        'Name of the object': 'Name des Objekts',
        'Selected': 'Ausgewählt',
        'Nothing selected yet, choose an object in Search, Browse or Bookmarks.': 'Noch nichts ausgewählt, bitte ein Objekt unter Suchen, Durchsuchen oder Lesezeichen wählen.',
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

        // Builds the editor XHTML for an embed tag, see eZOEXMLInput::inputTagXML() and the
        // tagAttributeEditor of design:ezoe/tag_embed_*.tpl; data.customAttributes is the
        // serialized customattributes value
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
                        inline: inline ? 'true' : 'false',
                        customattributes: data.customAttributes || null
                    };

                if ( type === 'images' && !settings().compatibility_mode )
                {
                    var alias = imageAlias( object, size ) || {};
                    attrs.src = absoluteUrl( alias.url ) || settings().attachment_icon;
                    attrs.width = alias.width || 32;
                    attrs.height = alias.height || 32;
                    if ( alias.alternative_text )
                        attrs.title = D.decodeHtml( alias.alternative_text );
                    // like the old dialog: center is stored as middle on images and an ezoeAlign* class
                    // makes the alignment visible in the editor (eZOEInputParser removes it again)
                    attrs.align = data.align === 'center' ? 'middle' : data.align;
                    if ( attrs.align )
                        classes = ( classes + ' ezoeAlign' + attrs.align ).trim();
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

        // Definitions of embed / embed-inline: classes, custom attributes and view modes from content.ini
        var tagDefinition = function ( inline ) {
            var defs = settings().embed_definitions || {}, d = defs[ inline ? 'embed-inline' : 'embed' ] || {};
            return {
                classes: Array.isArray( d.classes ) ? {} : ( d.classes || {} ),
                attributes: d.attributes || [],
                views: d.views && d.views.length ? d.views : [ inline ? 'embed-inline' : 'embed' ]
            };
        };

        // Reads the dialog data from an existing embed element
        var readEmbed = function ( el ) {
            var dom = editor.dom,
                align = dom.getAttrib( el, 'align' ),
                inline = dom.getAttrib( el, 'inline' ) === 'true',
                stored = D.parseCustomAttributes( dom.getAttrib( el, 'customattributes' ) ),
                data = {
                    query: '',
                    embedId: el.id,
                    inline: inline,
                    size: dom.getAttrib( el, 'alt' ) || settings().default_size || 'medium',
                    align: align === 'middle' ? 'center' : align,
                    view: dom.getAttrib( el, 'view' ) || ( inline ? 'embed-inline' : 'embed' ),
                    cssClass: ( dom.getAttrib( el, 'class' ) || '' )
                        .replace( /\b(ezoeItem\w+|ezoeAlign\w+|mceItem\w+)\b/g, '' ).replace( /\s+/g, ' ' ).trim()
                };
            tagDefinition( inline ).attributes.forEach( function ( attribute ) {
                data[ D.attributeFieldName( attribute ) ] = D.toAttributeFieldValue( attribute, stored[attribute.id] );
            } );
            return data;
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
                    query: '', embedId: '', inline: false, size: settings().default_size || 'medium', align: '', view: 'embed', cssClass: ''
                }, { uploadName: '', uploadFile: [], uploadLocation: 'auto', uploadAlt: '', uploadDescription: '' } ),
                state = {
                    // like the old dialog an existing embed only shows its properties until "Switch embed object"
                    choosing: !target,
                    tab: target ? 'properties' : 'upload',
                    search: null, searchText: '',
                    browse: null,
                    bookmarks: null,
                    object: null,
                    objectFor: null
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

            var objectId = function () {
                var m = String( data.embedId || '' ).match( /^eZObject_(\d+)$/ );
                return m ? m[1] : ( state.object ? String( state.object.contentobject_id || state.object.id ) : null );
            };

            // Relation like the old dialog: embed the object or its main node
            var relationItems = function () {
                var o = state.object;
                if ( !o )
                    return [ { text: data.embedId || '', value: data.embedId || '' } ];
                var items = [ { text: t( 'Object' ) + ': ' + D.decodeHtml( o.name ), value: 'eZObject_' + ( o.contentobject_id || o.id ) } ];
                if ( o.main_node_id )
                    items.push( { text: t( 'Node' ) + ': ' + D.decodeHtml( o.path_identification_string || o.url_alias || o.main_node_id ), value: 'eZNode_' + o.main_node_id } );
                // keep an embedded node that is not the main node
                if ( items.every( function ( i ) { return i.value !== data.embedId; } ) && data.embedId )
                    items.push( { text: data.embedId, value: data.embedId } );
                return items;
            };

            var classItems = function ( def ) {
                var items = [ { text: t( 'None' ), value: '' } ];
                Object.keys( def.classes ).forEach( function ( key ) {
                    items.push( { text: def.classes[key], value: key } );
                } );
                if ( data.cssClass && !def.classes[data.cssClass] )
                    items.push( { text: data.cssClass, value: data.cssClass } );
                return items;
            };

            var viewItems = function ( def ) {
                var views = def.views.slice();
                if ( data.view && views.indexOf( data.view ) === -1 )
                    views.push( data.view );
                return listItems( views );
            };

            var isImage = function () {
                return !!state.object && contentType( state.object.class_identifier ) === 'images';
            };

            var propertiesItems = function () {
                var def = tagDefinition( data.inline ), items = [
                    { type: 'htmlpanel', html: '<h2 class="ezoe-tag-title">' + D.escapeHtml( target ? t( 'Edit <embed> tag' ) : t( 'New <embed> tag' ) ) + '</h2>' }
                ];
                if ( !data.embedId )
                    return items.concat( [ { type: 'htmlpanel', html: '<p class="ezoe-selected">' + D.escapeHtml( t( 'Nothing selected yet, choose an object in Search, Browse or Bookmarks.' ) ) + '</p>' } ] );

                items.push( { type: 'listbox', name: 'embedId', label: t( 'Relation' ), items: relationItems() } );
                if ( isImage() )
                    items.push( { type: 'listbox', name: 'size', label: t( 'Size' ), items: listItems( settings().image_sizes ) } );
                items.push(
                    { type: 'listbox', name: 'view', label: t( 'View' ), items: viewItems( def ) },
                    { type: 'listbox', name: 'cssClass', label: t( 'Class' ), items: classItems( def ) },
                    {
                        type: 'listbox', name: 'align', label: t( 'Align' ), items: [
                            { text: t( 'None' ), value: '' },
                            { text: t( 'Left' ), value: 'left' },
                            { text: t( 'Center' ), value: 'center' },
                            { text: t( 'Right' ), value: 'right' }
                        ]
                    },
                    { type: 'checkbox', name: 'inline', label: t( 'Inline' ) }
                );
                def.attributes.forEach( function ( attribute ) {
                    if ( attribute.type !== 'hidden' )
                        items.push( D.attributeField( attribute ) );
                } );
                items.push(
                    {
                        type: 'bar', items: [
                            { type: 'button', name: 'switchObject', text: t( 'Switch embed object' ), buttonType: 'secondary' },
                            { type: 'button', name: 'editObject', text: t( 'Edit object' ), buttonType: 'secondary', enabled: !!objectId() }
                        ]
                    },
                    { type: 'htmlpanel', html: '<div class="ezoe-embed-preview-label">' + D.escapeHtml( t( 'Preview' ) ) + ':</div><div class="ezoe-embed-preview">' + ( state.preview || '' ) + '</div>' }
                );
                return items;
            };

            var uploadItems = function () {
                return [
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
                    // the dropzone does not show the chosen file, see showChosenFile()
                    { type: 'htmlpanel', html: '<p class="ezoe-upload-file"></p>' },
                    { type: 'listbox', name: 'uploadLocation', label: t( 'Location' ), items: locations || [ { text: t( 'Loading…' ), value: 'auto' } ] },
                    { type: 'input', name: 'uploadAlt', label: t( 'Alternative text (images)' ) },
                    { type: 'input', name: 'uploadDescription', label: t( 'Description' ) },
                    { type: 'bar', items: [ { type: 'button', name: 'uploadRun', text: t( 'Upload local file' ), buttonType: 'secondary' } ] }
                ];
            };

            var spec = function () {
                var tabs = [];
                if ( state.choosing )
                {
                    tabs.push(
                        { name: 'upload', title: t( 'Upload' ), items: uploadItems() },
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
                        { name: 'browse', title: t( 'Browse' ), items: [ { type: 'htmlpanel', html: listHtml( state.browse, true ) } ] },
                        { name: 'bookmarks', title: t( 'Bookmarks' ), items: [ { type: 'htmlpanel', html: listHtml( state.bookmarks ) } ] }
                    );
                }
                tabs.push( { name: 'properties', title: t( 'Properties' ), items: propertiesItems() } );

                return {
                    title: target ? t( 'Edit embedded object' ) : t( 'Embed object' ),
                    size: 'medium',
                    body: { type: 'tabpanel', tabs: tabs },
                    initialData: data,
                    buttons: [
                        { type: 'cancel', text: t( 'Cancel' ) },
                        { type: 'submit', text: 'OK', primary: true }
                    ],
                    onTabChange: function ( dialogApi, details ) {
                        state.tab = details.newTabName;
                        if ( state.tab === 'upload' && state.locationsPending && !state.redialing )
                        {
                            state.locationsPending = false;
                            var d = dialogApi.getData();
                            if ( !d.uploadFile || !d.uploadFile.length )
                                redial( dialogApi );
                        }
                        else if ( state.tab === 'browse' && !state.browse )
                            load( dialogApi, 'browse', D.browse( settings(), settings().root_node || 2, 0 ) );
                        else if ( state.tab === 'bookmarks' && !state.bookmarks )
                            load( dialogApi, 'bookmarks', D.bookmarks( settings(), 0 ) );
                    },
                    onChange: function ( dialogApi, details ) {
                        var d = dialogApi.getData();
                        if ( details.name === 'uploadFile' )
                            showChosenFile( dialogApi );
                        else if ( details.name === 'inline' )
                        {
                            // embed and embed-inline have their own views, classes and custom attributes
                            var views = tagDefinition( d.inline ).views;
                            redial( dialogApi, { view: views.indexOf( d.view ) !== -1 ? d.view : views[0], cssClass: '' } );
                            updatePreview( dialogApi );
                        }
                        else if ( /^(embedId|size|view|cssClass|align)$/.test( details.name ) )
                            updatePreview( dialogApi );
                    },
                    onAction: function ( dialogApi, details ) {
                        if ( details.name === 'searchRun' )
                            runSearch( dialogApi, 0 );
                        else if ( details.name === 'uploadRun' )
                            runUpload( dialogApi );
                        else if ( details.name === 'switchObject' )
                        {
                            state.choosing = true;
                            state.tab = 'search';
                            redial( dialogApi );
                        }
                        else if ( details.name === 'editObject' && objectId() )
                            window.open( String( settings().content_edit_url || '' ).replace( /\/?$/, '/' ) + objectId(), '_blank' );
                    },
                    onSubmit: function ( dialogApi ) {
                        var d = dialogApi.getData(), embedId = parseEmbedId( d.embedId );

                        // Enter in the search field submits the dialog, treat it as search
                        if ( state.tab === 'search' && String( d.query ).trim() )
                            return runSearch( dialogApi, 0 );
                        // OK on the upload tab with a chosen file uploads it and embeds it right away
                        if ( state.tab === 'upload' && d.uploadFile && d.uploadFile.length )
                            return runUpload( dialogApi, true );

                        if ( !embedId )
                        {
                            editor.notificationManager.open( { text: t( 'Please choose an object.' ), type: 'warning', timeout: 4000 } );
                            return;
                        }

                        var custom = collectCustomAttributes( d );
                        if ( custom.error )
                        {
                            editor.notificationManager.open( { text: custom.error, type: 'warning', timeout: 4000 } );
                            dialogApi.showTab( 'properties' );
                            dialogApi.focus( custom.field );
                            return;
                        }
                        insertEmbed( dialogApi, embedId, Object.assign( d, { customAttributes: custom.value } ) );
                    },
                    onClose: function () {
                        if ( unbind )
                            unbind();
                    }
                };
            };

            // default values of the custom attributes, for a newly chosen object like the old dialog
            var attributeDefaults = function ( inline ) {
                var values = {};
                tagDefinition( inline ).attributes.forEach( function ( attribute ) {
                    values[ D.attributeFieldName( attribute ) ] = D.toAttributeFieldValue( attribute, undefined );
                } );
                return values;
            };

            // custom attributes of the current tag, validated like the old dialog
            var collectCustomAttributes = function ( d ) {
                var values = {}, result = { value: '', error: null, field: null };
                tagDefinition( d.inline ).attributes.forEach( function ( attribute ) {
                    var name = D.attributeFieldName( attribute ),
                        value = D.fromAttributeFieldValue( attribute, attribute.type === 'hidden' ? attribute['default'] : d[name] ),
                        message = attribute.type === 'hidden' ? null : D.validateAttribute( attribute, value, t );
                    if ( message && !result.error )
                    {
                        result.error = message;
                        result.field = name;
                    }
                    if ( value !== null )
                        values[attribute.id] = value;
                } );
                result.value = D.serializeCustomAttributes( values );
                return result;
            };

            var showChosenFile = function ( dialogApi ) {
                var d = dialogApi.getData(), file = d.uploadFile && d.uploadFile[0],
                    el = document.querySelector( '.tox-dialog .ezoe-upload-file' );
                if ( el )
                    el.textContent = file ? t( 'Chosen file' ) + ': ' + file.name + ' (' + Math.max( 1, Math.round( file.size / 1024 ) ) + ' KB)' : '';
                if ( file && !String( d.uploadName ).trim() )
                    dialogApi.setData( { uploadName: file.name.replace( /\.[^.]+$/, '' ) } );
            };

            var fail = function ( dialogApi, e ) {
                dialogApi.unblock();
                editor.notificationManager.open( { text: e.message, type: 'error' } );
            };

            // the class filter is plain html, keep its selection over a rebuild of the dialog
            var rememberClassFilter = function () {
                var classes = D.readClassFilter();
                if ( classes !== null )
                    state.searchClasses = classes;
            };

            // Rebuilds the dialog with the current data and state; redial() shows the first tab
            // and fires onTabChange, so the tab is remembered before and the block is lifted first
            var redial = function ( dialogApi, changes ) {
                var tab = state.tab;
                rememberClassFilter();
                // fields that are not in the dialog right now (e.g. the size before an image is chosen) keep their value
                data = Object.assign( {}, data, dialogApi.getData(), changes || {} );
                dialogApi.unblock();
                state.redialing = true;
                dialogApi.redial( spec() );
                state.tab = tab;
                dialogApi.showTab( tab );
                state.redialing = false;
                D.revealClassFilter();
                loadObjectInfo( dialogApi );
            };

            // Object data for the relation list, the size list (images only) and the preview
            var loadObjectInfo = function ( dialogApi ) {
                var embedId = parseEmbedId( data.embedId );
                if ( !embedId || state.objectFor === embedId || ( state.object && state.objectFor && embedId === 'eZNode_' + state.object.main_node_id ) )
                    return;
                state.objectFor = embedId;
                D.loadObject( settings(), embedId, data.size ).then( function ( object ) {
                    state.object = object || null;
                    redial( dialogApi );
                    updatePreview( dialogApi );
                } ).catch( function () {} );
            };

            // Preview of the embed with the current settings, like the preview of the old dialog
            var previewTimer = null;
            var updatePreview = function ( dialogApi ) {
                clearTimeout( previewTimer );
                previewTimer = setTimeout( function () {
                    var d = dialogApi.getData(), embedId = parseEmbedId( d.embedId );
                    if ( !embedId )
                        return;
                    buildEmbedHtml( embedId, d ).then( function ( html ) {
                        state.preview = html.replace( /\sclass="ezoeItemNonEditable[^"]*"/, '' );
                        var el = document.querySelector( '.tox-dialog .ezoe-embed-preview' );
                        if ( el )
                            el.innerHTML = state.preview;
                    } ).catch( function () {} );
                }, 150 );
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
                    redial( dialogApi );
                } ).catch( function ( e ) {
                    fail( dialogApi, e );
                } );
            };

            // Inserts or replaces the embed; like insertHTMLCleanly() / onTagGenerated() of the old dialog
            // empty paragraphs next to a new block embed are removed and a paragraph follows it at the end
            var insertEmbed = function ( dialogApi, embedId, d ) {
                dialogApi.block( t( 'Loading preview…' ) );
                buildEmbedHtml( embedId, d ).then( function ( html ) {
                    // the new element is found by a temporary ezoeItem* class (removed by the parser anyway)
                    var marker = 'ezoeItemNew' + Date.now();
                    dialogApi.unblock();
                    dialogApi.close();
                    editor.undoManager.transact( function () {
                        if ( target )
                            editor.selection.select( target );
                        editor.insertContent( /\sclass="/.test( html.split( '>' )[0] )
                            ? html.replace( /\sclass="/, ' class="' + marker + ' ' )
                            : html.replace( /^<(\w+)/, '<$1 class="' + marker + '"' ) );
                        var el = editor.getBody().querySelector( '.' + marker );
                        if ( !el )
                            return;
                        editor.dom.removeClass( el, marker );
                        if ( !el.getAttribute( 'class' ) )
                            el.removeAttribute( 'class' );
                        if ( el.nodeName !== 'DIV' )
                            return;
                        [ el.previousSibling, el.nextSibling ].forEach( function ( sibling ) {
                            if ( sibling && sibling.nodeName === 'P' && /^(\s|&nbsp;|<br[^>]*>)*$/i.test( sibling.innerHTML ) )
                                sibling.parentNode.removeChild( sibling );
                        } );
                        if ( el.parentNode === editor.getBody() && !el.nextSibling )
                            editor.dom.insertAfter( editor.dom.create( 'p', {}, '<br>' ), el );
                    } );
                    editor.nodeChanged();
                } ).catch( function ( e ) {
                    fail( dialogApi, e );
                } );
            };

            // insertAfter: embed the new object directly (OK pressed), otherwise continue on Properties
            var runUpload = function ( dialogApi, insertAfter ) {
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
                    var embedId = 'eZObject_' + result.objectId;
                    if ( insertAfter )
                        return insertEmbed( dialogApi, embedId, Object.assign( dialogApi.getData(), { embedId: embedId, customAttributes: '' } ) );
                    // like eZOEPopupUtils.selectByEmbedId(): select the new object and continue on Properties
                    state.tab = 'properties';
                    redial( dialogApi, Object.assign( attributeDefaults( data.inline ), {
                        embedId: embedId,
                        uploadName: '', uploadFile: [], uploadAlt: '', uploadDescription: ''
                    } ) );
                    editor.notificationManager.open( {
                        text: t( 'Uploaded, press OK to embed it:' ) + ' ' + result.name, type: 'success', timeout: 5000
                    } );
                } ).catch( function ( e ) {
                    fail( dialogApi, e );
                } );
            };

            unbind = D.bindList( function ( action, value ) {
                if ( action === 'select' )
                {
                    state.object = null;
                    state.objectFor = null;
                    state.preview = '';
                    state.tab = 'properties';
                    redial( api, Object.assign( attributeDefaults( data.inline ), { embedId: value, query: '' } ) );
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

            // a new embed starts with the default values of the custom attributes
            if ( !target )
                Object.assign( data, attributeDefaults( false ) );

            api = editor.windowManager.open( spec() );
            api.showTab( state.tab );
            loadObjectInfo( api );

            if ( !locations )
            {
                locationsPromise = locationsPromise || D.uploadLocations( settings() );
                locationsPromise.then( function ( list ) {
                    locations = list;
                    // the dialog is only rebuilt for the locations while the upload tab is shown and nothing is
                    // chosen in the dropzone (files can not be put back into it); a rebuild on another tab
                    // would take the focus away, e.g. from the class filter of the search
                    var d = api.getData();
                    if ( state.choosing && state.tab === 'upload' && ( !d.uploadFile || !d.uploadFile.length ) )
                        redial( api );
                    else
                        state.locationsPending = true;
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

        // the command takes an optional element, e.g. from a click on the status bar path
        editor.addCommand( 'ezEmbed', function ( ui, element ) {
            openDialog( getEmbed( element && element.nodeType === 1 ? element : editor.selection.getNode() ) );
        } );

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

        // Double click opens the dialog. Firefox fires no dblclick on contenteditable=false elements
        // (TinyMCE prevents the mousedown to select them), so two clicks on the same embed count as well
        var lastClick = { el: null, time: 0 };
        var openOnDoubleClick = function ( e ) {
            var el = getEmbed( e.target ), now = Date.now(), isDouble;
            if ( !el )
            {
                lastClick.el = null;
                return;
            }
            isDouble = e.type === 'dblclick' || e.detail >= 2 || ( lastClick.el === el && now - lastClick.time < 500 );
            lastClick = { el: el, time: now };
            if ( isDouble && !document.querySelector( '.tox-dialog' ) )
            {
                e.preventDefault();
                lastClick.el = null;
                openDialog( el );
            }
        };
        editor.on( 'click', openOnDoubleClick );
        editor.on( 'dblclick', openOnDoubleClick );

        // Like the TinyMCE 3 ez theme: only the textarea content is cleaned up, not the editor.
        // The preview markup inside embed tags is replaced by 'ezembed' (see issue 18264) and
        // ezoeAlign* helper classes are removed (see EZP-22487).
        // GetContent with e.save: changes in SaveContent do not reach the textarea in TinyMCE 8
        editor.on( 'GetContent', function ( e ) {
            if ( !e.save || typeof e.content !== 'string' || !e.content )
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

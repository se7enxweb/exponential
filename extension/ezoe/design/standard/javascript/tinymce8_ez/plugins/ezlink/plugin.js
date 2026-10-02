/**
 * eZ Online Editor - TinyMCE 8 prototype: link plugin with content browser
 *
 * Replaces the TinyMCE link dialog (button, menu item, Ctrl+K). Links use the hrefs that
 * eZOEXMLInput writes and eZOEInputParser::publishHandlerLink() reads:
 *
 *   eznode://<node id>[#anchor]    ezobject://<object id>[#anchor]
 *   http(s)://...  mailto:...      #anchor
 *
 * plus title, target, class, view and id. Browse and Search list the content like the
 * TinyMCE 3 ezoe dialogs (ezoe_dialog.js), choosing an entry takes it over into the Link tab.
 *
 * Licensed under the GNU General Public License v2.0, like the rest of ezoe.
 */
(function () {
    'use strict';

    var INTERNAL_HREF_REGEX = /^(eznode|ezobject):\/\/(\d+)\/?(#.*)?$/i;
    var D = window.eZOe8Dialog;

    tinymce.addI18n( 'de', {
        'Insert link': 'Link einfügen',
        'Edit link': 'Link bearbeiten',
        'Link': 'Link',
        'Browse': 'Durchsuchen',
        'Search': 'Suchen',
        'URL': 'URL',
        'Text to display': 'Linktext',
        'Title': 'Titel',
        'Open link in': 'Öffnen in',
        'Current window': 'Gleiches Fenster',
        'New window': 'Neues Fenster',
        'Parent frame': 'Übergeordneter Frame',
        'Top frame': 'Oberster Frame',
        'Class': 'Klasse',
        'View': 'Ansicht',
        'ID': 'ID',
        'Anchor in this text': 'Anker in diesem Text',
        'None': 'Keine',
        'Default': 'Standard',
        'Link target': 'Linkziel',
        'Link to the object instead of the node (ezobject://)': 'Objekt statt Knoten verlinken (ezobject://)',
        'Loading…': 'Wird geladen …',
        'Searching…': 'Suche läuft …',
        'Name of the content': 'Name des Inhalts',
        'Please enter a URL.': 'Bitte eine URL eingeben.',
        'Node': 'Knoten',
        'Object': 'Objekt',
        'not found or no access': 'nicht gefunden oder kein Zugriff'
    } );

    tinymce.PluginManager.add( 'ezlink', function ( editor ) {

        // json_encode() of an empty hash in the template is [], accept both
        editor.options.register( 'ez_link_classes', {
            processor: function ( value ) {
                return { valid: !!value && typeof value === 'object', value: Array.isArray( value ) ? {} : value };
            },
            default: {}
        } );
        editor.options.register( 'ez_link_view_modes', { processor: 'array', default: [] } );

        var t = function ( text ) {
            return editor.translate( text );
        };

        var settings = function () {
            return editor.options.get( 'ez_settings' ) || {};
        };

        // Name of the node / object an internal href points to, null if not internal
        var describeHref = function ( href ) {
            var m = String( href || '' ).match( INTERNAL_HREF_REGEX );
            if ( !m )
                return null;
            var isNode = m[1].toLowerCase() === 'eznode';
            return D.loadObject( settings(), ( isNode ? 'eZNode_' : 'eZObject_' ) + m[2] ).then( function ( object ) {
                var kind = isNode ? t( 'Node' ) : t( 'Object' );
                return object
                    ? kind + ' ' + m[2] + ': ' + D.decodeHtml( object.name ) + ' (' + D.decodeHtml( object.class_name ) + ')'
                    : kind + ' ' + m[2] + ' ' + t( 'not found or no access' );
            } );
        };

        var getLink = function ( node ) {
            return editor.dom.getParent( node, 'a[href]', editor.getBody() );
        };

        var anchorItems = function () {
            var items = [ { text: t( 'None' ), value: '' } ];
            editor.dom.select( 'a[name]:not([href]), a[id]:not([href])' ).forEach( function ( a ) {
                var name = a.getAttribute( 'name' ) || a.id;
                if ( name )
                    items.push( { text: name, value: '#' + name } );
            } );
            return items;
        };

        var classItems = function () {
            var classes = editor.options.get( 'ez_link_classes' ), items = [ { text: t( 'None' ), value: '' } ];
            Object.keys( classes ).forEach( function ( key ) {
                items.push( { text: classes[key], value: key } );
            } );
            return items;
        };

        var viewItems = function () {
            return [ { text: t( 'Default' ), value: '' } ].concat( editor.options.get( 'ez_link_view_modes' ).map( function ( v ) {
                return { text: v, value: v };
            } ) );
        };

        // element: optional, e.g. from a click on the status bar path
        var openDialog = function ( ui, element ) {
            var link = getLink( element && element.nodeType === 1 ? element : editor.selection.getNode() ),
                dom = editor.dom,
                selectionText = editor.selection.isCollapsed() ? '' : editor.selection.getContent( { format: 'text' } ),
                state = { tab: 'link', browse: null, search: null, searchText: '', targetInfo: '' },
                unbind, api;

            var data = link ? {
                href: dom.getAttrib( link, 'data-mce-href' ) || dom.getAttrib( link, 'href' ),
                text: '',
                title: dom.getAttrib( link, 'title' ),
                target: dom.getAttrib( link, 'target' ),
                cssClass: dom.getAttrib( link, 'class' ).replace( /\b(mceItem\w+|ezoe\w+)\b/g, '' ).trim(),
                view: dom.getAttrib( link, 'view' ),
                htmlId: dom.getAttrib( link, 'id' ),
                anchor: '', query: '', browseAsObject: false, searchAsObject: false
            } : {
                href: '', text: selectionText, title: '', target: '', cssClass: '', view: '', htmlId: '',
                anchor: '', query: '', browseAsObject: false, searchAsObject: false
            };

            // list rows carry node and object id, the link type is chosen with the asObject checkbox
            var rowValue = function ( item ) {
                return item.node_id + ':' + item.contentobject_id;
            };

            var selectedRow = function () {
                var m = String( data.href || '' ).match( INTERNAL_HREF_REGEX ), list = [ state.browse, state.search ];
                if ( !m )
                    return '';
                for ( var i = 0; i < list.length; i++ )
                {
                    var hit = list[i] && list[i].items.filter( function ( item ) {
                        return m[1].toLowerCase() === 'eznode' ? String( item.node_id ) === m[2] : String( item.contentobject_id ) === m[2];
                    } )[0];
                    if ( hit )
                        return rowValue( hit );
                }
                return '';
            };

            var listHtml = function ( list, browse ) {
                if ( !list )
                    return '<p class="ezoe-list-empty">' + D.escapeHtml( t( 'Loading…' ) ) + '</p>';
                return D.renderList( list, { t: t, value: rowValue, previewAlias: settings().browse_image_alias, rootUrl: settings().root_url, selected: selectedRow(), browse: browse } );
            };

            var spec = function () {
                var linkItems = [
                    { type: 'input', name: 'href', label: t( 'URL' ), placeholder: 'https://…, eznode://12, ezobject://34, mailto:…, #anker' },
                    { type: 'htmlpanel', html: state.targetInfo ? '<p class="ezoe-selected">' + D.escapeHtml( t( 'Link target' ) ) + ': ' + D.escapeHtml( state.targetInfo ) + '</p>' : '' }
                ];
                // the link text is only asked for when nothing is selected, a selection becomes the link
                if ( !link && !selectionText )
                    linkItems.push( { type: 'input', name: 'text', label: t( 'Text to display' ) } );
                linkItems.push(
                    { type: 'listbox', name: 'anchor', label: t( 'Anchor in this text' ), items: anchorItems() },
                    { type: 'input', name: 'title', label: t( 'Title' ) },
                    {
                        type: 'listbox', name: 'target', label: t( 'Open link in' ), items: [
                            { text: t( 'Current window' ), value: '' },
                            { text: t( 'New window' ), value: '_blank' },
                            { text: t( 'Parent frame' ), value: '_parent' },
                            { text: t( 'Top frame' ), value: '_top' }
                        ]
                    },
                    { type: 'listbox', name: 'cssClass', label: t( 'Class' ), items: classItems() },
                    { type: 'listbox', name: 'view', label: t( 'View' ), items: viewItems() },
                    { type: 'input', name: 'htmlId', label: t( 'ID' ) }
                );

                return {
                    title: link ? t( 'Edit link' ) : t( 'Insert link' ),
                    size: 'medium',
                    body: {
                        type: 'tabpanel',
                        tabs: [
                            { name: 'link', title: t( 'Link' ), items: linkItems },
                            {
                                name: 'browse',
                                title: t( 'Browse' ),
                                items: [
                                    { type: 'checkbox', name: 'browseAsObject', label: t( 'Link to the object instead of the node (ezobject://)' ) },
                                    { type: 'htmlpanel', html: listHtml( state.browse, true ) }
                                ]
                            },
                            {
                                name: 'search',
                                title: t( 'Search' ),
                                items: [
                                    {
                                        type: 'bar', items: [
                                            { type: 'input', name: 'query', label: t( 'Search' ), placeholder: t( 'Name of the content' ) },
                                            { type: 'button', name: 'searchRun', text: t( 'Search' ), buttonType: 'secondary' }
                                        ]
                                    },
                                    { type: 'htmlpanel', html: D.renderClassFilter( settings().search_classes, state.searchClasses, t ) },
                                    { type: 'checkbox', name: 'searchAsObject', label: t( 'Link to the object instead of the node (ezobject://)' ) },
                                    { type: 'htmlpanel', html: state.search ? listHtml( state.search ) : '' }
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
                            loadBrowse( dialogApi, startNode(), 0 );
                    },
                    onChange: function ( dialogApi, details ) {
                        if ( details.name === 'anchor' && dialogApi.getData().anchor )
                        {
                            var href = dialogApi.getData().href.replace( /#.*$/, '' );
                            dialogApi.setData( { href: href + dialogApi.getData().anchor } );
                        }
                    },
                    onAction: function ( dialogApi, details ) {
                        if ( details.name === 'searchRun' )
                            runSearch( dialogApi, 0 );
                    },
                    onSubmit: function ( dialogApi ) {
                        var d = dialogApi.getData();
                        // Enter in the search field submits the dialog, treat it as search
                        if ( state.tab === 'search' && String( d.query ).trim() )
                            return runSearch( dialogApi, 0 );
                        if ( !String( d.href ).trim() )
                        {
                            editor.notificationManager.open( { text: t( 'Please enter a URL.' ), type: 'warning', timeout: 4000 } );
                            return;
                        }
                        dialogApi.close();
                        applyLink( d );
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

            var startNode = function () {
                var m = String( data.href || '' ).match( /^eznode:\/\/(\d+)/i );
                return m ? m[1] : ( settings().root_node || 2 );
            };

            var loadBrowse = function ( dialogApi, nodeId, offset ) {
                dialogApi.block( t( 'Loading…' ) );
                D.browse( settings(), nodeId, offset ).then( function ( list ) {
                    // a linked node without children: show its parent so the node itself is listed
                    if ( !list.total && list.node && list.node.parent_node_id && list.node.node_id !== 1 && !state.browse )
                        return loadBrowse( dialogApi, list.node.parent_node_id, 0 );
                    state.browse = list;
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
                    redial( dialogApi, { query: '' } );
                } ).catch( function ( e ) {
                    fail( dialogApi, e );
                } );
            };

            var findItem = function ( value ) {
                return [ state.browse, state.search ].reduce( function ( found, list ) {
                    return found || ( list && list.items.filter( function ( i ) {
                        return rowValue( i ) === value;
                    } )[0] );
                }, null );
            };

            // Takes over a chosen entry into the link tab, keeping an anchor already set
            var choose = function ( dialogApi, value ) {
                var d = dialogApi.getData(), ids = value.split( ':' ), item = findItem( value ),
                    href = ( state.tab === 'search' ? d.searchAsObject : d.browseAsObject ) ? 'ezobject://' + ids[1] : 'eznode://' + ids[0],
                    anchor = ( String( d.href ).match( /#.*$/ ) || [ '' ] )[0],
                    changes = { href: href + anchor };
                if ( !link && !selectionText && !String( d.text || '' ).trim() && item )
                    changes.text = D.decodeHtml( item.name );
                state.tab = 'link';
                state.targetInfo = '';
                redial( dialogApi, changes );
                showTargetInfo( dialogApi, href );
            };

            var showTargetInfo = function ( dialogApi, href ) {
                var description = describeHref( href );
                if ( !description )
                    return;
                description.then( function ( info ) {
                    state.targetInfo = info;
                    if ( state.tab === 'link' )
                        redial( dialogApi );
                } ).catch( function () {} );
            };

            var applyLink = function ( d ) {
                var attrs = {
                    href: d.href.trim(),
                    'data-mce-href': d.href.trim(),
                    title: d.title || null,
                    target: d.target || null,
                    'class': d.cssClass || null,
                    view: d.view || null,
                    id: d.htmlId || null
                };

                editor.undoManager.transact( function () {
                    if ( link )
                    {
                        dom.setAttribs( link, attrs );
                    }
                    else if ( editor.selection.isCollapsed() )
                    {
                        var html = '<a';
                        Object.keys( attrs ).forEach( function ( key ) {
                            if ( attrs[key] !== null && key !== 'data-mce-href' )
                                html += ' ' + key + '="' + D.escapeHtml( attrs[key] ) + '"';
                        } );
                        editor.insertContent( html + '>' + D.escapeHtml( d.text || d.href ) + '</a>' );
                    }
                    else
                    {
                        // mark the new links with a unique href to set all attributes afterwards
                        var marker = 'ezoe-new-link-' + Date.now();
                        editor.execCommand( 'mceInsertLink', false, { href: marker } );
                        dom.select( 'a[href="' + marker + '"]' ).forEach( function ( a ) {
                            dom.setAttribs( a, attrs );
                        } );
                    }
                } );
                editor.nodeChanged();
            };

            unbind = D.bindList( function ( action, value ) {
                if ( action === 'select' )
                    choose( api, value );
                else if ( action === 'open' )
                    loadBrowse( api, value, 0 );
                else if ( action === 'page' )
                {
                    if ( state.tab === 'search' )
                        runSearch( api, parseInt( value, 10 ) );
                    else
                        loadBrowse( api, state.browse.node.node_id, parseInt( value, 10 ) );
                }
            } );

            api = editor.windowManager.open( spec() );
            if ( link )
                showTargetInfo( api, data.href );
        };

        editor.addCommand( 'ezLink', openDialog );

        editor.ui.registry.addToggleButton( 'ezlink', {
            icon: 'link',
            tooltip: t( 'Insert link' ),
            onAction: openDialog,
            onSetup: function ( buttonApi ) {
                var handler = function ( e ) {
                    buttonApi.setActive( !!getLink( e.element ) );
                };
                editor.on( 'NodeChange', handler );
                return function () {
                    editor.off( 'NodeChange', handler );
                };
            }
        } );

        editor.ui.registry.addMenuItem( 'ezlink', {
            icon: 'link',
            text: t( 'Link' ) + '…',
            onAction: openDialog
        } );

        editor.ui.registry.addContextToolbar( 'ezlink', {
            predicate: function ( node ) {
                return !!getLink( node ) && !editor.dom.getParent( node, '[id^=eZObject_],[id^=eZNode_]', editor.getBody() );
            },
            items: 'ezlink unlink',
            position: 'node',
            scope: 'node'
        } );

        // Ctrl+K opens this dialog instead of the one of the TinyMCE link plugin
        editor.on( 'init', function () {
            editor.addShortcut( 'meta+k', '', 'ezLink' );
        } );
    } );
}());

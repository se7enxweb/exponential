/**
 * eZ Online Editor - TinyMCE 8 prototype: link plugin with content browser
 *
 * Replaces the TinyMCE link dialog (button, menu item, Ctrl+K). Links use the hrefs that
 * eZOEXMLInput writes and eZOEInputParser::publishHandlerLink() reads:
 *
 *   eznode://<node id>[#anchor]    ezobject://<object id>[#anchor]
 *   http(s)://...  mailto:...      #anchor
 *
 * plus title, target, class, view and id. Server endpoints (existing):
 *   ezjscore/call ezoe::browse::<node>::<offset>::<limit>   content tree
 *   ezjscore/call ezjsc::search                             search
 *   ezoe/load/<eZNode_x|eZObject_x>                         name of a linked node / object
 *
 * Licensed under the GNU General Public License v2.0, like the rest of ezoe.
 */
(function () {
    'use strict';

    var BROWSE_LIMIT = 50;
    var INTERNAL_HREF_REGEX = /^(eznode|ezobject):\/\/(\d+)\/?(#.*)?$/i;

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
        'Content': 'Inhalt',
        'Open': 'Öffnen',
        'Link to node': 'Als Knoten verlinken',
        'Link to object': 'Als Objekt verlinken',
        'Up': 'Eine Ebene höher',
        'Loading…': 'Wird geladen …',
        'Searching…': 'Suche läuft …',
        'No results': 'Keine Treffer',
        'Name of the content': 'Name des Inhalts',
        'Search result': 'Suchergebnis',
        'Please enter a URL.': 'Bitte eine URL eingeben.',
        'Please choose an entry first.': 'Bitte zuerst einen Eintrag auswählen.',
        'Node': 'Knoten',
        'Object': 'Objekt',
        'not found or no access': 'nicht gefunden oder kein Zugriff',
        'more entries, refine with the search': 'weitere Einträge, bitte über die Suche eingrenzen'
    } );

    tinymce.PluginManager.add( 'ezlink', function ( editor ) {

        editor.options.register( 'ez_link_classes', { processor: 'object', default: {} } );
        editor.options.register( 'ez_link_view_modes', { processor: 'array', default: [] } );
        editor.options.register( 'ez_link_root_node', { processor: 'number', default: 2 } );

        var t = function ( text ) {
            return editor.translate( text );
        };

        var settings = function () {
            return editor.options.get( 'ez_settings' ) || {};
        };

        // ezurl() strips the trailing slash
        var serverUrl = function ( name ) {
            return String( settings()[name] || '' ).replace( /\/?$/, '/' );
        };

        var escapeHtml = function ( value ) {
            return String( value ).replace( /&/g, '&amp;' ).replace( /"/g, '&quot;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' );
        };

        var request = function ( url, options ) {
            // ezjscore picks the response format from the Accept header
            options = Object.assign( {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            }, options || {} );
            return fetch( url, options ).then( function ( response ) {
                if ( !response.ok )
                    throw new Error( 'HTTP ' + response.status + ' – ' + url );
                return response.json();
            } );
        };

        var ezjscoreCall = function ( functionArguments, params ) {
            var body = new URLSearchParams( params || {} );
            body.append( 'ezjscServer_function_arguments', functionArguments );
            body.append( 'ezxform_token', settings().form_token || '' );
            return request( serverUrl( 'ezjscore_url' ) + 'call', { method: 'POST', body: body } ).then( function ( data ) {
                if ( data.error_text )
                    throw new Error( data.error_text );
                return data.content;
            } );
        };

        var browse = function ( nodeId ) {
            return ezjscoreCall( 'ezoe::browse::' + nodeId + '::0::' + BROWSE_LIMIT );
        };

        var search = function ( text ) {
            return ezjscoreCall( 'ezjsc::search', { SearchStr: text, SearchLimit: '20' } ).then( function ( content ) {
                return ( content && content.SearchResult ) || [];
            } );
        };

        // Name of the node / object an internal href points to, null if not internal
        var describeHref = function ( href ) {
            var m = String( href || '' ).match( INTERNAL_HREF_REGEX );
            if ( !m )
                return null;
            var isNode = m[1].toLowerCase() === 'eznode';
            return request( serverUrl( 'extension_url' ) + 'load/' + ( isNode ? 'eZNode_' : 'eZObject_' ) + m[2] )
                .then( function ( object ) {
                    var kind = isNode ? t( 'Node' ) : t( 'Object' );
                    return object
                        ? kind + ' ' + m[2] + ': ' + object.name + ' (' + object.class_name + ')'
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

        var openDialog = function () {
            var link = getLink( editor.selection.getNode() ),
                dom = editor.dom,
                selectionText = editor.selection.isCollapsed() ? '' : editor.selection.getContent( { format: 'text' } ),
                state = {
                    browseNode: null,
                    browseItems: [],
                    browsePath: '',
                    searchItems: [ { text: t( 'Search result' ), value: '' } ],
                    targetInfo: '',
                    activeTab: 'link'
                };

            var data = link ? {
                href: dom.getAttrib( link, 'data-mce-href' ) || dom.getAttrib( link, 'href' ),
                text: '',
                title: dom.getAttrib( link, 'title' ),
                target: dom.getAttrib( link, 'target' ),
                cssClass: dom.getAttrib( link, 'class' ).replace( /\b(mceItem\w+|ezoe\w+)\b/g, '' ).trim(),
                view: dom.getAttrib( link, 'view' ),
                htmlId: dom.getAttrib( link, 'id' ),
                anchor: '',
                browseItem: '',
                query: '',
                searchItem: ''
            } : {
                href: '', text: selectionText, title: '', target: '', cssClass: '', view: '', htmlId: '',
                anchor: '', browseItem: '', query: '', searchItem: ''
            };

            var browseListItems = function () {
                if ( !state.browseNode )
                    return [ { text: t( 'Loading…' ), value: '' } ];
                var items = [];
                if ( state.browseNode.parent_node_id && state.browseNode.node_id !== 1 )
                    items.push( { text: '⬑ ' + t( 'Up' ), value: 'up' } );
                state.browseItems.forEach( function ( n ) {
                    items.push( {
                        text: ( n.children_count ? '▸ ' : '   ' ) + n.name + ' (' + n.class_name + ')',
                        value: String( n.node_id )
                    } );
                } );
                if ( state.browseTotal > state.browseItems.length )
                    items.push( { text: '… ' + ( state.browseTotal - state.browseItems.length ) + ' ' + t( 'more entries, refine with the search' ), value: '' } );
                return items.length ? items : [ { text: t( 'No results' ), value: '' } ];
            };

            var spec = function () {
                var linkTab = {
                    name: 'link',
                    title: t( 'Link' ),
                    items: [
                        { type: 'input', name: 'href', label: t( 'URL' ), placeholder: 'https://…, eznode://12, ezobject://34, mailto:…, #anker' },
                        { type: 'htmlpanel', html: state.targetInfo ? '<p style="margin:0 0 6px;color:#555">' + t( 'Link target' ) + ': ' + escapeHtml( state.targetInfo ) + '</p>' : '' }
                    ]
                };
                // the link text is only asked for when nothing is selected, a selection becomes the link
                if ( !link && !selectionText )
                    linkTab.items.push( { type: 'input', name: 'text', label: t( 'Text to display' ) } );
                linkTab.items.push(
                    { type: 'listbox', name: 'anchor', label: t( 'Anchor in this text' ), items: anchorItems() },
                    { type: 'input', name: 'title', label: t( 'Title' ) },
                    {
                        type: 'grid', columns: 2, items: [
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
                        ]
                    }
                );

                return {
                    title: link ? t( 'Edit link' ) : t( 'Insert link' ),
                    size: 'medium',
                    body: {
                        type: 'tabpanel',
                        tabs: [
                            linkTab,
                            {
                                name: 'browse',
                                title: t( 'Browse' ),
                                items: [
                                    { type: 'htmlpanel', html: '<p style="margin:0 0 6px"><strong>' + escapeHtml( state.browsePath || '' ) + '</strong></p>' },
                                    { type: 'listbox', name: 'browseItem', label: t( 'Content' ), items: browseListItems() },
                                    {
                                        type: 'bar', items: [
                                            { type: 'button', name: 'browseOpen', text: t( 'Open' ), buttonType: 'secondary' },
                                            { type: 'button', name: 'browseNode', text: t( 'Link to node' ), buttonType: 'secondary' },
                                            { type: 'button', name: 'browseObject', text: t( 'Link to object' ), buttonType: 'secondary' }
                                        ]
                                    }
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
                                    { type: 'listbox', name: 'searchItem', label: t( 'Search result' ), items: state.searchItems },
                                    {
                                        type: 'bar', items: [
                                            { type: 'button', name: 'searchNode', text: t( 'Link to node' ), buttonType: 'secondary' },
                                            { type: 'button', name: 'searchObject', text: t( 'Link to object' ), buttonType: 'secondary' }
                                        ]
                                    }
                                ]
                            }
                        ]
                    },
                    initialData: data,
                    buttons: [
                        { type: 'cancel', text: t( 'Cancel' ) },
                        { type: 'submit', text: t( 'Save' ), primary: true }
                    ],
                    onTabChange: function ( api, details ) {
                        state.activeTab = details.newTabName;
                        if ( details.newTabName === 'browse' && !state.browseNode )
                            loadBrowse( api, startNode() );
                    },
                    onChange: function ( api, details ) {
                        if ( details.name === 'anchor' && api.getData().anchor )
                        {
                            var href = api.getData().href.replace( /#.*$/, '' );
                            api.setData( { href: href + api.getData().anchor } );
                        }
                    },
                    onAction: function ( api, details ) {
                        var d = api.getData();
                        switch ( details.name )
                        {
                            case 'browseOpen':
                                if ( d.browseItem === 'up' )
                                    loadBrowse( api, state.browseNode.parent_node_id );
                                else if ( d.browseItem )
                                    loadBrowse( api, d.browseItem );
                                else
                                    notify( t( 'Please choose an entry first.' ) );
                                break;
                            case 'browseNode':
                            case 'browseObject':
                                var node = state.browseItems.filter( function ( n ) {
                                    return String( n.node_id ) === d.browseItem;
                                } )[0];
                                if ( !node )
                                    return notify( t( 'Please choose an entry first.' ) );
                                setTarget( api, details.name === 'browseNode' ? 'eznode://' + node.node_id : 'ezobject://' + node.contentobject_id, node.name );
                                break;
                            case 'searchRun':
                                runSearch( api );
                                break;
                            case 'searchNode':
                            case 'searchObject':
                                if ( !d.searchItem )
                                    return notify( t( 'Please choose an entry first.' ) );
                                var ids = d.searchItem.split( ':' ), hit = state.searchHits[ d.searchItem ];
                                setTarget( api, details.name === 'searchNode' ? 'eznode://' + ids[0] : 'ezobject://' + ids[1], hit ? hit.name : '' );
                                break;
                        }
                    },
                    onSubmit: function ( api ) {
                        var d = api.getData();
                        // Enter in the search field submits the dialog, treat it as search
                        if ( state.activeTab === 'search' && String( d.query ).trim() && !d.searchItem )
                            return runSearch( api );
                        if ( !String( d.href ).trim() )
                            return notify( t( 'Please enter a URL.' ) );
                        api.close();
                        applyLink( d );
                    }
                };
            };

            var notify = function ( text ) {
                editor.notificationManager.open( { text: text, type: 'warning', timeout: 4000 } );
            };

            var fail = function ( api, e ) {
                api.unblock();
                editor.notificationManager.open( { text: e.message, type: 'error' } );
            };

            // Rebuilds the dialog with the current data and state, keeping the active tab
            var redial = function ( api, changes ) {
                // redial() shows the first tab and fires onTabChange, so remember the tab before
                var tab = state.activeTab;
                data = Object.assign( api.getData(), changes || {} );
                api.redial( spec() );
                state.activeTab = tab;
                api.showTab( tab );
            };

            var startNode = function () {
                var m = String( data.href || '' ).match( /^eznode:\/\/(\d+)/i );
                return m ? m[1] : editor.options.get( 'ez_link_root_node' );
            };

            var loadBrowse = function ( api, nodeId ) {
                api.block( t( 'Loading…' ) );
                browse( nodeId ).then( function ( content ) {
                    var node = content.node || {};
                    // a node without children: show its parent so the node itself can be selected
                    if ( !content.total_count && node.parent_node_id && String( nodeId ) === String( startNode() ) && node.node_id !== 1 )
                        return loadBrowse( api, node.parent_node_id );
                    state.browseNode = node;
                    state.browseItems = content.list || [];
                    state.browseTotal = content.total_count || 0;
                    state.browsePath = ( node.path || [] ).concat( [ node ] ).map( function ( n ) {
                        return n.name;
                    } ).join( ' / ' );
                    api.unblock();
                    redial( api, { browseItem: '' } );
                } ).catch( function ( e ) {
                    fail( api, e );
                } );
            };

            var runSearch = function ( api ) {
                var text = String( api.getData().query ).trim();
                if ( !text )
                    return;
                api.block( t( 'Searching…' ) );
                search( text ).then( function ( list ) {
                    state.searchHits = {};
                    state.searchItems = list.length ? list.map( function ( n ) {
                        var key = n.node_id + ':' + n.contentobject_id;
                        state.searchHits[key] = n;
                        return { text: n.name + ' (' + n.class_name + ')', value: key };
                    } ) : [ { text: t( 'No results' ), value: '' } ];
                    api.unblock();
                    redial( api, { searchItem: state.searchItems[0].value } );
                } ).catch( function ( e ) {
                    fail( api, e );
                } );
            };

            // Takes over an internal target into the link tab, keeping an anchor already set
            var setTarget = function ( api, href, name ) {
                var anchor = ( api.getData().href.match( /#.*$/ ) || [ '' ] )[0];
                var changes = { href: href + anchor };
                if ( !link && !selectionText && !String( api.getData().text || '' ).trim() && name )
                    changes.text = name;
                state.activeTab = 'link';
                state.targetInfo = '';
                redial( api, changes );
                showTargetInfo( api, href );
            };

            var showTargetInfo = function ( api, href ) {
                var description = describeHref( href );
                if ( !description )
                    return;
                description.then( function ( info ) {
                    state.targetInfo = info;
                    if ( state.activeTab === 'link' )
                        redial( api );
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
                                html += ' ' + key + '="' + escapeHtml( attrs[key] ) + '"';
                        } );
                        editor.insertContent( html + '>' + escapeHtml( d.text || d.href ) + '</a>' );
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

            var api = editor.windowManager.open( spec() );
            if ( link )
                showTargetInfo( api, data.href );
        };

        editor.addCommand( 'ezLink', openDialog );

        editor.ui.registry.addToggleButton( 'ezlink', {
            icon: 'link',
            tooltip: t( 'Insert link' ),
            onAction: openDialog,
            onSetup: function ( api ) {
                var handler = function ( e ) {
                    api.setActive( !!getLink( e.element ) );
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

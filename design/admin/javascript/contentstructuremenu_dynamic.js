function ContentStructureMenu( params, i18n )
{
    this.parameterName = "contentStructureMenu";
    // [TreeMenu] AutoopenCurrentNode: open the path down to the current node.
    // The path is copied: opening it removes entries one by one, and
    // params.path must keep the current node for the highlighting.
    this.autoOpen = !!params.autoOpen;
    this.autoOpenPath = params.path ? params.path.slice( 0 ) : [];

    // [TreeMenu] MaxDepth: levels shown below the root node, 0 = unlimited.
    // levels[nodeID] is the level of a node already in the menu, the root
    // node being level 0.
    this.maxDepth = parseInt( params.maxDepth, 10 ) || 0;
    this.levels = {};

    // hashes in ez template doesn't handle numeric keys correctly
    // an underscore has been prepended in keys and is removed here
    var arrayTemp = [];
    for ( var j in params.classes )
    {
        arrayTemp[j.replace(/^_/, '')] = params.classes[j];
    }

    params.classes = arrayTemp;

    // function taken from the modernizr library
    this.hasStorage = (function() {
        var mod = '_ez_ls_check';

        try
        {
            localStorage.setItem( mod, mod );
            localStorage.removeItem( mod );
            return true;
        }
        catch ( e )
        {
            return false;
        }
    }());

    if ( this.hasStorage )
    {
        this.isOpen = function( nodeID )
        {
            return localStorage.getItem( this.parameterName + "_" + nodeID ) == '1';
        };

        this.setOpen = function( nodeID )
        {
            localStorage.setItem( this.parameterName + "_" + nodeID, '1' );
        };

        this.setClosed = function( nodeID )
        {
            localStorage.removeItem( this.parameterName + "_" + nodeID );
        };
    }
    else
    {
        this.cookieValidity = 3650; // days
        this.cookie         = params.useCookie ? _getCookie( this.parameterName ) : '';
        this.open           = ( this.cookie )? this.cookie.split( '/' ): [];

        this.updateCookie = function()
        {
            if ( !params.useCookie )
                return;
            this.cookie = this.open.join('/');
            expireDate  = new Date();
            expireDate.setTime( expireDate.getTime() + this.cookieValidity * 86400000 );
            _setCookie( this.parameterName, this.cookie, expireDate );
        };

        this.isOpen = function( nodeID )
        {
            return jQuery.inArray( '' + nodeID, this.open ) !== -1;
        };

        this.setOpen = function( nodeID )
        {
            if ( this.isOpen( nodeID ) )
            {
                return;
            }

            this.open[this.open.length] = nodeID;
            this.updateCookie();
        };

        this.setClosed = function( nodeID )
        {
            var openIndex = jQuery.inArray( '' + nodeID, this.open );
            if ( openIndex !== -1 )
            {
                this.open.splice( openIndex, 1 );
                this.updateCookie();
            }
        };
    }

    this.generateEntry = function( item, lastli, rootNode, level )
    {
        var liclass = '';
        level = level || 0;
        this.levels[item.node_id] = level;
        var canOpen = item.has_children && ( this.maxDepth === 0 || level < this.maxDepth );
        if ( lastli )
        {
            liclass += ' lastli';
        }
        if ( params.path && ( params.path[params.path.length-1] == item.node_id || ( !item.has_children && jQuery.inArray( item.node_id, params.path ) !== -1 ) ) )
        {
            liclass += ' currentnode';
        }
        var html = '<li id="n'+item.node_id+'"'
            + ( ( liclass )? ' class="' + liclass + '"':
                             '' )
            + '>';
        if ( canOpen && !rootNode )
        {
            // A button for keyboards and screen readers: its name says whose sub items it shows, aria-expanded
            // whether they are shown (_toggleState), and Space presses it like Enter. It keeps the focus after a
            // press (no blur), so a keyboard can press it again; the ring shows on :focus-visible only.
            html += '<a class="openclose-open" id="a'
                + item.node_id
                + '" href="#" role="button" aria-expanded="false" aria-label="'
                + _attr( ( i18n.subitems || '%name' ).split( '%name' ).join( item.name ) )
                + '" onkeydown="if ( event.key === \' \' ) { event.preventDefault(); this.click(); }"'
                + ' onclick="return treeMenu.load( this, '
                + item.node_id
                + ', '
                + item.modified_subnode
                +' )"><\/a>';
        }

        var languages = "[";
        var firstLanguage = true;
        for ( var j = 0; j < item.languages.length; j++ )
        {
            if ( params.languages[item.languages[j]] )
            {
                if ( !firstLanguage )
                {
                    languages += ",";
                }
                firstLanguage = false;
                languages += "{locale:'"
                    + item.languages[j].replace(/'/g,"\\'")
                    + "',name:'"
                    + params.languages[item.languages[j]].replace(/'/g,"\\'")
                    + "'}";
            }
        }
        languages += "]";

        var canCreateClasses = false;
        var classes = "[";
        if ( params.createHereMenu != 'disabled' )
        {
            if ( params.createHereMenu == 'full' )
            {
                var classList = item.class_list;

                for ( var j = 0; j < classList.length; j++ )
                {
                    if ( params.classes[classList[j]] )
                    {
                        if ( canCreateClasses )
                        {
                            classes += ",";
                        }
                        canCreateClasses = true;
                        classes += "{classID:'"
                            + classList[j]
                            + "',name:'"
                            + String(params.classes[classList[j]].name).replace(/'/g,"\\'").replace(/>/g,'&gt;').replace(/"/g,'&quot;')
                            + "'}";
                    }
                }
            }
            else
            {
                for ( j in params.classes )
                {
                    if ( canCreateClasses )
                    {
                        classes += ",";
                    }
                    canCreateClasses = true;
                    classes += "{classID:'"
                        + j
                        + "',name:'"
                        + String(params.classes[j].name).replace(/'/g,"\\'").replace(/>/g,'&gt;').replace(/"/g,'&quot;')
                        + "'}";
                }
            }
        }
        classes += "]";

        var classIdentifier = params.classes[item.class_id].identifier;
        var icon = ( params.iconsList[classIdentifier] )? params.iconsList[classIdentifier]: params.iconsList['__default__'];
        if ( params.context != 'browse' && item.node_id > 1 )
        {
            html += '<a class="nodeicon" href="#" onclick="ezpopmenu_showTopLevel( event, \'ContextMenu\', {\'%nodeID%\':'
                + item.node_id
                + ', \'%objectID%\':'
                + item.object_id
                + ', \'%languages%\':'
                + languages
                + ', \'%classList%\':'
                + classes
                + ' }, \''
                + String(item.name).replace(/'/g,"\\'").replace(/>/g,'&gt;').replace(/"/g,'&quot;')
                + '\', '
                + item.node_id
                + ', '
                + ( ( canCreateClasses )? '-1':
                                          '\'menu-create-here\'' )
                + ' ); return false"><img src="'
                + icon
                + '" alt="" title="['
                + params.classes[item.class_id].name.replace(/>/g,'&gt;').replace(/"/g, '&quot;')
                + '] ' + i18n.expand + '" width="16" height="16" /><\/a>';
        }
        else
        {
            html += '<img src="'
                + icon
                + '" alt="" width="16" height="16" />';
        }
        html += '&nbsp;<a class="image-text" href="'
            + ( ( params.action )? params.action + '/' + item.node_id : item.url )
            + '"';

        if ( params.showTips )
        {
            html += ' title="' + i18n.node_id + ': '
                + item.node_id
                + ', ' + i18n.object_id + ': '
                + item.object_id
                + ', ' + i18n.visibility + ': '
                + ( item.is_hidden ? i18n.hidden: ( item.is_invisible ? i18n.hiddenbyparent : i18n.visible ) )
                + '"';
        }

        html += '>';
        var span = document.createElement( 'span' );
        span.setAttribute( 'class', 'node-name-' + ( ( item.is_hidden ) ? 'hidden': ( item.is_invisible ) ? 'hiddenbyparent' : 'normal' ) );
        span.innerHTML =  item.name ;
        var div = document.createElement( 'div' );
        div.appendChild( span );
        html += div.innerHTML;

        if ( item.is_hidden )
        {
            html += '<span class="node-hidden"> (' + i18n.hidden + ')<\/span>';
        }
        else if ( item.is_invisible )
        {
            html += '<span class="node-hiddenbyparent"> (' + i18n.hiddenbyparent + ')<\/span>';
        }

        html += '<\/a>';
        html += '<div id="c'
             + item.node_id
             + '"><\/div>';
        html += '<\/li>';

        return html;
    };

    this.load = function( aElement, nodeID, modifiedSubnode )
    {
        var divElement = document.getElementById('c' + nodeID);

        if ( !divElement )
        {
            return false;
        }

        if ( divElement.className == 'hidden' )
        {
            divElement.className = 'loaded';
            if ( aElement )
            {
                _toggleState( aElement, 'openclose-close' );
            }

            this.setOpen( nodeID );

            return false;
        }

        if ( divElement.className == 'loaded' )
        {
            divElement.className = 'hidden';
            if ( aElement )
            {
                _toggleState( aElement, 'openclose-open' );
            }

            this.setClosed( nodeID );

            return false;
        }

        if ( divElement.className == 'busy' )
        {
            return false;
        }

        var url = params.contentTreeUrl + nodeID
            + "/" + modifiedSubnode
            + "/" + params.expiry
            + "/" + params.perm;

        divElement.className = 'busy';
        if ( aElement )
        {
            _toggleState( aElement, 'openclose-busy' );
        }

        var thisThis = this, request = jQuery.ajax({
            'url': url,
            'dataType': 'json',
            'success': function( data, textStatus )
            {
                var html = '<ul>', items = [];
                // Filter out nodes to hide
                for ( var i = 0, l = data.children_count; i < l; i++ )
                {
                    if ( jQuery.inArray( data.children[i].node_id, params.hideNodes ) === -1 )
                    {
                        items.push( data.children[i] );
                    }
                }
                // Generate html content, one level below the opened node
                var childLevel = ( thisThis.levels[nodeID] || 0 ) + 1;
                for ( var i = 0, l = items.length; i < l; i++ )
                {
                    html += thisThis.generateEntry( items[i], i == l - 1, false, childLevel );
                }
                html += '<\/ul>';

                divElement.innerHTML += html;
                thisThis.decorate( divElement );
                divElement.className = 'loaded';
                if ( aElement )
                {
                    _toggleState( aElement, 'openclose-close' );
                }

                thisThis.setOpen( nodeID );
                thisThis.openUnder( nodeID );

                return;
            },
            'error': function( xhr, textStatus, errorThrown )
            {
                divElement.className = 'error';
                if (aElement) _toggleState( aElement, 'openclose-error' );

                function setErrorText( txt )
                {
                    if (aElement) aElement.title = txt;
                    else divElement.innerHTML = '<span></span>' + txt;
                }

                switch( xhr.status )
                {
                    case 403:
                    {
                      setErrorText( i18n.disabled );
                    } break;
                    case 404:
                    {
                      setErrorText( i18n.not_exist );
                    } break;
                    default:
                    {
                      setErrorText( i18n.internal_error );
                    }
                }
                if (aElement) aElement.onclick = function()
                {
                    return false;
                };
            }
        });

        return false;
    };

    this.openUnder = function( parentNodeID )
    {
        var divElement = document.getElementById( 'c' + parentNodeID );
        if ( !divElement )
        {
            return;
        }

        var ul = divElement.getElementsByTagName( 'ul' )[0];
        if ( !ul )
        {
            return;
        }

        var children = ul.childNodes;
        for ( var i = 0; i < children.length; i++ )
        {
            var liCandidate = children[i];
            if ( liCandidate.nodeType == 1 && liCandidate.id )
            {
                var nodeID = liCandidate.id.substr( 1 ), openIndex = jQuery.inArray( nodeID, this.autoOpenPath );
                if ( this.autoOpen && openIndex !== -1 )
                {
                    this.autoOpenPath.splice( openIndex, 1 );
                    this.setOpen( nodeID );
                }
                if ( this.isOpen( nodeID ) )
                {
                    var aElement = document.getElementById( 'a' + nodeID );
                    if ( aElement )
                    {
                        aElement.onclick();
                    }
                }
            }
        }
    };

    this.collapse = function( parentNodeID )
    {
        var divElement = document.getElementById( 'c' + parentNodeID );
        if ( !divElement )
        {
            return;
        }

        var aElements = divElement.getElementsByTagName( 'a' );
        for ( var index in aElements )
        {
            var aElement = aElements[index];
            if ( aElement.className == 'openclose-close' )
            {
                var nodeID        = aElement.id.substr( 1 );
                var subdivElement = document.getElementById( 'c' + nodeID );
                if ( subdivElement )
                {
                    subdivElement.className = 'hidden';
                }
                _toggleState( aElement, 'openclose-open' );
                this.setClosed( nodeID );
            }
        }

        var aElement = document.getElementById( 'a' + parentNodeID );
        if ( aElement )
        {
            divElement.className = 'hidden';
            _toggleState( aElement, 'openclose-open' );
            this.setClosed( parentNodeID );
        }
    };

    // The open/close button of a node: its class (the symbol), aria-expanded and, while the sub items load,
    // aria-busy. The node's tree item carries the same state.
    function _toggleState( a, state )
    {
        a.className = state;
        a.setAttribute( 'aria-expanded', state === 'openclose-close' ? 'true' : 'false' );
        if ( state === 'openclose-busy' ) a.setAttribute( 'aria-busy', 'true' );
        else a.removeAttribute( 'aria-busy' );
        var item = _ownItem( a.parentNode );
        if ( item )
        {
            item.setAttribute( 'aria-expanded', state === 'openclose-close' ? 'true' : 'false' );
            if ( state === 'openclose-busy' ) item.setAttribute( 'aria-busy', 'true' );
            else item.removeAttribute( 'aria-busy' );
        }
    }

    // The tree for screen readers and keyboards (WAI-ARIA tree view pattern, as the navigation tree example):
    // the list is a tree, every node's name link a tree item (its li is presentation), the list of its sub items
    // a group it owns. One tree item is in the Tab order: the current node's, else the first. Up and Down move
    // through the visible items, Right opens a node or moves to its first sub item, Left closes it or moves to its
    // parent, Home and End go to the first and last item, a letter to the next item starting with it, Enter
    // follows the link. The open/close button and the node icon stay for the mouse but leave the Tab order and
    // the accessibility tree: the tree item carries aria-expanded, and the node's menu opens on the tree item
    // with Shift+F10 or the context menu key, where ezpopupmenu.js supports that.
    this.tree = function( ul )
    {
        if ( !ul || ul.getAttribute( 'role' ) === 'tree' )
        {
            return;
        }
        ul.setAttribute( 'role', 'tree' );
        if ( i18n.tree )
        {
            ul.setAttribute( 'aria-label', i18n.tree );
        }
        this.treeElement = ul;
        this.decorate( ul );

        ul.addEventListener( 'focusin', function( e )
        {
            var target = e.target;
            if ( target.getAttribute && target.getAttribute( 'role' ) === 'treeitem' )
            {
                _tabStop( ul, target );
            }
            else if ( target.tagName === 'A' )
            {
                // The open/close button or node icon took the focus (a click, or the menu giving it back on
                // closing): hand it to the node's tree item.
                var item = _ownItem( target.parentNode );
                if ( item )
                {
                    item.focus();
                }
            }
        } );

        ul.addEventListener( 'keydown', function( e )
        {
            var item = e.target;
            if ( e.altKey || e.ctrlKey || e.metaKey || !item.getAttribute || item.getAttribute( 'role' ) !== 'treeitem' )
            {
                return;
            }
            var items = _visibleItems( ul ), index = jQuery.inArray( item, items ), next = null, li = item.parentNode;
            switch ( e.key )
            {
                case 'ArrowDown': next = items[index + 1]; break;
                case 'ArrowUp': next = items[index - 1]; break;
                case 'Home': next = items[0]; break;
                case 'End': next = items[items.length - 1]; break;
                case 'ArrowRight':
                    if ( item.getAttribute( 'aria-expanded' ) === 'false' )
                    {
                        _pressToggle( li );
                    }
                    else if ( item.getAttribute( 'aria-expanded' ) === 'true' )
                    {
                        var group = document.getElementById( item.getAttribute( 'aria-owns' ) || '' );
                        next = group ? _visibleItems( group )[0] : null;
                    }
                    break;
                case 'ArrowLeft':
                    if ( item.getAttribute( 'aria-expanded' ) === 'true' && _toggleOf( li ) )
                    {
                        _pressToggle( li );
                    }
                    else
                    {
                        next = _parentItem( li );
                    }
                    break;
                default:
                    if ( e.shiftKey || !e.key || e.key.length !== 1 || !/\S/.test( e.key ) )
                    {
                        return;
                    }
                    var key = e.key.toLowerCase();
                    for ( var i = 1; i <= items.length; i++ )
                    {
                        var candidate = items[( index + i ) % items.length];
                        if ( String( candidate.textContent ).trim().toLowerCase().indexOf( key ) === 0 )
                        {
                            next = candidate;
                            break;
                        }
                    }
                    if ( !next )
                    {
                        return;
                    }
            }
            e.preventDefault();
            if ( next )
            {
                next.focus();
            }
        } );
    };

    // Roles and states for the items under container (the tree, or the sub items just loaded)
    this.decorate = function( container )
    {
        var ul = this.treeElement;
        if ( !ul )
        {
            return;
        }
        var lis = container.getElementsByTagName( 'li' );
        for ( var i = 0; i < lis.length; i++ )
        {
            var li = lis[i], item = _ownItem( li );
            if ( !item || item.getAttribute( 'role' ) === 'treeitem' )
            {
                continue;
            }
            li.setAttribute( 'role', 'none' );
            item.setAttribute( 'role', 'treeitem' );
            item.setAttribute( 'tabindex', '-1' );
            var level = 1, siblings = [], parentList = li.parentNode;
            for ( var node = parentList; node && node !== ul; node = node.parentNode )
            {
                if ( node.tagName === 'UL' ) level++;
            }
            for ( var c = parentList.firstChild; c; c = c.nextSibling )
            {
                if ( c.nodeType === 1 && c.tagName === 'LI' ) siblings.push( c );
            }
            item.setAttribute( 'aria-level', level );
            item.setAttribute( 'aria-setsize', siblings.length );
            item.setAttribute( 'aria-posinset', jQuery.inArray( li, siblings ) + 1 );
            if ( /(^|\s)currentnode(\s|$)/.test( li.className ) )
            {
                item.setAttribute( 'aria-current', 'page' );
            }
            for ( c = li.firstChild; c; c = c.nextSibling )
            {
                if ( c.nodeType === 1 && c.tagName === 'A' && c !== item )
                {
                    c.setAttribute( 'tabindex', '-1' );
                    c.setAttribute( 'aria-hidden', 'true' );
                }
            }
            var toggle = _toggleOf( li ), childDiv = document.getElementById( 'c' + li.id.substr( 1 ) );
            if ( toggle )
            {
                item.setAttribute( 'aria-expanded', toggle.getAttribute( 'aria-expanded' ) || 'false' );
            }
            else if ( childDiv && childDiv.getElementsByTagName( 'li' ).length )
            {
                // The top node of the tree: always open
                item.setAttribute( 'aria-expanded', 'true' );
            }
        }
        // Groups: the list of sub items under each opened node, owned by the node's tree item
        var lists = container.getElementsByTagName( 'ul' );
        for ( i = 0; i < lists.length; i++ )
        {
            var group = lists[i], div = group.parentNode;
            if ( group === ul || !div || !div.id || div.id.charAt( 0 ) !== 'c' )
            {
                continue;
            }
            var owner = _ownItem( div.parentNode );
            group.setAttribute( 'role', 'group' );
            group.id = 'g' + div.id.substr( 1 );
            if ( owner )
            {
                owner.setAttribute( 'aria-owns', group.id );
                if ( !_toggleOf( div.parentNode ) )
                {
                    owner.setAttribute( 'aria-expanded', 'true' );
                }
            }
        }
        // The Tab stop is the current node's item once it is shown (the path to it opens step by step), unless
        // the focus is in the tree already; else the first item.
        var stop = _tabStop( ul ), current = jQuery( ul ).find( '[role="treeitem"][aria-current="page"]' )[0];
        if ( current && current !== stop && !( stop && stop.getAttribute( 'aria-current' ) === 'page' ) && !jQuery.contains( ul, document.activeElement ) )
        {
            _tabStop( ul, current );
        }
        else if ( !stop && _visibleItems( ul )[0] )
        {
            _tabStop( ul, _visibleItems( ul )[0] );
        }
    };

    // The name link of the node li: the tree item
    function _ownItem( li )
    {
        if ( !li || li.tagName !== 'LI' ) return null;
        for ( var c = li.firstChild; c; c = c.nextSibling )
        {
            if ( c.nodeType === 1 && c.tagName === 'A' && /(^|\s)image-text(\s|$)/.test( c.className ) ) return c;
        }
        return null;
    }

    // The open/close button of the node li, if it has one
    function _toggleOf( li )
    {
        for ( var c = li.firstChild; c; c = c.nextSibling )
        {
            if ( c.nodeType === 1 && c.tagName === 'A' && /(^|\s)openclose-/.test( c.className ) ) return c;
        }
        return null;
    }

    function _pressToggle( li )
    {
        var toggle = _toggleOf( li );
        if ( toggle && toggle.className !== 'openclose-busy' && toggle.className !== 'openclose-error' && toggle.onclick )
        {
            toggle.onclick();
        }
    }

    // The tree item of the node above li
    function _parentItem( li )
    {
        var div = li.parentNode && li.parentNode.parentNode;
        return div && div.parentNode ? _ownItem( div.parentNode ) : null;
    }

    // The tree items under root that are shown (their parents are open), in reading order
    function _visibleItems( root )
    {
        return jQuery( root ).find( '[role="treeitem"]' ).filter( function()
        {
            return this.getClientRects().length > 0;
        } ).get();
    }

    // Get, or move, the one tree item in the Tab order
    function _tabStop( ul, item )
    {
        var current = jQuery( ul ).find( '[role="treeitem"][tabindex="0"]' )[0] || null;
        if ( item && item !== current )
        {
            if ( current ) current.setAttribute( 'tabindex', '-1' );
            item.setAttribute( 'tabindex', '0' );
            current = item;
        }
        return current;
    }

    // A value for a double-quoted attribute of the generated markup
    function _attr( value )
    {
        return String( value ).replace( /&/g, '&amp;' ).replace( /"/g, '&quot;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' );
    }

    // Internal cookie functions
    function _setCookie( name, value, expires, path )
    {
        document.cookie = name + '=' + escape(value) + ( expires ? '; expires=' + expires.toUTCString(): '' ) + '; path='+ (path ? path : '/');
    }

    function _getCookie( name )
    {
        var n = name + '=', c = document.cookie, start = c.indexOf( n ), end = c.indexOf( ";", start );
        if ( start !== -1 )
        {
            return unescape( c.substring( start + n.length, ( end === -1 ? c.length : end ) ) );
        }
        return null;
    }

    function _delCookie( name )
    {
        _setCookie( name, '', ( new Date() - 86400000 ) );
    }
}
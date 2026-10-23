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

    // The data of each node's menu by node ID (generateEntry); its menu button carries only data-node-id.
    this.nodes = {};
    var thisMenu = this;

    // One listener opens the menu of every node: a click, Enter or Space on the menu button, and Shift+F10 or the
    // context menu key on a tree item (they click the button).
    document.addEventListener( 'click', function( e )
    {
        var opener = e.target && e.target.closest ? e.target.closest( 'a.nodeicon[data-node-id]' ) : null;
        var node = opener ? thisMenu.nodes[opener.getAttribute( 'data-node-id' )] : null;
        if ( !node )
        {
            return;
        }
        e.preventDefault();
        if ( !window.ezpopmenu_showTopLevel )
        {
            return;
        }
        // The menu reads the opener and how it was opened from the event; this listener sits on the document.
        var event = { type: e.type, detail: e.detail, pageX: e.pageX, pageY: e.pageY, clientX: e.clientX, clientY: e.clientY,
                      target: opener, currentTarget: opener };
        var nodeID = parseInt( opener.getAttribute( 'data-node-id' ), 10 );
        ezpopmenu_showTopLevel( event, 'ContextMenu', {
            '%nodeID%': nodeID,
            '%objectID%': node.objectID,
            '%languages%': node.languages,
            '%classList%': node.classes
        }, node.name, nodeID, node.disableMenuID );
    } );

    // The open/close buttons: a click (or Space, as for a button) loads, opens or closes the node's sub items. One
    // listener for all of them, no handler attribute (doc/eng-us/features/6.0/content-security-policy.md).
    document.addEventListener( 'click', function( e )
    {
        var toggle = e.target && e.target.closest ? e.target.closest( 'a[data-tree-load]' ) : null;
        if ( !toggle || thisMenu.levels[toggle.getAttribute( 'data-tree-load' )] === undefined )
        {
            return;
        }
        e.preventDefault();
        thisMenu.press( toggle );
    } );
    document.addEventListener( 'keydown', function( e )
    {
        var toggle = e.target && e.target.closest ? e.target.closest( 'a[data-tree-load]' ) : null;
        if ( toggle && e.key === ' ' && thisMenu.levels[toggle.getAttribute( 'data-tree-load' )] !== undefined )
        {
            e.preventDefault();
            toggle.click();
        }
    } );

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
            var expireDate = new Date();
            expireDate.setTime( expireDate.getTime() + this.cookieValidity * 86400000 );
            _setCookie( this.parameterName, this.cookie, expireDate );
        };

        this.isOpen = function( nodeID )
        {
            return this.open.indexOf( '' + nodeID ) !== -1;
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
            var openIndex = this.open.indexOf( '' + nodeID );
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
        // Node IDs and the subtree stamp are numbers; the name arrives HTML-encoded (treemenu.php: htmlentities,
        // the root node from the template: wash) and is decoded once here, then always handled as text.
        var nodeID = parseInt( item.node_id, 10 ) || 0, name = _text( item.name );
        level = level || 0;
        this.levels[nodeID] = level;
        var canOpen = item.has_children && ( this.maxDepth === 0 || level < this.maxDepth );
        if ( lastli )
        {
            liclass += ' lastli';
        }
        // a node on the path without sub items in the tree is marked too (_onPath compares as strings)
        if ( params.path && ( params.path[params.path.length-1] == item.node_id || ( !item.has_children && _onPath( item.node_id ) ) ) )
        {
            liclass += ' currentnode';
        }
        var html = '<li id="n'+nodeID+'"'
            + ( ( liclass )? ' class="' + liclass + '"':
                             '' )
            + '>';
        if ( canOpen && !rootNode )
        {
            // A button for keyboards and screen readers: its name says whose sub items it shows, aria-expanded
            // whether they are shown (_toggleState), and Space presses it like Enter. It keeps the focus after a
            // press (no blur), so a keyboard can press it again; the ring shows on :focus-visible only.
            html += '<a class="openclose-open" id="a'
                + nodeID
                + '" href="#" role="button" aria-expanded="false" aria-label="'
                + _attr( ( i18n.subitems || '%name' ).split( '%name' ).join( name ) )
                + '" data-tree-load="'
                + nodeID
                + '" data-tree-modified="'
                + ( parseInt( item.modified_subnode, 10 ) || 0 )
                + '"><\/a>';
        }

        // What the node's menu needs is kept here, by node ID, and read by the click listener below: names are
        // data and never become part of an inline script.
        var languages = [];
        for ( var j = 0; j < item.languages.length; j++ )
        {
            if ( params.languages[item.languages[j]] )
            {
                languages.push( { locale: String( item.languages[j] ), name: String( params.languages[item.languages[j]] ) } );
            }
        }

        var classes = [];
        if ( params.createHereMenu != 'disabled' )
        {
            if ( params.createHereMenu == 'full' )
            {
                var classList = item.class_list;

                for ( var j = 0; j < classList.length; j++ )
                {
                    if ( params.classes[classList[j]] )
                    {
                        classes.push( { classID: String( classList[j] ), name: String( params.classes[classList[j]].name ) } );
                    }
                }
            }
            else
            {
                for ( j in params.classes )
                {
                    classes.push( { classID: String( j ), name: String( params.classes[j].name ) } );
                }
            }
        }
        var canCreateClasses = classes.length > 0;

        // A class created after the tree's parameters were cached is not in params.classes: the node is drawn with
        // the default icon and no class name instead of throwing, which left its parent busy for good.
        var itemClass = params.classes[item.class_id] || { identifier: '', name: '' };
        var classIdentifier = itemClass.identifier;
        var icon = ( params.iconsList[classIdentifier] )? params.iconsList[classIdentifier]: params.iconsList['__default__'];
        if ( params.context != 'browse' && item.node_id > 1 )
        {
            this.nodes[nodeID] = {
                objectID: parseInt( item.object_id, 10 ) || 0,
                name: name,
                languages: languages,
                classes: classes,
                disableMenuID: canCreateClasses ? -1 : 'menu-create-here'
            };
            // A menu button named after the node (the image inside is decorative), opened with Enter, Space or
            // Shift+F10 too (ezpopupmenu.js); the click listener of the tree opens its menu.
            html += '<a class="nodeicon" href="#" role="button" aria-haspopup="menu" aria-expanded="false" aria-label="'
                + _attr( ( i18n.actions || '%name' ).split( '%name' ).join( name ) )
                + '" data-node-id="'
                + nodeID
                + '"><img src="'
                + _attr( icon )
                + '" alt="" title="['
                + _attr( itemClass.name )
                + '] ' + _attr( i18n.expand ) + '" width="16" height="16" /><\/a>';
        }
        else
        {
            html += '<img src="'
                + _attr( icon )
                + '" alt="" width="16" height="16" />';
        }
        html += '&nbsp;<a class="image-text" href="'
            + _attr( ( params.action )? params.action + '/' + nodeID : item.url )
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
        // its own direction run: an English name in a right-to-left page keeps its end punctuation at its end; only
        // there, as an isolated name is not hyphenated (the tree's li has hyphens: auto) and left to right stays as it was
        if ( document.documentElement && document.documentElement.getAttribute( 'dir' ) === 'rtl' ) span.setAttribute( 'dir', 'auto' );
        span.textContent = name;
        var div = document.createElement( 'div' );
        div.appendChild( span );
        html += div.innerHTML;

        // A status mark (components.css .a4-mark) after the name, only where something differs, so the tree
        // stays quiet: a ring for a hidden node, a ring with a bar for one hidden by a node above it, a crossed
        // ring for a disabled user account (treemenu.php sends user_status only to users with role/read).
        // The hidden marks are decorative (title only): the "(Hidden)" text after them names the state for screen
        // readers. The account mark has no text of its own and is named (role="img").
        if ( item.is_hidden || item.is_invisible )
        {
            var visibilityName = item.is_hidden ? i18n.hidden : i18n.hiddenbyparent;
            html += '<span class="a4-mark is-' + ( item.is_hidden ? 'hidden' : 'hidden-by-parent' )
                + '" title="' + visibilityName + '" aria-hidden="true"><\/span>';
        }
        if ( item.user_status === 'disabled' )
        {
            var accountName = i18n.account_disabled || 'Disabled';
            html += '<span class="a4-mark is-disabled" role="img" aria-label="' + accountName + '" title="' + accountName + '"><\/span>';
        }

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
             + nodeID
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

        // After a failed load a press tries again: the failure may have been passing (a timeout, a restart)
        if ( divElement.className == 'error' )
        {
            divElement.className = '';
            divElement.innerHTML = '';
            if ( aElement )
            {
                aElement.removeAttribute( 'title' );
                _undescribe( aElement.parentNode );
            }
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

        // The sub items as JSON, requested as an XMLHttpRequest (the Accept and X-Requested-With headers, the
        // browser's cache, the session cookie): an answer outside 2xx or one that is not JSON is an error with its
        // status, a network failure one with status 0.
        var thisThis = this;
        var success = function( data )
        {
            var html = '<ul>', items = [];
            // Filter out nodes to hide
            for ( var i = 0, l = data.children_count; i < l; i++ )
            {
                if ( params.hideNodes.indexOf( data.children[i].node_id ) === -1 )
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
        };
        var error = function( status )
        {
            divElement.className = 'error';
            if (aElement) _toggleState( aElement, 'openclose-error' );

            function setErrorText( txt )
            {
                if (aElement) aElement.title = txt;
                else
                {
                    // an empty span, then the text as text: the message is never parsed as markup
                    divElement.textContent = '';
                    divElement.appendChild( document.createElement( 'span' ) );
                    divElement.appendChild( document.createTextNode( txt ) );
                }
                if (aElement) _describe( aElement.parentNode, txt );
            }

            switch( status )
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
        };
        fetch( url, {
            method: 'GET',
            credentials: 'same-origin',
            // the wildcard type in two parts: whole, it would read as the start of a comment to tools that strip them
            headers: { 'Accept': 'application/json, text/javascript, */' + '*; q=0.01', 'X-Requested-With': 'XMLHttpRequest' }
        } ).then( function( response )
        {
            return response.text().then( function( text )
            {
                if ( !response.ok )
                {
                    throw response.status;
                }
                try { return JSON.parse( text ); } catch ( e ) { throw response.status; }
            } );
        } ).then( success, function( failure )
        {
            // success() is not covered: an exception there is not a failed load
            error( typeof failure === 'number' ? failure : 0 );
        } );

        return false;
    };

    // An open/close button pressed: the node's sub items loaded, opened or closed
    this.press = function( toggle )
    {
        return this.load( toggle, parseInt( toggle.getAttribute( 'data-tree-load' ), 10 ),
                          parseInt( toggle.getAttribute( 'data-tree-modified' ), 10 ) || 0 );
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
                var nodeID = liCandidate.id.substr( 1 ), openIndex = this.autoOpenPath.indexOf( nodeID );
                if ( this.autoOpen && openIndex !== -1 )
                {
                    this.autoOpenPath.splice( openIndex, 1 );
                    this.setOpen( nodeID );
                }
                if ( this.isOpen( nodeID ) )
                {
                    var aElement = document.getElementById( 'a' + nodeID );
                    if ( aElement && aElement.hasAttribute( 'data-tree-load' ) )
                    {
                        this.press( aElement );
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
            // Opened, but every sub item was filtered out (hideNodes): nothing to expand
            var childDiv = a.parentNode.id ? document.getElementById( 'c' + a.parentNode.id.substr( 1 ) ) : null;
            if ( state === 'openclose-close' && childDiv && !childDiv.getElementsByTagName( 'li' ).length )
                item.removeAttribute( 'aria-expanded' );
            else
                item.setAttribute( 'aria-expanded', state === 'openclose-close' ? 'true' : 'false' );
            if ( state === 'openclose-busy' ) item.setAttribute( 'aria-busy', 'true' );
            else item.removeAttribute( 'aria-busy' );
            // Closed with the mouse (browsers that do not focus a clicked link): the Tab stop may sit in the items
            // that just went away; it moves to the closed node.
            var tree = _treeOf( item ), stop = tree ? _tabStop( tree ) : null;
            if ( state === 'openclose-open' && stop && !_shown( stop ) )
            {
                _tabStop( tree, item );
            }
        }
    }

    // The tree for screen readers and keyboards (WAI-ARIA tree view pattern, as the navigation tree example):
    // the list is a tree, every node's name link a tree item (its li is presentation), the list of its sub items
    // a group it owns. One tree item is in the Tab order: the current node's, else the first. Up and Down move
    // through the visible items, Right opens a node or moves to its first sub item, Left closes it or moves to its
    // parent, Home and End go to the first and last item, typed letters to the next item starting with them, Enter
    // follows the link, Shift+F10 or the context menu key opens the node's menu. The open/close button and the
    // node icon stay for the mouse but leave the Tab order and the accessibility tree: the tree item carries
    // aria-expanded, and the focus they get from the keyboard or a closing menu goes to the tree item.
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

        var pointer = false, typed = '', typedAt = 0;
        ul.addEventListener( 'mousedown', function()
        {
            pointer = true;
            setTimeout( function() { pointer = false; }, 0 );
        } );

        ul.addEventListener( 'focusin', function( e )
        {
            var target = e.target;
            if ( target.getAttribute && target.getAttribute( 'role' ) === 'treeitem' )
            {
                _tabStop( ul, target );
            }
            else if ( target.tagName === 'A' )
            {
                // The open/close button or node icon took the focus (the keyboard, or a closing menu giving it
                // back): hand it to the node's tree item. Not for a click on the node icon or while its menu is
                // open: the menu's keys work from the icon.
                var item = _ownItem( target.parentNode );
                if ( item && !( /(^|\s)nodeicon(\s|$)/.test( target.className ) && ( pointer || target.getAttribute( 'aria-expanded' ) === 'true' ) ) )
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
            var li = item.parentNode, next = null, handled = false;
            if ( e.key === 'ContextMenu' || ( e.shiftKey && e.key === 'F10' ) )
            {
                // The node's own menu button (a direct child of its li, never one of a sub item)
                var opener = _childLink( li, 'nodeicon' );
                if ( opener )
                {
                    e.preventDefault();
                    e.stopPropagation();
                    opener.click();
                }
                return;
            }
            if ( e.shiftKey && e.key !== 'Home' && e.key !== 'End' && e.key.length !== 1 )
            {
                return;
            }
            switch ( e.key )
            {
                case 'ArrowDown': next = _step( ul, item, 1 ); break;
                case 'ArrowUp': next = _step( ul, item, -1 ); break;
                case 'Home': next = _step( ul, null, 1 ); break;
                case 'End': next = _step( ul, null, -1 ); break;
                case 'ArrowRight':
                    if ( item.getAttribute( 'aria-expanded' ) === 'false' )
                    {
                        handled = _pressToggle( li );
                    }
                    else if ( item.getAttribute( 'aria-expanded' ) === 'true' )
                    {
                        var group = document.getElementById( item.getAttribute( 'aria-owns' ) || '' );
                        next = group ? _step( group, null, 1 ) : null;
                    }
                    break;
                case 'ArrowLeft':
                    if ( item.getAttribute( 'aria-expanded' ) === 'true' && _toggleOf( li ) )
                    {
                        handled = _pressToggle( li );
                    }
                    else
                    {
                        next = _parentItem( li );
                    }
                    break;
                default:
                    if ( !e.key || e.key.length !== 1 || !/\S/.test( e.key ) )
                    {
                        return;
                    }
                    // Type-ahead: letters typed within half a second form one search
                    var now = new Date().getTime();
                    typed = ( now - typedAt < 500 ? typed : '' ) + e.key.toLowerCase();
                    typedAt = now;
                    next = _find( ul, item, typed );
            }
            if ( next )
            {
                next.focus();
                handled = true;
            }
            if ( handled )
            {
                e.preventDefault();
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
        var lists = Array.prototype.slice.call( container.getElementsByTagName( 'ul' ) );
        if ( container === ul )
        {
            lists.unshift( ul );
        }
        for ( var l = 0; l < lists.length; l++ )
        {
            // One pass per list: level once, position by a running counter
            var list = lists[l], lis = [], level = 1, c;
            for ( var node = list; node && node !== ul; node = node.parentNode )
            {
                if ( node.tagName === 'UL' ) level++;
            }
            for ( c = list.firstChild; c; c = c.nextSibling )
            {
                if ( c.nodeType === 1 && c.tagName === 'LI' ) lis.push( c );
            }
            var div = list.parentNode, owner = list !== ul && div && div.id && div.id.charAt( 0 ) === 'c' ? _ownItem( div.parentNode ) : null;
            if ( list !== ul )
            {
                if ( !lis.length )
                {
                    // Every sub item was filtered out (hideNodes): no group
                    if ( owner && !_toggleOf( div.parentNode ) ) owner.removeAttribute( 'aria-expanded' );
                    continue;
                }
                list.setAttribute( 'role', 'group' );
                list.id = 'g' + div.id.substr( 1 );
                if ( owner )
                {
                    owner.setAttribute( 'aria-owns', list.id );
                    if ( !_toggleOf( div.parentNode ) )
                    {
                        // The top node of the tree: always open
                        owner.setAttribute( 'aria-expanded', 'true' );
                    }
                }
            }
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
                item.setAttribute( 'aria-level', level );
                item.setAttribute( 'aria-setsize', lis.length );
                item.setAttribute( 'aria-posinset', i + 1 );
                // The node's menu opens with Shift+F10 (its icon button is hidden from the tree): say so
                if ( _childLink( li, 'nodeicon' ) )
                {
                    item.setAttribute( 'aria-keyshortcuts', 'Shift+F10' );
                }
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
                var toggle = _toggleOf( li );
                if ( toggle )
                {
                    item.setAttribute( 'aria-expanded', toggle.getAttribute( 'aria-expanded' ) || 'false' );
                }
            }
        }
        // The Tab stop is the current node's item once it is shown (the path to it opens step by step), unless
        // the focus is in the tree already; else the first shown item, else the first item.
        var stop = _tabStop( ul ), current = ul.querySelector( '[role="treeitem"][aria-current="page"]' );
        if ( current && current !== stop && !( stop && stop.getAttribute( 'aria-current' ) === 'page' ) && !_inside( ul, document.activeElement ) )
        {
            _tabStop( ul, current );
        }
        else if ( !stop )
        {
            var first = _step( ul, null, 1 ) || ul.querySelector( '[role="treeitem"]' );
            if ( first ) _tabStop( ul, first );
        }
    };

    // node lies inside outer (outer itself does not count)
    function _inside( outer, node )
    {
        return !!node && node !== outer && outer.contains( node );
    }

    // The name link of the node li: the tree item
    function _ownItem( li )
    {
        return _childLink( li, 'image-text' );
    }

    // The link with the given class among the direct children of li
    function _childLink( li, className )
    {
        if ( !li || li.tagName !== 'LI' ) return null;
        var pattern = new RegExp( '(^|\\s)' + className + '(\\s|$)' );
        for ( var c = li.firstChild; c; c = c.nextSibling )
        {
            if ( c.nodeType === 1 && c.tagName === 'A' && pattern.test( c.className ) ) return c;
        }
        return null;
    }

    // The open/close button of the node li, if it has one
    function _toggleOf( li )
    {
        return _childLink( li, 'openclose-(?:open|close|busy|error)' );
    }

    // Press the open/close button of the node li; false when there is nothing to press
    function _pressToggle( li )
    {
        var toggle = _toggleOf( li );
        if ( toggle && toggle.className !== 'openclose-busy' && toggle.hasAttribute( 'data-tree-load' ) )
        {
            thisMenu.press( toggle );
            return true;
        }
        return false;
    }

    // The tree item of the node above li
    function _parentItem( li )
    {
        var div = li.parentNode && li.parentNode.parentNode;
        return div && div.parentNode ? _ownItem( div.parentNode ) : null;
    }

    // The tree the item belongs to
    function _treeOf( item )
    {
        for ( var node = item; node; node = node.parentNode )
        {
            if ( node.getAttribute && node.getAttribute( 'role' ) === 'tree' ) return node;
        }
        return null;
    }

    function _shown( item )
    {
        return item.getClientRects().length > 0;
    }

    // The next shown tree item under root after (direction 1) or before (-1) from, or the first or last one
    // when from is null; only the items passed are measured.
    function _step( root, from, direction )
    {
        var items = root.querySelectorAll( '[role="treeitem"]' ), i;
        if ( from )
        {
            for ( i = 0; i < items.length && items[i] !== from; i++ );
            i += direction;
        }
        else
        {
            i = direction > 0 ? 0 : items.length - 1;
        }
        for ( ; i >= 0 && i < items.length; i += direction )
        {
            if ( _shown( items[i] ) ) return items[i];
        }
        return null;
    }

    // The next shown tree item whose name starts with text, after from (at from itself for a longer search),
    // wrapping round
    function _find( root, from, text )
    {
        var items = root.querySelectorAll( '[role="treeitem"]' ), start = 0;
        for ( ; start < items.length && items[start] !== from; start++ );
        if ( text.length === 1 ) start++;
        for ( var n = 0; n < items.length; n++ )
        {
            var candidate = items[( start + n ) % items.length];
            if ( String( candidate.textContent ).trim().toLowerCase().indexOf( text ) === 0 && _shown( candidate ) ) return candidate;
        }
        return null;
    }

    // Get, or move, the one tree item in the Tab order
    function _tabStop( ul, item )
    {
        var current = ul.querySelector( '[role="treeitem"][tabindex="0"]' );
        if ( item && item !== current )
        {
            if ( current ) current.setAttribute( 'tabindex', '-1' );
            item.setAttribute( 'tabindex', '0' );
            current = item;
        }
        return current;
    }

    // A message about the node li for screen readers (its open/close button, which shows it as a title, is
    // hidden from them): a visually hidden text the tree item is described by
    function _describe( li, text )
    {
        var item = _ownItem( li );
        if ( !item || !li.id ) return;
        var id = 'e' + li.id.substr( 1 ), span = document.getElementById( id );
        if ( !span )
        {
            span = document.createElement( 'span' );
            span.id = id;
            span.hidden = true;   // aria-describedby reads hidden text; no loose text in the tree
            li.appendChild( span );
        }
        span.textContent = text;
        item.setAttribute( 'aria-describedby', id );
    }

    // Takes back what _describe said about the node li
    function _undescribe( li )
    {
        var item = _ownItem( li );
        if ( !item || !li.id ) return;
        var span = document.getElementById( 'e' + li.id.substr( 1 ) );
        if ( span ) span.parentNode.removeChild( span );
        item.removeAttribute( 'aria-describedby' );
    }

    // The text of an HTML-encoded value ("Tom &amp; Jerry" is "Tom & Jerry"); a textarea parses no markup
    function _text( html )
    {
        var textarea = document.createElement( 'textarea' );
        textarea.innerHTML = html === undefined || html === null ? '' : String( html );
        return textarea.value;
    }

    // A value for a double-quoted attribute of the generated markup
    function _attr( value )
    {
        return String( value ).replace( /&/g, '&amp;' ).replace( /"/g, '&quot;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' );
    }

    // The node is on the path to the current node (params.path: strings from the template; a node ID from the JSON
    // is a number)
    function _onPath( nodeID )
    {
        for ( var i = 0; i < params.path.length; i++ )
        {
            if ( String( params.path[i] ) === String( nodeID ) ) return true;
        }
        return false;
    }

    // Internal cookie functions (only where localStorage is blocked): encodeURIComponent, not the deprecated
    // escape(); same site only, secure over https
    function _setCookie( name, value, expires, path )
    {
        document.cookie = name + '=' + encodeURIComponent( value ) + ( expires ? '; expires=' + expires.toUTCString(): '' ) + '; path='+ (path ? path : '/')
            + '; SameSite=Lax' + ( window.location && window.location.protocol === 'https:' ? '; Secure' : '' );
    }

    // The cookie of exactly this name; a value escape()d by an older version reads the same (node IDs and "/")
    function _getCookie( name )
    {
        var all = document.cookie ? document.cookie.split( /;\s*/ ) : [];
        for ( var i = 0; i < all.length; i++ )
        {
            if ( all[i].indexOf( name + '=' ) !== 0 ) continue;
            var value = all[i].substring( name.length + 1 );
            try { return decodeURIComponent( value ); } catch ( e ) { return value; }
        }
        return null;
    }
}

/*
 * The tree of a page: each div.content-tree-menu (contentstructuremenu/content_structure_menu_dynamic.tpl) carries
 * its data as JSON in data-tree-root, data-tree-params and data-tree-request, and its list (ul#content_tree_menu).
 * The tree is drawn when the page is parsed; window.treeMenu is the last one, as with the inline script before.
 */
ContentStructureMenu.fromElement = function( holder )
{
    var read = function( name )
    {
        try { return JSON.parse( holder.getAttribute( name ) || '{}' ) || {}; } catch ( e ) { return {}; }
    };
    var ul = holder.querySelector( 'ul' ), rootNode = read( 'data-tree-root' ), params = read( 'data-tree-params' );
    var request = read( 'data-tree-request' ), i18n = params.i18n || {};
    if ( !ul || rootNode.node_id === undefined )
    {
        return null;
    }
    for ( var key in request )
    {
        params[key] = request[key];
    }
    var hideNodes = [];
    for ( var h = 0; params.hideNodes && h < params.hideNodes.length; h++ )
    {
        if ( !isNaN( parseInt( params.hideNodes[h], 10 ) ) ) hideNodes.push( parseInt( params.hideNodes[h], 10 ) );
    }
    params.hideNodes = hideNodes;
    params.action = params.action || '';
    params.iconsList = {};
    for ( var identifier in params.icons || {} )
    {
        params.iconsList[identifier] = params.wwwDirPrefix + params.icons[identifier];
    }
    params.iconsList['__default__'] = params.wwwDirPrefix + params.defaultIcon;
    if ( params.preloadIcons && window.ezjslib_preloadImageList )
    {
        window.ezjslib_preloadImageList( params.iconsList );
    }

    var menu = new ContentStructureMenu( params, i18n );
    ul.innerHTML = menu.generateEntry( rootNode, false, true );
    menu.tree( ul );
    menu.load( false, rootNode.node_id, rootNode.modified_subnode );
    holder.contentStructureMenu = menu;
    window.treeMenu = menu;
    return menu;
};

ContentStructureMenu.drawAll = function()
{
    if ( !document.querySelectorAll )
    {
        return;
    }
    var holders = document.querySelectorAll( 'div.content-tree-menu[data-tree-root]' );
    for ( var i = 0; i < holders.length; i++ )
    {
        if ( !holders[i].contentStructureMenu )
        {
            ContentStructureMenu.fromElement( holders[i] );
        }
    }
};

if ( document.readyState === 'loading' )
{
    document.addEventListener( 'DOMContentLoaded', ContentStructureMenu.drawAll );
}
else
{
    ContentStructureMenu.drawAll();
}

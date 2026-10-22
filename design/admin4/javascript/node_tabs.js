// the tabs of the node view, in plain JavaScript; set up once the document is parsed (the head loads this file),
// one turn after DOMContentLoaded as jQuery's ready did
(function( init )
{
    if ( document.readyState === 'loading' )
        document.addEventListener( 'DOMContentLoaded', function(){ window.setTimeout( init ); } );
    else
        window.setTimeout( init );
})(function()
{
    // the element children of el that match the selector (jQuery's children( selector ))
    function children( el, selector )
    {
        var out = [];
        for ( var i = 0; i < el.children.length; i++ )
        {
            if ( el.children[i].matches( selector ) ) out.push( el.children[i] );
        }
        return out;
    }

    function each( list, fn )
    {
        Array.prototype.forEach.call( list, fn );
    }

    // the installation's root: the Exp configuration block of the admin <head>, else ezjscore's $.ez where a page
    // still loads it
    function rootUrl()
    {
        var root = '';
        each( document.querySelectorAll( 'script[type="application/json"]#exp-config, script[type="application/json"][data-exp-config]' ), function( block )
        {
            try
            {
                var data = JSON.parse( block.textContent );
                if ( !root && data && data.config && data.config.root ) root = String( data.config.root );
            }
            catch ( e ) {}
        });
        var jq = window.jQuery;
        if ( !root && jq && jq.ez && jq.ez.root_url ) root = jq.ez.root_url;
        return root || '/';
    }

    var NodeTab = {

        timeout : null,

        // click on a tab event: the tab's li, whatever element inside the link was clicked
        click : function( e )
        {
            NodeTab.open( this.closest( 'li' ).id, true );

            // enable tabs if disabled
            var link = document.getElementById('maincontent-show');
            if ( link )
            {
                NodeTab.toggle( document.querySelectorAll('div.tab-block ul.tabs'), document.querySelectorAll('div.tab-block div.tabs-content'), link );
            }

            e.preventDefault();
            e.stopPropagation();
        },

        // toggle tabs ( enable / disable state )
        toggleClick : function( e )
        {
            NodeTab.toggle( document.querySelectorAll('div.tab-block ul.tabs'), document.querySelectorAll('div.tab-block div.tabs-content'), this );
            e.preventDefault();
            e.stopPropagation();
        },

        // toggle tabs ( enable / disable state ): disabled while any of the tab lists is
        toggle : function( ul, div, link )
        {
            if ( Array.prototype.some.call( ul, function( el ){ return el.classList.contains('disabled'); } ) )
            {
                each( ul, function( el ){ el.classList.remove('disabled'); } );
                each( div, function( el ){ el.classList.remove('disabled'); } );
                NodeTab.saveTabState( 1 );
                link.innerHTML = '&nbsp;';
                link.id = 'maincontent-hide';
                NodeTab.toggleState( link, true );
            }
            else
            {
                each( ul, function( el ){ el.classList.add('disabled'); } );
                each( div, function( el ){ el.classList.add('disabled'); } );
                NodeTab.saveTabState( 0 );
                link.innerHTML = '&nbsp;';
                link.id = 'maincontent-show';
                NodeTab.toggleState( link, false );
            }
            each( ul, function( el ){ NodeTab.aria( el ); } );
            return false;
        },

        // the show/hide tabs toggle keeps one name and reports in aria-pressed whether the tabs are shown; its
        // title says what a press does
        toggleState : function( link, on )
        {
            link.setAttribute( 'aria-pressed', on ? 'true' : 'false' );
            var title = link.getAttribute( on ? 'data-title-hide' : 'data-title-show' );
            if ( title ) link.title = title;
        },

        // The tabs as the WAI-ARIA tabs pattern: tablist, tab with aria-selected and aria-controls, tabpanel, and
        // only the selected tab in the Tab order (the arrow keys reach the others). While the tabs are switched off
        // (ul.disabled, every panel shown) the roles are taken away again.
        aria : function( ul )
        {
            var on = !ul.classList.contains( 'disabled' ), items = children( ul, 'li' ), first = null, noneSelected = true;
            if ( on ) ul.setAttribute( 'role', 'tablist' ); else ul.removeAttribute( 'role' );
            // with no tab selected the first one takes the Tab stop, so the keyboard can still reach the list
            items.forEach( function( li )
            {
                var a = children( li, 'a' );
                if ( a.length && !first ) first = a[0];
                if ( a.length && li.classList.contains( 'selected' ) ) noneSelected = false;
            });
            items.forEach( function( li )
            {
                var a = children( li, 'a' )[0];
                if ( !a ) return;
                var panel = li.id ? document.getElementById( li.id + '-content' ) : null,
                    selected = li.classList.contains( 'selected' ),
                    stop = selected || ( noneSelected && a === first );
                if ( on )
                {
                    li.setAttribute( 'role', 'presentation' );
                    if ( !a.id && li.id ) a.id = li.id + '-tab';
                    a.setAttribute( 'role', 'tab' );
                    a.setAttribute( 'aria-selected', selected ? 'true' : 'false' );
                    a.setAttribute( 'tabindex', stop ? '0' : '-1' );
                    if ( panel )
                    {
                        a.setAttribute( 'aria-controls', panel.id );
                        panel.setAttribute( 'role', 'tabpanel' );
                        panel.setAttribute( 'aria-labelledby', a.id );
                    }
                }
                else
                {
                    li.removeAttribute( 'role' );
                    [ 'role', 'aria-selected', 'aria-controls', 'tabindex' ].forEach( function( name ){ a.removeAttribute( name ); } );
                    if ( panel )
                    {
                        panel.removeAttribute( 'role' );
                        panel.removeAttribute( 'aria-labelledby' );
                    }
                }
            });
        },

        // Left and Right (a horizontal tab list: Up and Down stay with the page), Home and End move between the tabs and open the one they reach
        keydown : function( e )
        {
            var ul = this.closest( 'ul.tabs' );
            if ( ul.classList.contains( 'disabled' ) ) return;
            var tabs = [], i, next = null;
            children( ul, 'li' ).forEach( function( li ){ tabs = tabs.concat( children( li, 'a' ) ); } );
            i = tabs.indexOf( this );
            switch ( e.key )
            {
                case 'ArrowRight': next = tabs[( i + 1 ) % tabs.length]; break;
                case 'ArrowLeft': next = tabs[( i - 1 + tabs.length ) % tabs.length]; break;
                case 'Home': next = tabs[0]; break;
                case 'End': next = tabs[tabs.length - 1]; break;
                default: return;
            }
            e.preventDefault();
            NodeTab.open( next.closest( 'li' ).id, true );
            next.focus();
        },

        // open a tab & optionally save state
        open : function( id, save )
        {
            var li = id ? document.getElementById( id ) : null;
            if ( li && !li.classList.contains('selected') )
            {
                if ( NodeTab.timeout !== null )
                {
                    clearTimeout( NodeTab.timeout );
                    NodeTab.timeout = null;
                }
                var block = li.closest( 'div.tab-block' ), content = document.getElementById( id + '-content' );
                if ( block )
                {
                    each( block.querySelectorAll('ul.tabs li.selected'), function( el ){ el.classList.remove('selected'); } );
                    each( block.querySelectorAll('div.tab-content.selected'), function( el ){ el.classList.add('hide'); el.classList.remove('selected'); } );
                }
                li.classList.add('selected');
                if ( content )
                {
                    content.classList.add('selected');
                    content.classList.remove('hide');
                }
                NodeTab.aria( li.parentNode );

                var wc = li.parentNode.parentNode;
                if ( wc && wc.id === 'window-controls' )
                {
                    wc.classList.toggle( 'layouts-active', id === 'node-tab-layouts' );
                }

                if ( save ) NodeTab.timeout = setTimeout( function(){ NodeTab.saveOpenTab( id ) }, 400 );
            }
        },

        // save tab state: the preference admin_navigation_content through window.a4Pref (the head script), like the
        // frame's other remembered choices (a state the server does not take is kept in this browser); where it is
        // missing a POST as jQuery's $.post() sent it (no Content-Type and no body without a form token)
        saveTabState : function( intBool )
        {
            if ( window.a4Pref )
            {
                window.a4Pref.set( 'admin_navigation_content', String( intBool ) );
                return;
            }
            var url = rootUrl() + 'user/preferences/set_and_exit/admin_navigation_content/' + intBool;
            var _token = '', _tokenNode = document.getElementById('ezxform_token_js');
            // a space as + (jQuery's form encoding of the data); fetch() sends the same Accept as $.post() did on its own
            if ( _tokenNode ) _token = 'ezxform_token=' + encodeURIComponent( _tokenNode.getAttribute('title') ).replace( /%20/g, '+' );
            var headers = _token ? { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' } : {};
            headers['X-Requested-With'] = 'XMLHttpRequest';
            window.fetch( url, { method: 'POST', credentials: 'same-origin', headers: headers, body: _token || null } ).catch( function(){} );
        },

        // save open tab (using cookie)
        saveOpenTab : function( id )
        {
            if ( id.indexOf( 'node-tab-' ) === 0 )
            {
                var expireDate = new Date();
                expireDate.setTime( expireDate.getTime() + 365 * 86400000 );
                NodeTab.setCookie( 'adminNavigationTab', id.split('tab-')[1], expireDate );
            }
        },

        // set cookie value (encodeURIComponent, not the deprecated escape(); same site only, secure over https)
        setCookie: function( name, value, expires, path )
        {
            document.cookie = name + '=' + encodeURIComponent( value ) + ( expires ? '; expires=' + expires.toUTCString(): '' ) + '; path='+ (path ? path : '/')
                + '; SameSite=Lax' + ( window.location && window.location.protocol === 'https:' ? '; Secure' : '' );
        },

        // get cookie value: the cookie of exactly this name; a value escape()d by an older version reads the same
        // (tab names are ASCII, where both encodings agree)
        getCookie: function( name )
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
    };

    each( document.querySelectorAll('div.tab-block ul.tabs li a'), function( a )
    {
        a.addEventListener( 'click', NodeTab.click );
        a.addEventListener( 'keydown', NodeTab.keydown );
    });
    each( document.querySelectorAll('div.tab-block ul.tabs'), function( ul ){ NodeTab.aria( ul ); } );

    // Both states of the switch: the show link (#maincontent-show) followed its href before, a GET that changed
    // the preference and reloaded the page; the script changes the id between the two.
    each( document.querySelectorAll('a.show-hide-tabs'), function( link )
    {
        link.addEventListener( 'click', NodeTab.toggleClick );
        link.addEventListener( 'keydown', function( e )
        {
            // role="button": Space presses it like Enter
            if ( e.key === ' ' ) { e.preventDefault(); this.click(); }
        });
    });

    // the tab of the cookie: only a tab name goes into the selector (a broken value would throw in it)
    var openTab = NodeTab.getCookie( 'adminNavigationTab' );
    if ( openTab && /^[a-z0-9_-]+$/i.test( openTab ) && document.querySelector('div.tab-block ul.tabs.tabs-by-cookie #node-tab-' + openTab) )
    {
        NodeTab.open( 'node-tab-' + openTab );
    }
});

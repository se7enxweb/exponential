// jquery code to a provide tabbing
jQuery(function( $ )
{
    var NodeTab = {

        timeout : null,

        // click on a tab event: the tab's li, whatever element inside the link was clicked
        click : function( e )
        {
            NodeTab.open( $( this ).closest( 'li' ).attr( 'id' ), true );

            // enable tabs if disabled
            var link = document.getElementById('maincontent-show');
            if ( link )
            {
                NodeTab.toggle( $('div.tab-block ul.tabs'), $('div.tab-block div.tabs-content'), link );
            }

            return false;
        },

        // toggle tabs ( enable / disable state )
        toggleClick : function( e )
        {
            NodeTab.toggle( $('div.tab-block ul.tabs'), $('div.tab-block div.tabs-content'), this );
            return false;
        },

        // toggle tabs ( enable / disable state )
        toggle : function( ul, div, link )
        {
            if ( ul.hasClass('disabled') )
            {
                ul.removeClass('disabled');
                div.removeClass('disabled');
                NodeTab.saveTabState( 1 );
                link.innerHTML = '&nbsp;';
                link.id = 'maincontent-hide';
                NodeTab.toggleState( link, true );
            }
            else
            {
                ul.addClass('disabled');
                div.addClass('disabled');
                NodeTab.saveTabState( 0 );
                link.innerHTML = '&nbsp;';
                link.id = 'maincontent-show';
                NodeTab.toggleState( link, false );
            }
            ul.each( function(){ NodeTab.aria( this ); } );
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
            var $ul = $( ul ), on = !$ul.hasClass( 'disabled' );
            if ( on ) $ul.attr( 'role', 'tablist' ); else $ul.removeAttr( 'role' );
            // with no tab selected the first one takes the Tab stop, so the keyboard can still reach the list
            var noneSelected = !$ul.children( 'li.selected' ).children( 'a' ).length;
            $ul.children( 'li' ).each( function()
            {
                var li = this, a = $( li ).children( 'a' )[0];
                if ( !a ) return;
                var panel = li.id ? document.getElementById( li.id + '-content' ) : null,
                    selected = $( li ).hasClass( 'selected' ),
                    stop = selected || ( noneSelected && a === $ul.children( 'li' ).children( 'a' )[0] );
                if ( on )
                {
                    $( li ).attr( 'role', 'presentation' );
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
                    $( li ).removeAttr( 'role' );
                    $( a ).removeAttr( 'role aria-selected aria-controls tabindex' );
                    if ( panel ) $( panel ).removeAttr( 'role aria-labelledby' );
                }
            });
        },

        // Left and Right (a horizontal tab list: Up and Down stay with the page), Home and End move between the tabs and open the one they reach
        keydown : function( e )
        {
            var ul = $( this ).closest( 'ul.tabs' );
            if ( ul.hasClass( 'disabled' ) ) return;
            var tabs = ul.children( 'li' ).children( 'a' ).toArray(), i = tabs.indexOf( this ), next = null;
            switch ( e.key )
            {
                case 'ArrowRight': next = tabs[( i + 1 ) % tabs.length]; break;
                case 'ArrowLeft': next = tabs[( i - 1 + tabs.length ) % tabs.length]; break;
                case 'Home': next = tabs[0]; break;
                case 'End': next = tabs[tabs.length - 1]; break;
                default: return;
            }
            e.preventDefault();
            NodeTab.open( $( next ).closest( 'li' ).attr( 'id' ), true );
            next.focus();
        },

        // open a tab & optionally save state
        open : function( id, save )
        {
            var li = $( '#' + id );
            if ( li.length && !li.hasClass('selected') )
            {
                if ( NodeTab.timeout !== null )
                {
                    clearTimeout( NodeTab.timeout );
                    NodeTab.timeout = null;
                }
                var block = $( li[0].parentNode.parentNode.parentNode );
                block.find("ul.tabs li.selected").removeClass('selected');
                block.find("div.tab-content.selected").addClass('hide').removeClass('selected');
                li.addClass('selected');
                $( '#' + id + '-content' ).addClass('selected').removeClass('hide');
                NodeTab.aria( li[0].parentNode );

                var wc = li[0].parentNode.parentNode;
                if ( wc && wc.id === 'window-controls' )
                {
                    $( wc ).toggleClass( 'layouts-active', id === 'node-tab-layouts' );
                }

                if ( save ) NodeTab.timeout = setTimeout( function(){ NodeTab.saveOpenTab( id ) }, 400 );
            }
        },

        // save tab state (using ajax)
        saveTabState : function( intBool )
        {
            var url = $.ez.url.replace( 'ezjscore/', 'user/preferences/set_and_exit/admin_navigation_content/' ) + intBool;
            var _token = '', _tokenNode = document.getElementById('ezxform_token_js');
            if ( _tokenNode ) _token = 'ezxform_token=' + _tokenNode.getAttribute('title');
            $.post( url, _token, function(){} );
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

        // set cookie value
        setCookie: function( name, value, expires, path )
        {
            document.cookie = name + '=' + escape(value) + ( expires ? '; expires=' + expires.toUTCString(): '' ) + '; path='+ (path ? path : '/');
        },

        // get cookie value
        getCookie: function( name )
        {
            var n = name + '=', c = document.cookie, start = c.indexOf( n ), end = c.indexOf( ";", start );
            if ( start !== -1 )
            {
                return unescape( c.substring( start + n.length, ( end === -1 ? c.length : end ) ) );
            }
            return null;
        }
    };

    $('div.tab-block ul.tabs li a').on( 'click', NodeTab.click ).on( 'keydown', NodeTab.keydown );
    $('div.tab-block ul.tabs').each( function(){ NodeTab.aria( this ); } );

    // Both states of the switch: the show link (#maincontent-show) followed its href before, a GET that changed
    // the preference and reloaded the page; the script changes the id between the two.
    $('a.show-hide-tabs').on( 'click', NodeTab.toggleClick );
    $('a.show-hide-tabs').on( 'keydown', function( e )
    {
        // role="button": Space presses it like Enter
        if ( e.key === ' ' ) { e.preventDefault(); this.click(); }
    });

    var openTab = NodeTab.getCookie( 'adminNavigationTab' );
    if ( openTab && $('div.tab-block ul.tabs.tabs-by-cookie #node-tab-' + openTab).length )
    {
        NodeTab.open( 'node-tab-' + openTab );
    }
});

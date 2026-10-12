/**
 * Custom tag controls for the Online Editor on TinyMCE 3 (ezoe.ini [EditorSettings] CustomTagControls=enabled).
 *
 * When the caret is in a custom tag, a small bar on the tag offers: move up, move down (block custom tags),
 * edit (opens the tag's dialog) and remove (the tag goes, its content stays). The same from the keyboard:
 * Alt+Shift+Up, Alt+Shift+Down, Alt+Shift+E, Alt+Shift+Delete; Backspace in an empty block custom tag
 * replaces it with an empty paragraph. Every block custom tag shows its name as a label (ezoe_customtags.css).
 *
 * The bar lives in the page above the editor's frame, never in the edited document, so nothing of it can
 * reach the saved text. Uses TinyMCE 3's plugin API and ezjscore's jQuery; no YUI.
 *
 * Copyright (C) 1998 - 2026 7x & Exponential Foundation, GNU General Public License v2.0 (or any later version)
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 63334f1681), Copyright (C) Mugo Web, GPL v2:
 * the design of the custom_tags_controls plugin (@copyright Copyright 2014, Mugo Web) -- controls on the focused
 * custom tag to move it up and down and to edit it, and Backspace removing an empty custom tag -- with thanks to
 * Mugo Web.
 */
( function()
{
    var BAR_GAP = 2;

    function isCustomTag( n )
    {
        return n && n.nodeType === 1 && n.getAttribute( 'type' ) === 'custom';
    }

    tinymce.create( 'tinymce.plugins.exp_customtag_controls',
    {
        init: function( ed, url )
        {
            var t = this;
            t.ed = ed;
            t.current = null;
            t.bar = null;

            ed.onInit.add( function( ed )
            {
                t.addLabels( ed );
                t.createBar( ed );
                // reposition when the frame or the page scrolls or changes size
                tinymce.dom.Event.add( ed.getWin(), 'scroll', function() { t.position(); } );
                tinymce.dom.Event.add( window, 'scroll', function() { t.position(); } );
                tinymce.dom.Event.add( window, 'resize', function() { t.position(); } );
            });

            ed.onNodeChange.add( function( ed, cm, n )
            {
                t.update( n );
            });
            ed.onKeyUp.add( function( ed )
            {
                t.update( ed.selection.getNode() );
            });
            ed.onRemove.add( function()
            {
                if ( t.bar && t.bar.parentNode )
                    t.bar.parentNode.removeChild( t.bar );
                t.bar = null;
            });

            ed.onKeyDown.add( function( ed, e )
            {
                var done = false;
                if ( e.altKey && e.shiftKey && !e.ctrlKey && !e.metaKey )
                {
                    if ( e.keyCode === 38 )
                        done = t.move( false );
                    else if ( e.keyCode === 40 )
                        done = t.move( true );
                    else if ( e.keyCode === 69 )
                        done = t.edit();
                    else if ( e.keyCode === 46 || e.keyCode === 8 )
                        done = t.remove();
                }
                else if ( e.keyCode === 8 && !e.altKey && !e.ctrlKey && !e.metaKey )
                {
                    done = t.removeIfEmpty();
                }
                if ( done )
                    return tinymce.dom.Event.cancel( e );
            });
        },

        text: function( key, fallback )
        {
            return this.ed.getLang( 'customtagcontrols.' + key, fallback );
        },

        // labels: one "::before" rule per block custom tag with its CustomTagsDescription, see ezoe_customtags.css
        addLabels: function( ed )
        {
            var tags = ed.getParam( 'ez_custom_tags', [] ), css = '', i, d;
            for ( i = 0; i < tags.length; i++ )
            {
                d = tags[i];
                if ( !d.inline && /^[A-Za-z0-9_\-]+$/.test( d.name ) )
                    css += 'div.ezoeItemCustomTag.' + d.name + '::before { content: "' + String( d.title || d.name ).replace( /["\\\n\r]/g, ' ' ) + '"; }\n';
            }
            ed.dom.addClass( ed.getBody(), 'ezoe-custom-tag-labels' );
            if ( css )
                ed.dom.addStyle( css );
        },

        createBar: function( ed )
        {
            var t = this, bar = document.createElement( 'div' ), buttons = [
                [ 'up', '▲', t.text( 'up', 'Move the custom tag up' ), 'Alt+Shift+Up', function() { t.move( false ); } ],
                [ 'down', '▼', t.text( 'down', 'Move the custom tag down' ), 'Alt+Shift+Down', function() { t.move( true ); } ],
                [ 'edit', '✎', t.text( 'edit', 'Edit the custom tag' ), 'Alt+Shift+E', function() { t.edit(); } ],
                [ 'remove', '✕', t.text( 'remove', 'Remove the custom tag' ), 'Alt+Shift+Delete', function() { t.remove(); } ]
            ];
            bar.className = 'ezoe-custom-tag-controls';
            bar.id = ed.id + '_customtag_controls';
            bar.setAttribute( 'role', 'toolbar' );
            bar.setAttribute( 'aria-label', t.text( 'bar', 'Custom tag controls' ) );
            bar.hidden = true;
            jQuery.each( buttons, function( i, b )
            {
                var button = document.createElement( 'button' );
                button.type = 'button';
                button.className = 'ezoe-custom-tag-' + b[0];
                button.setAttribute( 'aria-label', b[2] );
                button.title = b[2] + ' (' + b[3] + ')';
                button.appendChild( document.createTextNode( b[1] ) );
                // keep the editor's selection: act on click, never take the focus on mousedown
                jQuery( button ).on( 'mousedown', function( e ) { e.preventDefault(); } ).on( 'click', function( e )
                {
                    e.preventDefault();
                    b[4]();
                });
                bar.appendChild( button );
            });
            jQuery( bar ).on( 'keydown', function( e )
            {
                if ( e.keyCode === 27 )
                {
                    e.preventDefault();
                    ed.focus();
                }
            });
            document.body.appendChild( bar );
            t.bar = bar;
        },

        tagOf: function( n )
        {
            var ed = this.ed;
            return n ? ed.dom.getParent( n, isCustomTag, ed.getBody() ) : null;
        },

        blockTagOf: function( n )
        {
            var el = this.tagOf( n );
            return el && el.nodeName === 'DIV' ? el : null;
        },

        sibling: function( el, forward )
        {
            var n = forward ? el.nextSibling : el.previousSibling;
            while ( n && n.nodeType !== 1 )
                n = forward ? n.nextSibling : n.previousSibling;
            return n;
        },

        update: function( n )
        {
            var el = this.tagOf( n );
            // custom tags inside embeds are not the editor's to move
            if ( el && this.ed.dom.getParent( el, function( p ) { return /^eZ(Object|Node)_/.test( p.id || '' ); }, this.ed.getBody() ) )
                el = null;
            this.current = el;
            this.position();
        },

        position: function()
        {
            var t = this, bar = t.bar, el = t.current, ed = t.ed;
            if ( !bar )
                return;
            if ( !el || !el.parentNode || !ed.getBody().contains( el ) )
            {
                bar.hidden = true;
                return;
            }
            var block = el.nodeName === 'DIV';
            jQuery( '.ezoe-custom-tag-up, .ezoe-custom-tag-down', bar ).toggle( block );
            jQuery( '.ezoe-custom-tag-up', bar ).prop( 'disabled', !( block && t.sibling( el, false ) ) );
            jQuery( '.ezoe-custom-tag-down', bar ).prop( 'disabled', !( block && t.sibling( el, true ) ) );
            bar.hidden = false;

            var frame = document.getElementById( ed.id + '_ifr' ), f = frame.getBoundingClientRect(), r = el.getBoundingClientRect(),
                top = f.top + Math.max( 0, r.top ) + BAR_GAP, left = f.left + Math.min( r.right, f.width ) - bar.offsetWidth - BAR_GAP;
            if ( r.bottom < 0 || r.top > f.height )
            {
                bar.hidden = true;
                return;
            }
            left = Math.max( f.left + BAR_GAP, left );
            bar.style.top = Math.round( top + window.pageYOffset ) + 'px';
            bar.style.left = Math.round( left + window.pageXOffset ) + 'px';
        },

        caretTo: function( el )
        {
            var ed = this.ed;
            ed.selection.select( el, true );
            ed.selection.collapse( true );
            ed.nodeChanged();
        },

        move: function( forward )
        {
            var t = this, ed = t.ed, el = t.blockTagOf( ed.selection.getNode() ) || ( t.current && t.current.nodeName === 'DIV' ? t.current : null ),
                other = el && t.sibling( el, forward );
            if ( !other )
                return false;
            ed.undoManager.add();
            if ( forward )
            {
                el.parentNode.insertBefore( other, el );
                // keep a paragraph after a tag that became the last one, to go on writing after it
                if ( !t.sibling( el, true ) && el.parentNode === ed.getBody() )
                    ed.dom.insertAfter( ed.dom.create( 'p', {}, tinymce.isIE ? '&nbsp;' : '<br data-mce-bogus="1" />' ), el );
            }
            else
                el.parentNode.insertBefore( el, other );
            ed.undoManager.add();
            ed.isNotDirty = false;
            t.caretTo( el );
            return true;
        },

        edit: function()
        {
            var ed = this.ed, el = this.tagOf( ed.selection.getNode() ) || this.current, x;
            if ( !el || !ed.theme.__getTagCommand )
                return false;
            x = ed.theme.__getTagCommand( el );
            if ( !x )
                return false;
            ed.selection.select( el );
            ed.execCommand( x.cmd, el, x.val );
            return true;
        },

        remove: function()
        {
            var t = this, ed = t.ed, el = t.tagOf( ed.selection.getNode() ) || t.current, parent;
            if ( !el )
                return false;
            parent = el.parentNode;
            ed.undoManager.add();
            // the content stays (an inline image tag has none)
            ed.dom.remove( el, el.nodeName !== 'IMG' );
            ed.undoManager.add();
            ed.isNotDirty = false;
            t.current = null;
            ed.nodeChanged();
            t.position();
            return true;
        },

        removeIfEmpty: function()
        {
            var t = this, ed = t.ed, el = t.blockTagOf( ed.selection.getNode() ), p;
            if ( !el || ( el.textContent || '' ).replace( /[\s ​]+/g, '' ) !== '' || jQuery( 'img,table,[id^=eZObject_],[id^=eZNode_]', el ).length )
                return false;
            p = ed.dom.create( 'p', {}, tinymce.isIE ? '&nbsp;' : '<br data-mce-bogus="1" />' );
            ed.undoManager.add();
            el.parentNode.replaceChild( p, el );
            ed.undoManager.add();
            ed.isNotDirty = false;
            t.caretTo( p );
            return true;
        },

        getInfo: function()
        {
            return {
                longname: 'Custom tag controls',
                author: '7x & Exponential Foundation, after a design by Mugo Web',
                version: '1.0'
            };
        }
    });

    tinymce.PluginManager.add( 'exp_customtag_controls', tinymce.plugins.exp_customtag_controls );
})();

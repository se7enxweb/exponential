/**
 * Starts eZ Online Editor on TinyMCE 8 (design:content/datatype/edit/ezxmltext_ezoe_tinymce8.tpl) from a file, so
 * the edit page needs no inline script and runs under a Content Security Policy without 'unsafe-inline'.
 *
 * The template writes two data blocks once per page, <script type="application/json" id="ezoe8-settings"> (the
 * settings every editor shares) and id="ezoe8-button-map" (ezoe.ini [EditorLayout] button names to TinyMCE 8 toolbar
 * items), and gives each editor's textarea its attribute id, toolbar buttons, path location and rows in
 * data-ezoe8-attribute, data-ezoe8-buttons (JSON), data-ezoe8-path-location and data-ezoe8-rows. This file is loaded
 * with defer, reads the blocks and starts an editor for every such textarea. eZOe8Start() starts the ones added
 * later; eZOe8Init(), eZOe8Toolbar() and eZOe8StickyOffset() stay global as before.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 */
(function( window, document )
{
    'use strict';

    // The parsed content of a data block, null when it is missing or broken (the error goes to the console)
    function readData( id )
    {
        var element = document.getElementById( id );
        if ( !element )
            return null;
        try
        {
            return JSON.parse( element.textContent );
        }
        catch ( error )
        {
            if ( window.console )
                window.console.error( 'eZ Online Editor: the data block #' + id + ' is not valid JSON', error );
            return null;
        }
    }

    var globalSettings = null, buttonMap = null;

    function data()
    {
        if ( globalSettings === null )
        {
            globalSettings = readData( 'ezoe8-settings' );
            buttonMap = readData( 'ezoe8-button-map' ) || {};
            window.eZOe8GlobalSettings = globalSettings;
            window.eZOe8ButtonMap = buttonMap;
        }
        return globalSettings;
    }

    // Maps the button names of ezoe.ini [EditorLayout] Buttons[] (TinyMCE 3 ez theme) to TinyMCE 8 toolbar items.
    // Buttons without a counterpart are dropped.
    function eZOe8Toolbar( buttons )
    {
        var items = [];
        data();
        buttons.forEach( function( b ) {
            var item = buttonMap[ b ];
            // skip unknown buttons, duplicates (image/object both map to ezembed) and double separators
            if ( !item || ( item !== '|' && items.indexOf( item ) !== -1 ) || ( item === '|' && ( !items.length || items[ items.length - 1 ] === '|' ) ) )
                return;
            items.push( item );
        });
        if ( items[ items.length - 1 ] === '|' )
            items.pop();
        return items.join( ' ' ) + ' | code';
    }

    // Where the visible page starts under the admin's fixed header and sticky button bar, 0 without them
    function eZOe8StickyOffset()
    {
        var edge = window.getComputedStyle ? parseFloat( window.getComputedStyle( document.documentElement ).getPropertyValue( '--a4-sticky-edge' ) ) : NaN;
        return isNaN( edge ) ? 0 : edge;
    }

    function eZOe8Init( id, attributeId, buttons, pathLocation, rows )
    {
        var settings = Object.assign( {}, data(), {
            selector: '#' + id,
            toolbar: eZOe8Toolbar( buttons ),
            toolbar_mode: 'wrap',
            // the toolbar stays in view while the editor scrolls, below a fixed header and sticky button bar when the
            // admin design names their bottom (--a4-sticky-edge on <html>); the offset is set again when the editor is
            // ready and whenever the edge moves (the bar wraps, the header grows), see setup
            toolbar_sticky: true,
            toolbar_sticky_offset: eZOe8StickyOffset(),
            statusbar: pathLocation !== 'none',
            height: Math.max( 300, rows * 24 ),
            setup: function( editor ) {
                editor.options.register( 'ez_skin_class', { processor: 'string', default: '' } );
                editor.options.register( 'ez_disable_editor_text', { processor: 'string', default: 'Disable editor' } );

                // The admin design may not have measured its header and button bar yet: the sticky toolbar's offset
                // is read when the editor is ready and again on every change of the edge (admin4.js sends
                // a4:sticky-edge). TinyMCE reads the option on each scroll.
                var stickyOffset = function() {
                    editor.options.set( 'toolbar_sticky_offset', eZOe8StickyOffset() );
                    if ( editor.initialized )
                        editor.dispatch( 'ResizeWindow' );
                };
                editor.on( 'init', stickyOffset );
                document.addEventListener( 'a4:sticky-edge', stickyOffset );
                editor.on( 'remove', function() {
                    document.removeEventListener( 'a4:sticky-edge', stickyOffset );
                });

                // skin class for skins/<skin>/skin.css on the editor container
                editor.on( 'PostRender', function() {
                    if ( editor.options.get( 'ez_skin_class' ) )
                    {
                        editor.getContainer().classList.add( editor.options.get( 'ez_skin_class' ) );
                        // dialogs and menus are rendered outside the editor container
                        document.body.classList.add( editor.options.get( 'ez_skin_class' ) );
                    }
                });

                // toolbar counterpart of the "Disable editor" form button (ezoe.ini [EditorLayout] button "disable")
                editor.ui.registry.addButton( 'ezdisable', {
                    icon: 'close',
                    tooltip: editor.options.get( 'ez_disable_editor_text' ),
                    onAction: function() {
                        var button = document.querySelector( 'input[name="CustomActionButton[' + attributeId + '_disable_editor]"]' );
                        if ( button )
                        {
                            editor.save();
                            button.click();
                        }
                    },
                    onSetup: function( api ) {
                        api.setEnabled( !!document.querySelector( 'input[name="CustomActionButton[' + attributeId + '_disable_editor]"]' ) );
                    }
                });
            }
        });
        // a text in a right-to-left content language: the textarea says dir="rtl" and its language tag (lang)
        var source = document.getElementById( id );
        if ( source && source.getAttribute( 'dir' ) === 'rtl' )
            settings.directionality = 'rtl';
        if ( source && source.getAttribute( 'lang' ) )
        {
            var setupBefore = settings.setup;
            settings.setup = function( editor ) {
                setupBefore( editor );
                editor.on( 'init', function() {
                    editor.getBody().setAttribute( 'lang', source.getAttribute( 'lang' ) );
                });
            };
        }
        window.tinymce.init( settings );
    }

    // Starts an editor for every textarea the template marked and that has none yet
    function eZOe8Start()
    {
        if ( !data() || !window.tinymce )
            return;
        var areas = document.querySelectorAll( 'textarea[data-ezoe8-attribute]' );
        for ( var i = 0; i < areas.length; i++ )
        {
            var area = areas[i], buttons = [];
            if ( area.getAttribute( 'data-ezoe8-started' ) === '1' || !area.id )
                continue;
            area.setAttribute( 'data-ezoe8-started', '1' );
            try
            {
                buttons = JSON.parse( area.getAttribute( 'data-ezoe8-buttons' ) || '[]' );
            }
            catch ( error )
            {
                if ( window.console )
                    window.console.error( 'eZ Online Editor: data-ezoe8-buttons of #' + area.id + ' is not valid JSON', error );
            }
            eZOe8Init( area.id, parseInt( area.getAttribute( 'data-ezoe8-attribute' ), 10 ), buttons,
                       area.getAttribute( 'data-ezoe8-path-location' ) || '', parseInt( area.getAttribute( 'data-ezoe8-rows' ), 10 ) || 10 );
        }
    }

    window.eZOe8Toolbar = eZOe8Toolbar;
    window.eZOe8StickyOffset = eZOe8StickyOffset;
    window.eZOe8Init = eZOe8Init;
    window.eZOe8Start = eZOe8Start;

    if ( document.readyState === 'loading' )
        document.addEventListener( 'DOMContentLoaded', eZOe8Start );
    else
        eZOe8Start();
})( window, document );

/**
 * eZ Online Editor - TinyMCE 8 prototype: custom tag plugin
 *
 * Produces the same XHTML markup as eZOEXMLInput::inputTagXML() for custom tags, so the
 * existing eZOEInputParser converts it back to ezxml unchanged:
 *
 *   block:        <div class="ezoeItemCustomTag factbox" type="custom" customattributes="title|Infoattribute_separationalign|right">...</div>
 *   inline:       <span class="ezoeItemCustomTag strike" type="custom">...</span>  (<u> for underline)
 *   inline image: <img src="icon" class="ezoeItemCustomTag mytag" type="custom" width="22" height="22" />
 *
 * Tag and attribute definitions come from eZOEXMLInput::getCustomTagDefinitions()
 * (content.ini [CustomTagSettings] and [<tag>] CustomAttributes, ezoe_attributes.ini).
 *
 * Licensed under the GNU General Public License v2.0, like the rest of ezoe.
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commits e21ef46691 and 63334f1681),
 * Copyright (C) Mugo Web, GPL v2
 */
(function () {
    'use strict';

    // Custom tags with a native xhtml counterpart, edited with the subscript / superscript buttons
    var NATIVE_TAGS = { sub: true, sup: true };
    var D = window.eZOe8Dialog;

    tinymce.addI18n( 'de', {
        'Custom tag': 'Custom Tag',
        'Insert custom tag': 'Custom Tag einfügen',
        'Edit custom tag': 'Custom Tag bearbeiten',
        'Remove custom tag': 'Custom Tag entfernen',
        'Tag': 'Tag',
        'This tag has no attributes.': 'Dieses Tag hat keine Attribute.',
        'No custom tags are configured in content.ini [CustomTagSettings].': 'In content.ini [CustomTagSettings] sind keine Custom Tags eingerichtet.'
    } );

    tinymce.PluginManager.add( 'ezcustomtag', function ( editor ) {

        editor.options.register( 'ez_custom_tags', { processor: 'array', default: [] } );
        editor.options.register( 'ez_custom_attribute_style_map', { processor: 'object', default: {} } );
        // ezoe.ini [EditorSettings] CustomTagControls: { enabled: bool, texts: { up, down, edit, remove, example } }
        editor.options.register( 'ez_custom_tag_controls', { processor: 'object', default: { enabled: false, texts: {} } } );

        var t = function ( text, value ) {
            var s = editor.translate( text );
            return value === undefined ? s : s.replace( '%s', value );
        };

        var definitions = function () {
            return editor.options.get( 'ez_custom_tags' ).filter( function ( d ) {
                return !NATIVE_TAGS[ d.name ];
            } );
        };

        var getDefinition = function ( name ) {
            return editor.options.get( 'ez_custom_tags' ).filter( function ( d ) {
                return d.name === name;
            } )[0] || null;
        };

        // Returns the custom tag element node is part of, or null
        var getCustomTag = function ( node ) {
            return editor.dom.getParent( node, function ( n ) {
                return n.nodeType === 1 && n.getAttribute( 'type' ) === 'custom';
            }, editor.getBody() );
        };

        // The custom tag name is stored as class, next to the internal ezoeItem* classes
        var getTagName = function ( el ) {
            return ( el.getAttribute( 'class' ) || '' ).split( /\s+/ ).filter( function ( c ) {
                return c && !/^(ezoeItem|ezoeAlign|mceItem)/.test( c );
            } )[0] || '';
        };

        // custom attribute handling shared with the literal dialog, see ezoe_dialog.js
        var parseCustomAttributes = D.parseCustomAttributes,
            serializeCustomAttributes = D.serializeCustomAttributes,
            escapeHtml = D.escapeHtml,
            fieldName = D.attributeFieldName,
            buildField = D.attributeField,
            toFieldValue = D.toAttributeFieldValue,
            fromFieldValue = D.fromAttributeFieldValue;

        var validate = function ( attribute, value ) {
            return D.validateAttribute( attribute, value, t );
        };

        // Same as eZOEXMLInput::getCustomAttrPart(): mapped custom attributes become inline styles
        var buildStyle = function ( values ) {
            var map = editor.options.get( 'ez_custom_attribute_style_map' ), style = '';
            Object.keys( values ).forEach( function ( key ) {
                if ( map[key] && /(margin|border|padding|width|height)/.test( map[key] ) )
                    style += map[key] + ': ' + values[key] + '; ';
            } );
            return style.trim();
        };

        var buildHtml = function ( definition, values, content ) {
            var attrs = ' type="custom"', customAttributes = serializeCustomAttributes( values ), style = buildStyle( values );
            if ( customAttributes )
                attrs += ' customattributes="' + escapeHtml( customAttributes ) + '"';
            if ( style )
                attrs += ' style="' + escapeHtml( style ) + '"';

            if ( definition.inline === 'image' )
                return '<img src="' + escapeHtml( definition.icon ) + '" class="ezoeItemCustomTag ' + definition.name + '"' + attrs + ' width="22" height="22" />';

            if ( definition.inline )
            {
                var tag = definition.name === 'underline' ? 'u' : 'span';
                return '<' + tag + ' class="ezoeItemCustomTag ' + definition.name + '"' + attrs + '>' + ( content || definition.name ) + '</' + tag + '>';
            }

            // An empty block tag gets a paragraph with the tag name, eZOEInputParser::structHandlerParagraph()
            // removes exactly that placeholder again when saving
            return '<div class="ezoeItemCustomTag ' + definition.name + '"' + attrs + '>' +
                   ( content && /^\s*<(p|h[1-6]|ul|ol|table|pre|div)\b/i.test( content ) ? content : '<p>' + ( content || definition.name ) + '</p>' ) +
                   '</div>';
        };

        var updateElement = function ( el, values ) {
            var dom = editor.dom, customAttributes = serializeCustomAttributes( values ), style = buildStyle( values );
            dom.setAttrib( el, 'customattributes', customAttributes || null );
            dom.setAttrib( el, 'style', style || null );
        };

        var openDialog = function ( target ) {
            // content.ini [CustomTagSettings] IsHidden[]: not offered, except the hidden tag being edited
            // (LoveStack commit e21ef46691, Copyright (C) Mugo Web, GPL v2)
            var targetName = target ? getTagName( target ) : '';
            var list = definitions().filter( function ( d ) {
                return !d.hidden || d.name === targetName;
            } );
            if ( !list.length )
            {
                editor.notificationManager.open( { text: t( 'No custom tags are configured in content.ini [CustomTagSettings].' ), type: 'warning', timeout: 5000 } );
                return;
            }

            var stored = target ? parseCustomAttributes( target.getAttribute( 'customattributes' ) ) : {};

            var initialData = function ( definition, previous ) {
                var data = { tag: definition.name };
                definition.attributes.forEach( function ( attribute ) {
                    var name = fieldName( attribute );
                    data[name] = previous && previous.hasOwnProperty( name ) ? previous[name] : toFieldValue( attribute, stored[attribute.id] );
                } );
                return data;
            };

            var spec = function ( definition, data ) {
                var items = [ {
                    type: 'listbox',
                    name: 'tag',
                    label: t( 'Tag' ),
                    enabled: !target,
                    items: list.map( function ( d ) {
                        return { text: d.title, value: d.name };
                    } )
                } ];

                var visible = definition.attributes.filter( function ( a ) {
                    return a.type !== 'hidden';
                } );
                if ( visible.length )
                    items = items.concat( visible.map( buildField ) );
                else
                    items.push( { type: 'htmlpanel', html: '<p>' + escapeHtml( t( 'This tag has no attributes.' ) ) + '</p>' } );
                // the example image, images/editor/custom_tags_thumbs/<name>.png (LoveStack commit 63334f1681)
                if ( definition.preview )
                    items.push( { type: 'htmlpanel', html: '<div class="ezoe-custom-tag-preview"><p><strong>' + escapeHtml( controlText( 'example', 'Example' ) ) + '</strong></p>' +
                                  '<img src="' + escapeHtml( definition.preview ) + '" alt="" style="max-width: 100%;" /></div>' } );

                return {
                    title: target ? t( 'Edit custom tag' ) : t( 'Insert custom tag' ),
                    size: 'normal',
                    body: { type: 'panel', items: items },
                    initialData: data,
                    buttons: [
                        { type: 'cancel', text: t( 'Cancel' ) },
                        { type: 'submit', text: 'OK', primary: true }
                    ],
                    onChange: function ( api, details ) {
                        if ( details.name !== 'tag' )
                            return;
                        var next = getDefinition( api.getData().tag );
                        if ( next )
                        {
                            stored = {};
                            api.redial( spec( next, initialData( next ) ) );
                        }
                    },
                    onSubmit: function ( api ) {
                        var dialogData = api.getData(), values = {}, error = null, errorField = null;

                        definition.attributes.forEach( function ( attribute ) {
                            var value = attribute.type === 'hidden'
                                ? fromFieldValue( attribute, stored[attribute.id] !== undefined ? stored[attribute.id] : attribute['default'] )
                                : fromFieldValue( attribute, dialogData[ fieldName( attribute ) ] );
                            var message = attribute.type === 'hidden' ? null : validate( attribute, value );
                            if ( message && !error )
                            {
                                error = message;
                                errorField = fieldName( attribute );
                            }
                            if ( value !== null )
                                values[attribute.id] = value;
                        } );

                        if ( error )
                        {
                            editor.notificationManager.open( { text: error, type: 'warning', timeout: 4000 } );
                            api.focus( errorField );
                            return;
                        }

                        api.close();
                        editor.undoManager.transact( function () {
                            if ( target )
                                updateElement( target, values );
                            else
                                editor.insertContent( buildHtml( definition, values, editor.selection.isCollapsed() ? '' : editor.selection.getContent() ) );
                        } );
                        editor.nodeChanged();
                    }
                };
            };

            var current = ( target && getDefinition( getTagName( target ) ) ) || list[0];
            editor.windowManager.open( spec( current, initialData( current ) ) );
        };

        var openForSelection = function ( ui, element ) {
            // the command takes an optional element, e.g. from a click on the status bar path
            openDialog( getCustomTag( element && element.nodeType === 1 ? element : editor.selection.getNode() ) );
        };

        var removeCustomTag = function () {
            var el = getCustomTag( editor.selection.getNode() );
            if ( !el )
                return;
            editor.undoManager.transact( function () {
                // keep the content of block and inline tags, inline images have none
                editor.dom.remove( el, el.nodeName !== 'IMG' );
            } );
            editor.nodeChanged();
        };

        editor.addCommand( 'ezCustomTag', openForSelection );

        // ------------------------------------------------------------------ custom tag controls
        // Buttons on the focused custom tag to move it up or down, edit and remove it, keyboard shortcuts for the
        // same, Backspace in an empty block custom tag removes it, and a label with the name on every block tag.
        // Rebuilt from the custom_tags_controls plugin of LoveStack (commit 63334f1681, plugin
        // Copyright 2014, Mugo Web), with thanks to Mugo Web.
        var controls = editor.options.get( 'ez_custom_tag_controls' ) || {};

        function controlText( key, fallback ) {
            var texts = ( editor.options.get( 'ez_custom_tag_controls' ) || {} ).texts || {};
            return texts[key] || t( fallback );
        }

        // the block custom tag the selection is in (moving applies to block tags only)
        var getBlockCustomTag = function () {
            var el = getCustomTag( editor.selection.getNode() );
            return el && el.nodeName === 'DIV' && el.parentNode ? el : null;
        };

        var siblingBlock = function ( el, forward ) {
            var n = forward ? el.nextSibling : el.previousSibling;
            while ( n && n.nodeType !== 1 )
                n = forward ? n.nextSibling : n.previousSibling;
            return n;
        };

        var placeCaretIn = function ( el ) {
            editor.selection.select( el, true );
            editor.selection.collapse( true );
        };

        var moveCustomTag = function ( forward ) {
            var el = getBlockCustomTag(), other = el && siblingBlock( el, forward );
            if ( !other )
                return false;
            editor.undoManager.transact( function () {
                if ( forward )
                {
                    el.parentNode.insertBefore( other, el );
                    // keep a paragraph after a tag that became the last one, to go on writing after it
                    if ( !siblingBlock( el, true ) && el.parentNode === editor.getBody() )
                        editor.dom.insertAfter( editor.dom.create( 'p', {}, '<br data-mce-bogus="1">' ), el );
                }
                else
                    el.parentNode.insertBefore( el, other );
            } );
            placeCaretIn( el );
            editor.nodeChanged();
            return true;
        };

        // an empty block custom tag (no text, no image or embed in it) is replaced by an empty paragraph
        var removeEmptyCustomTag = function () {
            var el = getBlockCustomTag();
            if ( !el || ( el.textContent || '' ).replace( /[\s ​]+/g, '' ) !== '' || el.querySelector( 'img,table,[id^=eZObject_],[id^=eZNode_]' ) )
                return false;
            var p = editor.dom.create( 'p', {}, '<br data-mce-bogus="1">' );
            editor.undoManager.transact( function () {
                el.parentNode.replaceChild( p, el );
            } );
            placeCaretIn( p );
            editor.nodeChanged();
            return true;
        };

        if ( controls.enabled )
        {
            var canMove = function ( forward ) {
                return function ( api ) {
                    var handler = function () {
                        var el = getBlockCustomTag();
                        api.setEnabled( !!( el && siblingBlock( el, forward ) ) );
                    };
                    handler();
                    editor.on( 'NodeChange', handler );
                    return function () {
                        editor.off( 'NodeChange', handler );
                    };
                };
            };
            editor.ui.registry.addButton( 'ezcustomtagup', {
                icon: 'chevron-up',
                tooltip: controlText( 'up', 'Move the custom tag up' ) + ' (Alt+Shift+Up)',
                onAction: function () { moveCustomTag( false ); },
                onSetup: canMove( false )
            } );
            editor.ui.registry.addButton( 'ezcustomtagdown', {
                icon: 'chevron-down',
                tooltip: controlText( 'down', 'Move the custom tag down' ) + ' (Alt+Shift+Down)',
                onAction: function () { moveCustomTag( true ); },
                onSetup: canMove( true )
            } );

            editor.on( 'keydown', function ( e ) {
                if ( e.altKey && e.shiftKey && !e.ctrlKey && !e.metaKey )
                {
                    var done = false;
                    if ( e.key === 'ArrowUp' )
                        done = moveCustomTag( false );
                    else if ( e.key === 'ArrowDown' )
                        done = moveCustomTag( true );
                    else if ( ( e.key === 'E' || e.key === 'e' || e.code === 'KeyE' ) && getCustomTag( editor.selection.getNode() ) )
                    {
                        openForSelection();
                        done = true;
                    }
                    else if ( ( e.key === 'Delete' || e.key === 'Backspace' ) && getCustomTag( editor.selection.getNode() ) )
                    {
                        removeCustomTag();
                        done = true;
                    }
                    if ( done )
                        e.preventDefault();
                }
                else if ( e.key === 'Backspace' && !e.altKey && !e.ctrlKey && !e.metaKey && removeEmptyCustomTag() )
                    e.preventDefault();
            } );

            // labels: the name (CustomTagsDescription) of each block custom tag above it, from ezoe_customtags.css's
            // look; the rules are written into the editor's document so no site has to list its tags in a stylesheet
            editor.on( 'init', function () {
                var css = '';
                editor.options.get( 'ez_custom_tags' ).forEach( function ( d ) {
                    if ( !d.inline && /^[A-Za-z0-9_\-]+$/.test( d.name ) )
                        css += 'div.ezoeItemCustomTag.' + d.name + '::before { content: "' + String( d.title || d.name ).replace( /["\\\n\r]/g, ' ' ) + '"; }\n';
                } );
                editor.getBody().classList.add( 'ezoe-custom-tag-labels' );
                if ( css )
                    editor.dom.addStyle( css );
            } );
        }

        editor.ui.registry.addToggleButton( 'ezcustomtag', {
            icon: 'template',
            tooltip: t( 'Custom tag' ),
            onAction: openForSelection,
            onSetup: function ( api ) {
                var handler = function ( e ) {
                    api.setActive( !!getCustomTag( e.element ) );
                };
                editor.on( 'NodeChange', handler );
                return function () {
                    editor.off( 'NodeChange', handler );
                };
            }
        } );

        editor.ui.registry.addButton( 'ezcustomtagedit', {
            icon: 'edit-block',
            tooltip: t( 'Edit custom tag' ),
            onAction: openForSelection
        } );

        editor.ui.registry.addButton( 'ezcustomtagremove', {
            icon: 'remove',
            tooltip: t( 'Remove custom tag' ),
            onAction: removeCustomTag
        } );

        editor.ui.registry.addMenuItem( 'ezcustomtag', {
            icon: 'template',
            text: t( 'Custom tag' ) + '…',
            onAction: openForSelection
        } );

        editor.ui.registry.addContextToolbar( 'ezcustomtag', {
            predicate: function ( node ) {
                return !!getCustomTag( node ) && !editor.dom.getParent( node, '[id^=eZObject_],[id^=eZNode_]', editor.getBody() );
            },
            items: controls.enabled ? 'ezcustomtagup ezcustomtagdown | ezcustomtagedit ezcustomtagremove' : 'ezcustomtagedit ezcustomtagremove',
            position: 'node',
            scope: 'node'
        } );

        // inline image custom tags have no text to click into
        editor.on( 'dblclick', function ( e ) {
            var el = getCustomTag( e.target );
            if ( el && el.nodeName === 'IMG' )
            {
                e.preventDefault();
                openDialog( el );
            }
        } );
    } );
}());

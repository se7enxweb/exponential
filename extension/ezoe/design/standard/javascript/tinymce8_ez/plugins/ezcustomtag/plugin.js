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
            var list = definitions();
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
            items: 'ezcustomtagedit ezcustomtagremove',
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

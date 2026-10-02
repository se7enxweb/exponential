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
    var ATTRIBUTE_SEPARATOR = 'attribute_separation';
    var FIELD_PREFIX = 'attr_';

    tinymce.addI18n( 'de', {
        'Custom tag': 'Custom Tag',
        'Insert custom tag': 'Custom Tag einfügen',
        'Edit custom tag': 'Custom Tag bearbeiten',
        'Remove custom tag': 'Custom Tag entfernen',
        'Tag': 'Tag',
        'This tag has no attributes.': 'Dieses Tag hat keine Attribute.',
        'No custom tags are configured in content.ini [CustomTagSettings].': 'In content.ini [CustomTagSettings] sind keine Custom Tags eingerichtet.',
        'Please fill in: %s': 'Bitte ausfüllen: %s',
        'Please enter a whole number: %s': 'Bitte eine ganze Zahl eingeben: %s',
        'Please enter a number: %s': 'Bitte eine Zahl eingeben: %s',
        'Please enter an e-mail address: %s': 'Bitte eine E-Mail-Adresse eingeben: %s',
        'Value too small: %s': 'Wert zu klein: %s',
        'Value too large: %s': 'Wert zu groß: %s'
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

        var parseCustomAttributes = function ( value ) {
            var result = {};
            ( value || '' ).split( ATTRIBUTE_SEPARATOR ).forEach( function ( part ) {
                var pos = part.indexOf( '|' );
                if ( pos > 0 )
                    result[ part.slice( 0, pos ) ] = part.slice( pos + 1 );
            } );
            return result;
        };

        var serializeCustomAttributes = function ( values ) {
            return Object.keys( values ).map( function ( key ) {
                return key + '|' + values[key];
            } ).join( ATTRIBUTE_SEPARATOR );
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

        var escapeHtml = function ( value ) {
            return String( value ).replace( /&/g, '&amp;' ).replace( /"/g, '&quot;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' );
        };

        var fieldName = function ( attribute ) {
            return FIELD_PREFIX + attribute.id;
        };

        var buildField = function ( attribute ) {
            var label = attribute.name + ( attribute.required ? ' *' : '' ),
                field = { name: fieldName( attribute ), label: label, enabled: !attribute.disabled };

            switch ( attribute.type )
            {
                case 'select':
                    field.type = 'listbox';
                    field.items = Object.keys( attribute.selection || {} ).map( function ( key ) {
                        return { text: attribute.selection[key], value: key };
                    } );
                    if ( !field.items.length )
                        field.items = [ { text: '', value: '' } ];
                    break;
                case 'checkbox':
                    field.type = 'checkbox';
                    break;
                case 'textarea':
                    field.type = 'textarea';
                    field.maximized = false;
                    break;
                case 'color':
                    field.type = 'colorinput';
                    break;
                default:
                    // text, int, number, email, link and the css/html size types
                    field.type = 'input';
                    if ( attribute.title )
                        field.placeholder = attribute.title;
            }
            return field;
        };

        // Dialog value of an attribute from the stored string value
        var toFieldValue = function ( attribute, value ) {
            if ( attribute.type === 'checkbox' )
                return value !== undefined && value !== '' && value !== 'false';
            if ( attribute.type === 'select' && value === undefined )
            {
                var keys = Object.keys( attribute.selection || {} );
                return keys.indexOf( attribute['default'] ) !== -1 ? attribute['default'] : ( keys[0] || '' );
            }
            return value === undefined ? ( attribute['default'] || '' ) : value;
        };

        // Stored string value of an attribute from the dialog value, null means not set
        var fromFieldValue = function ( attribute, value ) {
            if ( attribute.type === 'checkbox' )
                return value ? ( String( attribute['default'] || '' ).trim() || '1' ) : null;
            value = String( value === undefined || value === null ? '' : value ).trim();
            return value === '' ? null : value;
        };

        // Validation like popup_validate.js of the TinyMCE 3 dialogs
        var validate = function ( attribute, value ) {
            var empty = value === null;
            if ( attribute.required && ( empty || ( attribute.type === 'select' && value === Object.keys( attribute.selection || {} )[0] ) ) )
                return t( 'Please fill in: %s', attribute.name );
            if ( empty )
                return ( attribute.type === 'int' || attribute.type === 'number' ) && !attribute.allowEmpty
                    ? t( 'Please fill in: %s', attribute.name ) : null;
            if ( attribute.type === 'int' && !/^-?\d+$/.test( value ) )
                return t( 'Please enter a whole number: %s', attribute.name );
            if ( attribute.type === 'number' && !/^-?\d+([.,]\d+)?$/.test( value ) )
                return t( 'Please enter a number: %s', attribute.name );
            if ( attribute.type === 'email' && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test( value ) )
                return t( 'Please enter an e-mail address: %s', attribute.name );
            if ( ( attribute.type === 'int' || attribute.type === 'number' ) )
            {
                var number = parseFloat( value.replace( ',', '.' ) );
                if ( attribute.minimum !== null && number < parseFloat( attribute.minimum ) )
                    return t( 'Value too small: %s', attribute.name );
                if ( attribute.maximum !== null && number > parseFloat( attribute.maximum ) )
                    return t( 'Value too large: %s', attribute.name );
            }
            return null;
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
                        { type: 'submit', text: t( 'Save' ), primary: true }
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

        var openForSelection = function () {
            openDialog( getCustomTag( editor.selection.getNode() ) );
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

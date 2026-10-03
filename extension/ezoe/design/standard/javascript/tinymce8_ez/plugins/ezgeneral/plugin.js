/**
 * eZ Online Editor - TinyMCE 8: general tag dialog
 *
 * Like the TinyMCE 3 ezoe dialogs tag_general.tpl and tag_header.tpl: class and custom attributes
 * of paragraphs, headings, lists, list items and strong / emphasize, from content.ini
 * [<tag>] AvailableClasses / ClassDescription / CustomAttributes and ezoe_attributes.ini.
 * The markup is the one eZOEXMLInput writes: class, customattributes and the inline styles of
 * ezoe.ini [EditorSettings] CustomAttributeStyleMap on the element.
 *
 * Licensed under the GNU General Public License v2.0, like the rest of ezoe.
 */
(function () {
    'use strict';

    var D = window.eZOe8Dialog;

    // html element → ezxml tag of the dialog, see __simpleTagsToXmlHash of the TinyMCE 3 ez theme
    var XML_TAGS = {
        P: 'paragraph', H1: 'header', H2: 'header', H3: 'header', H4: 'header', H5: 'header', H6: 'header',
        UL: 'ul', OL: 'ol', LI: 'li', STRONG: 'strong', B: 'strong', EM: 'emphasize', I: 'emphasize'
    };

    tinymce.addI18n( 'de', {
        'Edit <%s> tag': 'Tag <%s> bearbeiten',
        'Tag properties': 'Tag-Eigenschaften',
        '<%s> properties': 'Eigenschaften von <%s>',
        'Class': 'Klasse',
        'None': 'Keine',
        'This tag has no classes or custom attributes in content.ini.': 'Für dieses Tag sind in content.ini keine Klassen oder Custom-Attribute eingerichtet.'
    } );

    tinymce.PluginManager.add( 'ezgeneral', function ( editor ) {

        editor.options.register( 'ez_general_definitions', { processor: 'object', default: {} } );
        editor.options.register( 'ez_xml_tag_alias', { processor: 'object', default: {} } );
        editor.options.register( 'ez_custom_attribute_style_map', { processor: 'object', default: {} } );

        var t = function ( text, value ) {
            var s = editor.translate( text );
            return value === undefined ? s : s.replace( '%s', value );
        };

        var definition = function ( tag ) {
            var d = editor.options.get( 'ez_general_definitions' )[tag] || {};
            return { classes: Array.isArray( d.classes ) ? {} : ( d.classes || {} ), attributes: d.attributes || [] };
        };

        // friendly name of the tag like in the status bar path (ezoe.ini XmlTagNameAlias)
        var tagLabel = function ( tag ) {
            var alias = editor.options.get( 'ez_xml_tag_alias' );
            return alias[tag] !== undefined ? alias[tag] : tag;
        };

        // the closest element the dialog handles, starting at the given element or the selection
        var getElement = function ( element ) {
            var node = element && element.nodeType === 1 ? element : editor.selection.getNode();
            return editor.dom.getParent( node, function ( n ) {
                return n.nodeType === 1 && !!XML_TAGS[ n.nodeName ];
            }, editor.getBody() );
        };

        var elementClass = function ( el ) {
            return String( el.getAttribute( 'class' ) || '' ).replace( /\b(mce[\w-]*|ezoe\w*)\b/g, '' ).replace( /\s+/g, ' ' ).trim();
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

        var openDialog = function ( element ) {
            var el = getElement( element );
            if ( !el )
                return;
            var tag = XML_TAGS[ el.nodeName ],
                def = definition( tag ),
                stored = D.parseCustomAttributes( el.getAttribute( 'customattributes' ) ),
                current = elementClass( el ),
                data = { cssClass: current },
                classes = [ { text: t( 'None' ), value: '' } ],
                items = [ { type: 'htmlpanel', html: '<h2 class="ezoe-tag-title">' + D.escapeHtml( t( 'Edit <%s> tag', tagLabel( tag ) ) ) + '</h2>' } ];

            Object.keys( def.classes ).forEach( function ( key ) {
                classes.push( { text: def.classes[key], value: key } );
            } );
            if ( current && !def.classes[current] )
                classes.push( { text: current, value: current } );
            items.push( { type: 'listbox', name: 'cssClass', label: t( 'Class' ), items: classes } );

            def.attributes.forEach( function ( attribute ) {
                data[ D.attributeFieldName( attribute ) ] = D.toAttributeFieldValue( attribute, stored[attribute.id] );
                if ( attribute.type !== 'hidden' )
                    items.push( D.attributeField( attribute ) );
            } );
            if ( !Object.keys( def.classes ).length && !def.attributes.length && !current )
                items.push( { type: 'htmlpanel', html: '<p>' + D.escapeHtml( t( 'This tag has no classes or custom attributes in content.ini.' ) ) + '</p>' } );

            editor.windowManager.open( {
                title: t( '<%s> properties', tagLabel( tag ) ),
                size: 'normal',
                body: { type: 'panel', items: items },
                initialData: data,
                buttons: [
                    { type: 'cancel', text: t( 'Cancel' ) },
                    { type: 'submit', text: 'OK', primary: true }
                ],
                onSubmit: function ( api ) {
                    var d = api.getData(), values = {}, error = null, field = null;
                    def.attributes.forEach( function ( attribute ) {
                        var name = D.attributeFieldName( attribute ),
                            hidden = attribute.type === 'hidden',
                            value = D.fromAttributeFieldValue( attribute, hidden ? ( stored[attribute.id] !== undefined ? stored[attribute.id] : attribute['default'] ) : d[name] ),
                            message = hidden ? null : D.validateAttribute( attribute, value, t );
                        if ( message && !error )
                        {
                            error = message;
                            field = name;
                        }
                        if ( value !== null )
                            values[attribute.id] = value;
                    } );
                    if ( error )
                    {
                        editor.notificationManager.open( { text: error, type: 'warning', timeout: 4000 } );
                        api.focus( field );
                        return;
                    }
                    api.close();
                    editor.undoManager.transact( function () {
                        var dom = editor.dom, internal = String( el.getAttribute( 'class' ) || '' ).split( /\s+/ ).filter( function ( c ) {
                            return /^(mce|ezoe)/.test( c );
                        } );
                        dom.setAttrib( el, 'class', internal.concat( d.cssClass ? [ d.cssClass ] : [] ).join( ' ' ) || null );
                        dom.setAttrib( el, 'customattributes', D.serializeCustomAttributes( values ) || null );
                        // only the styles of mapped custom attributes are replaced, e.g. text-align stays
                        var mapped = Object.keys( editor.options.get( 'ez_custom_attribute_style_map' ) ).map( function ( key ) {
                                return editor.options.get( 'ez_custom_attribute_style_map' )[key];
                            } ),
                            kept = String( el.getAttribute( 'style' ) || '' ).split( ';' ).map( function ( part ) {
                                return part.trim();
                            } ).filter( function ( part ) {
                                return part && mapped.indexOf( part.split( ':' )[0].trim().toLowerCase() ) === -1;
                            } ),
                            style = ( kept.length ? kept.join( '; ' ) + '; ' : '' ) + buildStyle( values );
                        dom.setAttrib( el, 'style', style.trim() || null );
                    } );
                    editor.nodeChanged();
                }
            } );
        };

        // the command takes an optional element, e.g. from a click on the status bar path
        editor.addCommand( 'ezGeneral', function ( ui, element ) {
            openDialog( element );
        } );

        editor.ui.registry.addMenuItem( 'ezgeneral', {
            icon: 'edit-block',
            text: t( 'Tag properties' ) + '…',
            onAction: function () {
                openDialog();
            }
        } );

        // context menu entry for the element the cursor is in (paragraph, heading, list item, …)
        editor.ui.registry.addContextMenu( 'ezgeneral', {
            update: function ( node ) {
                var el = getElement( node );
                return el && !editor.dom.getParent( node, '[type=custom],pre,[id^=eZObject_],[id^=eZNode_]', editor.getBody() ) ? 'ezgeneral' : '';
            }
        } );
    } );
}());

/**
 * eZ Online Editor - TinyMCE 8 prototype: literal plugin
 *
 * The ezxml literal tag is a <pre> in the editor, as written by eZOEXMLInput::inputTagXML():
 *
 *   <pre class="html" customattributes="name|value">line 1<br>line 2</pre>
 *
 * eZOEInputParser::parsingHandlerLiteral() reads its content as text and turns <br> into line breaks.
 * The dialog has the class and the custom attributes of the TinyMCE 3 general tag dialog
 * (content.ini [literal] AvailableClasses / CustomAttributes) plus the text of the literal.
 * Like before a paragraph follows a new literal and pasting into a literal pastes plain text.
 *
 * Licensed under the GNU General Public License v2.0, like the rest of ezoe.
 */
(function () {
    'use strict';

    var D = window.eZOe8Dialog;

    tinymce.addI18n( 'de', {
        'Literal': 'Literal',
        'Insert literal': 'Literal einfügen',
        'Edit literal': 'Literal bearbeiten',
        'Remove literal': 'Literal entfernen',
        'Text': 'Text',
        'Class': 'Klasse',
        'None': 'Keine',
        'The text is stored as it is, without formatting (e.g. code).': 'Der Text wird unverändert ohne Formatierung gespeichert (z. B. Code).'
    } );

    tinymce.PluginManager.add( 'ezliteral', function ( editor ) {

        editor.options.register( 'ez_literal', { processor: 'object', default: { classes: {}, attributes: [] } } );

        var t = function ( text ) {
            return editor.translate( text );
        };

        var definition = function () {
            var d = editor.options.get( 'ez_literal' );
            return { classes: Array.isArray( d.classes ) ? {} : ( d.classes || {} ), attributes: d.attributes || [] };
        };

        var getLiteral = function ( node ) {
            return editor.dom.getParent( node, 'pre', editor.getBody() );
        };

        // Text of a literal: <br> are line breaks
        var literalText = function ( pre ) {
            var clone = pre.cloneNode( true );
            Array.prototype.forEach.call( clone.querySelectorAll( 'br' ), function ( br ) {
                br.parentNode.replaceChild( document.createTextNode( '\n' ), br );
            } );
            return clone.textContent.replace( / /g, ' ' );
        };

        // Html of the literal text, line breaks as <br> like eZOEXMLInput writes them
        var literalHtml = function ( text ) {
            return D.escapeHtml( text ).split( /\r?\n/ ).join( '<br>' ) || '<br>';
        };

        var classItems = function () {
            var classes = definition().classes, items = [ { text: t( 'None' ), value: '' } ];
            Object.keys( classes ).forEach( function ( key ) {
                items.push( { text: classes[key], value: key } );
            } );
            return items;
        };

        var openDialog = function ( target ) {
            var def = definition(),
                stored = target ? D.parseCustomAttributes( target.getAttribute( 'customattributes' ) ) : {},
                data = {
                    text: target ? literalText( target ) : ( editor.selection.isCollapsed() ? '' : editor.selection.getContent( { format: 'text' } ) ),
                    cssClass: target ? String( target.getAttribute( 'class' ) || '' ).replace( /\b(mce|ezoe)\w*\b/g, '' ).trim() : ''
                },
                items = [
                    { type: 'textarea', name: 'text', label: t( 'Text' ), placeholder: t( 'The text is stored as it is, without formatting (e.g. code).' ) }
                ];

            if ( Object.keys( def.classes ).length )
                items.push( { type: 'listbox', name: 'cssClass', label: t( 'Class' ), items: classItems() } );

            def.attributes.forEach( function ( attribute ) {
                data[ D.attributeFieldName( attribute ) ] = D.toAttributeFieldValue( attribute, stored[attribute.id] );
                if ( attribute.type !== 'hidden' )
                    items.push( D.attributeField( attribute ) );
            } );

            editor.windowManager.open( {
                title: target ? t( 'Edit literal' ) : t( 'Insert literal' ),
                size: 'medium',
                body: { type: 'panel', items: items },
                initialData: data,
                buttons: [
                    { type: 'cancel', text: t( 'Cancel' ) },
                    { type: 'submit', text: 'OK', primary: true }
                ],
                onSubmit: function ( api ) {
                    var d = api.getData(), values = {}, error = null, errorField = null;

                    def.attributes.forEach( function ( attribute ) {
                        var hidden = attribute.type === 'hidden',
                            value = D.fromAttributeFieldValue( attribute, hidden ? ( stored[attribute.id] !== undefined ? stored[attribute.id] : attribute['default'] ) : d[ D.attributeFieldName( attribute ) ] ),
                            message = hidden ? null : D.validateAttribute( attribute, value, t );
                        if ( message && !error )
                        {
                            error = message;
                            errorField = D.attributeFieldName( attribute );
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
                    apply( target, d.text || '', d.cssClass || '', D.serializeCustomAttributes( values ) );
                }
            } );
        };

        var apply = function ( target, text, cssClass, customAttributes ) {
            var dom = editor.dom;
            editor.undoManager.transact( function () {
                if ( target )
                {
                    target.innerHTML = literalHtml( text );
                    dom.setAttrib( target, 'class', cssClass || null );
                    dom.setAttrib( target, 'customattributes', customAttributes || null );
                    return;
                }

                // the new literal replaces the selection, a paragraph follows it (see http://issues.ez.no/18209);
                // pre has no id in valid_elements, the new literal is the one the cursor is in afterwards
                editor.insertContent( '<pre' +
                                      ( cssClass ? ' class="' + D.escapeHtml( cssClass ) + '"' : '' ) +
                                      ( customAttributes ? ' customattributes="' + D.escapeHtml( customAttributes ) + '"' : '' ) +
                                      '>' + literalHtml( text ) + '</pre>' );
                var pre = getLiteral( editor.selection.getNode() );
                if ( pre )
                {
                    if ( !pre.nextSibling || pre.nextSibling.nodeName !== 'P' )
                        dom.insertAfter( dom.create( 'p', {}, '<br>' ), pre );
                    editor.selection.setCursorLocation( pre.nextSibling, 0 );
                }
            } );
            editor.nodeChanged();
        };

        var removeLiteral = function () {
            var pre = getLiteral( editor.selection.getNode() );
            if ( !pre )
                return;
            editor.undoManager.transact( function () {
                // the text stays as paragraph
                var p = editor.dom.create( 'p', {}, literalHtml( literalText( pre ) ) );
                editor.dom.replace( p, pre );
                editor.selection.setCursorLocation( p, 0 );
            } );
            editor.nodeChanged();
        };

        // the command takes an optional element, e.g. from a click on the status bar path
        editor.addCommand( 'ezLiteral', function ( ui, element ) {
            openDialog( getLiteral( element && element.nodeType === 1 ? element : editor.selection.getNode() ) );
        } );

        var openForSelection = function () {
            editor.execCommand( 'ezLiteral' );
        };

        editor.ui.registry.addToggleButton( 'ezliteral', {
            icon: 'sourcecode',
            tooltip: t( 'Literal' ),
            onAction: openForSelection,
            onSetup: function ( api ) {
                var handler = function ( e ) {
                    api.setActive( !!getLiteral( e.element ) );
                };
                editor.on( 'NodeChange', handler );
                return function () {
                    editor.off( 'NodeChange', handler );
                };
            }
        } );

        editor.ui.registry.addButton( 'ezliteraledit', {
            icon: 'edit-block',
            tooltip: t( 'Edit literal' ),
            onAction: openForSelection
        } );

        editor.ui.registry.addButton( 'ezliteralremove', {
            icon: 'remove',
            tooltip: t( 'Remove literal' ),
            onAction: removeLiteral
        } );

        editor.ui.registry.addMenuItem( 'ezliteral', {
            icon: 'sourcecode',
            text: t( 'Literal' ) + '…',
            onAction: openForSelection
        } );

        editor.ui.registry.addContextToolbar( 'ezliteral', {
            predicate: function ( node ) {
                return !!getLiteral( node );
            },
            items: 'ezliteraledit ezliteralremove',
            position: 'node',
            scope: 'node'
        } );

        // Pasting into a literal pastes plain text, like the TinyMCE 3 ez theme (pasteAsPlainText)
        editor.on( 'PastePreProcess', function ( e ) {
            if ( !getLiteral( editor.selection.getNode() ) )
                return;
            var div = document.createElement( 'div' );
            div.innerHTML = e.content.replace( /<br\s*\/?>/gi, '\n' ).replace( /<\/(p|div|h[1-6]|li|tr)>/gi, '\n' );
            e.content = literalHtml( div.textContent.replace( /\n$/, '' ) );
        } );
    } );
}());

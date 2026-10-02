/**
 * eZ Online Editor - TinyMCE 8 prototype: ezxml tag names in the element path of the status bar
 *
 * Like the TinyMCE 3 ez theme (_updatePath / __tagsToXml): the path shows the ezxml tags instead
 * of the html elements, e.g. "paragraph » embed.right" or "table » table row » table cell",
 * using the friendly names of ezoe.ini [EditorSettings] XmlTagNameAlias and ".class" suffixes.
 * With ezoe.ini [EditorSettings] TagPathOpenDialog=enabled a click on a path item opens the dialog
 * of the tag like before (embed, custom tag, link, anchor, table, table row, table cell).
 *
 * Licensed under the GNU General Public License v2.0, like the rest of ezoe.
 */
(function () {
    'use strict';

    var SIMPLE_TAGS = {
        P: 'paragraph', I: 'emphasize', EM: 'emphasize', B: 'strong', STRONG: 'strong', PRE: 'literal',
        U: 'custom', SUB: 'custom', SUP: 'custom',
        H1: 'header', H2: 'header', H3: 'header', H4: 'header', H5: 'header', H6: 'header',
        TABLE: 'table', TH: 'th', TD: 'td', TR: 'tr', UL: 'ul', OL: 'ol', LI: 'li'
    };

    // Internal classes that are not shown, see the TinyMCE 3 ez theme
    var INTERNAL_CLASS_REGEX = /\b\s*(webkit|mce|Apple-|ezoeItem|ezoeAlign)[\w-]*\s*\b/g;

    tinymce.addI18n( 'de', {
        'Path': 'Pfad'
    } );

    tinymce.PluginManager.add( 'ezpath', function ( editor ) {

        editor.options.register( 'ez_xml_tag_alias', { processor: 'object', default: {} } );
        editor.options.register( 'ez_path_open_dialog', { processor: 'boolean', default: false } );

        var isEmbed = function ( n ) {
            return ( n.getAttribute( 'class' ) || '' ).indexOf( 'ezoeItemNonEditable' ) !== -1;
        };

        var embedName = function ( n ) {
            return 'embed' + ( n.getAttribute( 'inline' ) === 'true' ? '-inline' : '' );
        };

        // ezxml tag name of an element, see __tagsToXml() of the TinyMCE 3 ez theme
        var xmlTagName = function ( n ) {
            if ( SIMPLE_TAGS[ n.nodeName ] )
                return SIMPLE_TAGS[ n.nodeName ];
            switch ( n.nodeName )
            {
                case 'A':
                    return n.getAttribute( 'href' ) ? 'link' : 'anchor';
                case 'DIV':
                    if ( isEmbed( n ) )
                        return embedName( n );
                    if ( n.getAttribute( 'type' ) === 'custom' )
                        return 'custom';
                    break;
                case 'SPAN':
                    if ( isEmbed( n ) )
                        return embedName( n );
                    if ( n.getAttribute( 'type' ) === 'custom' || n.style.textDecoration === 'underline' )
                        return 'custom';
                    break;
                case 'IMG':
                    return n.getAttribute( 'type' ) === 'custom' ? 'custom' : embedName( n );
            }
            return null;
        };

        editor.on( 'ResolveName', function ( e ) {
            var n = e.target, name = xmlTagName( n ), alias = editor.options.get( 'ez_xml_tag_alias' ), label, className;

            // table sections have no ezxml counterpart
            if ( /^(TBODY|THEAD|TFOOT)$/.test( n.nodeName ) )
            {
                e.preventDefault();
                return;
            }
            if ( !name )
                return;

            label = alias[name] !== undefined ? alias[name] : name;

            if ( name === 'header' )
                label += ' ' + n.nodeName.charAt( 1 );
            else if ( name === 'anchor' && n.getAttribute( 'name' ) )
                label += '#' + n.getAttribute( 'name' );

            // custom tags with a native element have their name as class
            if ( name === 'custom' && /^(U|SUB|SUP)$/.test( n.nodeName ) )
                className = { U: 'underline', SUB: 'sub', SUP: 'sup' }[ n.nodeName ];
            else if ( name === 'custom' && n.nodeName === 'SPAN' && n.style.textDecoration === 'underline' )
                className = 'underline';
            else
                className = String( n.getAttribute( 'class' ) || '' ).replace( INTERNAL_CLASS_REGEX, ' ' ).trim();

            if ( className )
                label += '.' + className.split( /\s+/ ).join( '.' );

            e.name = label;
        } );

        // Element selected by a click on the path: for a whole selected element getNode() returns its parent
        var selectedElement = function () {
            var rng = editor.selection.getRng(), container = rng.startContainer;
            if ( !rng.collapsed && container === rng.endContainer && container.nodeType === 1 && rng.endOffset - rng.startOffset === 1 )
                return container.childNodes[ rng.startOffset ];
            return editor.selection.getNode();
        };

        // Editor command for the dialog of an element, see __getTagCommand() of the TinyMCE 3 ez theme
        var dialogCommand = function ( n ) {
            switch ( xmlTagName( n ) )
            {
                case 'embed':
                case 'embed-inline':
                    return 'ezEmbed';
                case 'custom':
                    // custom tags with a native element (underline, sub, sup) have no attributes to edit
                    return n.getAttribute( 'type' ) === 'custom' ? 'ezCustomTag' : null;
                case 'link':
                    return 'ezLink';
                case 'anchor':
                    return 'mceAnchor';
                case 'table':
                    return 'mceTableProps';
                case 'tr':
                    return 'mceTableRowProps';
                case 'td':
                case 'th':
                    return 'mceTableCellProps';
            }
            return null;
        };

        editor.on( 'init', function () {
            var statusbar = editor.getContainer().querySelector( '.tox-statusbar' );
            if ( !statusbar )
                return;

            // "Path:" in front of the element path like the old status bar
            statusbar.style.setProperty( '--ezoe-path-label', JSON.stringify( editor.translate( 'Path' ) + ': ' ) );

            if ( !editor.options.get( 'ez_path_open_dialog' ) )
                return;

            // TinyMCE selects the element of a clicked path item, the dialog is opened afterwards
            statusbar.addEventListener( 'click', function ( e ) {
                if ( !e.target.closest( '.tox-statusbar__path-item' ) )
                    return;
                setTimeout( function () {
                    var node = selectedElement(), command = node && dialogCommand( node );
                    if ( !command )
                        return;
                    // the table row dialog works on the cell the cursor is in
                    if ( command === 'mceTableRowProps' && node.cells && node.cells[0] )
                        editor.selection.setCursorLocation( node.cells[0], 0 );
                    // the ez dialogs take the element, the TinyMCE ones use the selection
                    editor.execCommand( command, false, /^ez/.test( command ) ? node : undefined );
                }, 0 );
            } );
        } );
    } );
}());

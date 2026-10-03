/**
 * eZ Online Editor - TinyMCE 8: table, row and cell dialogs
 *
 * Like the TinyMCE 3 ezoe dialogs tag_table.tpl, tag_table_cell.tpl and tag_general.tpl (tr):
 *   table: size (new tables), width, border, class and custom attributes (content.ini [table])
 *   cell:  td / th, width, class and custom attributes of the tag, apply to cell, column or row
 *   row:   class and custom attributes (content.ini [tr])
 * The markup is the one eZOEXMLInput writes: class, width, border and customattributes on the
 * elements, eZOEInputParser maps border to ezborder and the cell width to xhtml:width.
 *
 * Licensed under the GNU General Public License v2.0, like the rest of ezoe.
 */
(function () {
    'use strict';

    var D = window.eZOe8Dialog;
    var SIZE_REGEX = /^\d+%?$/;

    tinymce.addI18n( 'de', {
        'Table': 'Tabelle',
        'Insert table': 'Tabelle einfügen',
        'New <table> tag': 'Neues Tag <table>',
        'Edit <table> tag': 'Tag <table> bearbeiten',
        'Edit <td> tag': 'Tag <td> bearbeiten',
        'Edit <th> tag': 'Tag <th> bearbeiten',
        'Edit <tr> tag': 'Tag <tr> bearbeiten',
        'Table properties': 'Tabelleneigenschaften',
        'Cell properties': 'Zelleneigenschaften',
        'Row properties': 'Zeileneigenschaften',
        'Rows': 'Zeilen',
        'Columns': 'Spalten',
        'Width': 'Breite',
        'Border': 'Rahmen',
        'Class': 'Klasse',
        'None': 'Keine',
        'Tag': 'Tag',
        'Table cell': 'Tabellenzelle',
        'Table header': 'Tabellenkopf',
        'Apply to': 'Anwenden auf',
        'Cell': 'Zelle',
        'Column': 'Spalte',
        'Row': 'Zeile',
        'Please enter a size in pixel or percent, e.g. 200 or 100%: %s': 'Bitte eine Größe in Pixel oder Prozent eingeben, z. B. 200 oder 100%: %s',
        'Please enter a number from 1 to 100: %s': 'Bitte eine Zahl von 1 bis 100 eingeben: %s'
    } );

    tinymce.PluginManager.add( 'eztable', function ( editor ) {

        editor.options.register( 'ez_table_definitions', { processor: 'object', default: {} } );
        editor.options.register( 'ez_custom_attribute_style_map', { processor: 'object', default: {} } );
        // ezoe.ini [Engine_tinymce8] TableDialog: classic (the TinyMCE 3 dialog design) or modern
        editor.options.register( 'ez_table_dialog', { processor: 'string', default: 'classic' } );
        // texts of the classic dialog, translated by the server like the TinyMCE 3 dialog
        editor.options.register( 'ez_table_classic_texts', { processor: 'object', default: {} } );

        var t = function ( text ) {
            return editor.translate( text );
        };

        // classes, custom attributes (and defaults for table) of table, tr, td and th
        var definition = function ( tag ) {
            var d = editor.options.get( 'ez_table_definitions' )[tag] || {};
            return {
                classes: Array.isArray( d.classes ) ? {} : ( d.classes || {} ),
                attributes: d.attributes || [],
                defaults: Array.isArray( d.defaults ) ? {} : ( d.defaults || {} )
            };
        };

        var getParent = function ( node, selector ) {
            return node ? editor.dom.getParent( node, selector, editor.getBody() ) : null;
        };

        var elementOrSelection = function ( element ) {
            return element && element.nodeType === 1 ? element : editor.selection.getNode();
        };

        var classItems = function ( def, current ) {
            var items = [ { text: t( 'None' ), value: '' } ];
            Object.keys( def.classes ).forEach( function ( key ) {
                items.push( { text: def.classes[key], value: key } );
            } );
            if ( current && !def.classes[current] )
                items.push( { text: current, value: current } );
            return items;
        };

        // class of an element without the internal TinyMCE / ezoe classes
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

        // dialog fields and initial data for the custom attributes of a tag
        var attributeFields = function ( def, stored, data ) {
            var items = [];
            def.attributes.forEach( function ( attribute ) {
                data[ D.attributeFieldName( attribute ) ] = D.toAttributeFieldValue( attribute, stored[attribute.id] );
                if ( attribute.type !== 'hidden' )
                    items.push( D.attributeField( attribute ) );
            } );
            return items;
        };

        // custom attribute values from the dialog data, validated like the old dialogs
        var collectAttributes = function ( def, d, stored ) {
            var values = {}, result = { values: values, error: null, field: null };
            def.attributes.forEach( function ( attribute ) {
                var name = D.attributeFieldName( attribute ),
                    hidden = attribute.type === 'hidden',
                    value = D.fromAttributeFieldValue( attribute, hidden ? ( stored[attribute.id] !== undefined ? stored[attribute.id] : attribute['default'] ) : d[name] ),
                    message = hidden ? null : D.validateAttribute( attribute, value, t );
                if ( message && !result.error )
                {
                    result.error = message;
                    result.field = name;
                }
                if ( value !== null )
                    values[attribute.id] = value;
            } );
            return result;
        };

        var sizeError = function ( label, value ) {
            return value && !SIZE_REGEX.test( value ) ? t( 'Please enter a size in pixel or percent, e.g. 200 or 100%: %s' ).replace( '%s', label ) : null;
        };

        var warn = function ( api, text, field ) {
            editor.notificationManager.open( { text: text, type: 'warning', timeout: 4000 } );
            if ( field )
            {
                // message under the field as well, the toast alone is easy to miss
                D.showFieldError( field, text );
                api.focus( field );
            }
        };

        // sets class, custom attributes (with mapped styles) and further attributes on an element
        var applyAttributes = function ( el, cssClass, customValues, extra ) {
            var dom = editor.dom, internal = String( el.getAttribute( 'class' ) || '' ).split( /\s+/ ).filter( function ( c ) {
                return /^(mce|ezoe)/.test( c );
            } );
            dom.setAttrib( el, 'class', internal.concat( cssClass ? [ cssClass ] : [] ).join( ' ' ) || null );
            dom.setAttrib( el, 'customattributes', D.serializeCustomAttributes( customValues ) || null );
            dom.setAttrib( el, 'style', buildStyle( customValues ) || null );
            Object.keys( extra || {} ).forEach( function ( key ) {
                dom.setAttrib( el, key, extra[key] === '' ? null : extra[key] );
            } );
        };

        // ---- table

        // An empty required summary is taken from the caption, the stored markup is the same as typing it twice
        var summaryFromCaption = function ( def, d ) {
            def.attributes.forEach( function ( a ) {
                if ( a.id === 'summary' && a.required && !String( d[ D.attributeFieldName( a ) ] || '' ).trim() )
                {
                    var captionAttr = def.attributes.filter( function ( c ) { return c.id === 'caption'; } )[0];
                    if ( captionAttr && String( d[ D.attributeFieldName( captionAttr ) ] || '' ).trim() )
                        d[ D.attributeFieldName( a ) ] = String( d[ D.attributeFieldName( captionAttr ) ] ).trim();
                }
            } );
        };

        // Inserts a new table (table is null) or updates the given one, like tagGenerator / tagAttributeEditor of tag_table.tpl
        var commitTable = function ( table, d, custom, rows, cols ) {
            editor.undoManager.transact( function () {
                var el = table;
                if ( !el )
                {
                    // like tagGenerator of tag_table.tpl, the new table is found by a temporary class
                    var marker = 'ezoeItemNewTable' + Date.now(), html = '<table class="' + marker + '"><tbody>';
                    for ( var y = 0; y < rows; y++ )
                    {
                        html += '<tr>';
                        for ( var x = 0; x < cols; x++ )
                            html += '<td><br></td>';
                        html += '</tr>';
                    }
                    editor.insertContent( html + '</tbody></table>' );
                    el = editor.getBody().querySelector( 'table.' + marker );
                    if ( !el )
                        return;
                    editor.dom.removeClass( el, marker );
                    editor.selection.setCursorLocation( el.querySelector( 'td' ), 0 );
                }
                applyAttributes( el, d.cssClass, custom.values, { width: d.width.trim(), border: d.border.trim() } );
            } );
            editor.nodeChanged();
        };

        // ---- table, classic dialog: the design of the TinyMCE 3 dialog (tag_table.tpl), same fields, order and texts

        var openClassicTableDialog = function ( table, def, stored, data ) {
            var x = editor.options.get( 'ez_table_classic_texts' ) || {}, E = D.escapeHtml,
                tx = function ( key, fallback ) { return E( x[key] || t( fallback ) ); },
                row = function ( label, title, field ) {
                    return '<tr><td class="column1"><label>' + label + '</label></td><td' + ( title ? ' title="' + E( title ) + '"' : '' ) + '>' + field + '</td></tr>';
                },
                sizeParts = function ( value ) {
                    value = String( value || '' );
                    return { num: ( value.match( /^\d+/ ) || [ '' ] )[0], pct: value.indexOf( '%' ) !== -1 };
                },
                unitSelect = function ( name, pct ) {
                    return '<select data-ezoe-unit="' + name + '"><option value="">px</option><option value="%"' + ( pct ? ' selected' : '' ) + '>%</option></select>';
                },
                w = sizeParts( data.width ), b = sizeParts( data.border ), html = '', gridCells = '', i, j;

            html += '<div class="ezoe-classic tag-view tag-type-table">';
            html += '<div class="ezoe-classic-tabs"><ul><li class="current"><span><a href="#" tabindex="-1">' + tx( 'properties', 'Properties' ) + '</a></span></li></ul></div>';
            html += '<div class="ezoe-classic-panel"><h2>' + E( table ? t( 'Edit <table> tag' ) : t( 'New <table> tag' ) ) + '</h2>';
            if ( !table )
            {
                for ( i = 0; i < 5; i++ )
                {
                    gridCells += '<tr>';
                    for ( j = 0; j < 6; j++ )
                        gridCells += '<td><div></div></td>';
                    gridCells += '</tr>';
                }
                html += '<table class="properties" title="' + tx( 'size_title', 'Click to select table size' ) + '"><tr><td class="column1">' + tx( 'size', 'Size' ) + ':</td><td>' +
                    '<table class="ezoe-classic-grid">' + gridCells + '</table>' +
                    tx( 'columns', 'Columns' ) + ': <input type="text" size="3" maxlength="2" data-mce-name="cols" data-ezoe-field="cols" value="' + E( data.cols ) + '" /> ' +
                    tx( 'rows', 'Rows' ) + ': <input type="text" size="3" maxlength="3" data-mce-name="rows" data-ezoe-field="rows" value="' + E( data.rows ) + '" />' +
                    '</td></tr></table>';
            }
            html += '<table class="properties general_attributes">';
            html += row( tx( 'width', 'Width' ), x.width_title || '', '<input type="text" size="3" data-mce-name="width" data-ezoe-field="width" value="' + E( w.num ) + '" /> ' + unitSelect( 'width', w.pct ) );
            html += row( tx( 'border', 'Border' ), '', '<input type="text" size="3" data-mce-name="border" data-ezoe-field="border" value="' + E( b.num ) + '" /> ' + unitSelect( 'border', b.pct ) );
            html += row( tx( 'class', 'Class' ), x.class_title || '', '<select data-mce-name="cssClass" data-ezoe-field="cssClass">' + classItems( def, data.cssClass ).map( function ( c ) {
                return '<option value="' + E( c.value ) + '"' + ( c.value === data.cssClass ? ' selected' : '' ) + '>' + E( c.text ) + '</option>';
            } ).join( '' ) + '</select>' );
            html += '</table><table class="properties custom_attributes">';
            def.attributes.forEach( function ( a ) {
                var name = D.attributeFieldName( a ), value = data[name], field;
                if ( a.type === 'hidden' )
                    return;
                if ( a.type === 'select' )
                    field = '<select data-mce-name="' + name + '" data-ezoe-field="' + name + '"' + ( a.disabled ? ' disabled' : '' ) + '>' + Object.keys( a.selection || {} ).map( function ( key ) {
                        var v = key === '-0-' ? '' : key;
                        return '<option value="' + E( v ) + '"' + ( v === value ? ' selected' : '' ) + '>' + E( a.selection[key] ) + '</option>';
                    } ).join( '' ) + '</select>';
                else if ( a.type === 'checkbox' )
                    field = '<input type="checkbox" data-mce-name="' + name + '" data-ezoe-field="' + name + '"' + ( value ? ' checked' : '' ) + ( a.disabled ? ' disabled' : '' ) + ' />';
                else if ( a.type === 'textarea' )
                    field = '<textarea rows="3" cols="30" data-mce-name="' + name + '" data-ezoe-field="' + name + '"' + ( a.disabled ? ' disabled' : '' ) + '>' + E( value ) + '</textarea>';
                else
                    field = '<input type="text" data-mce-name="' + name + '" data-ezoe-field="' + name + '" value="' + E( value ) + '"' + ( a.disabled ? ' disabled' : '' ) + ' />';
                html += row( E( a.name ), a.title || '', field );
            } );
            html += '</table><div class="block"><button type="button" class="ezoe-classic-ok" data-ezoe-classic="ok">' + tx( 'ok', 'OK' ) + '</button> ' +
                    '<button type="button" class="ezoe-classic-cancel" data-ezoe-classic="cancel">' + tx( 'cancel', 'Cancel' ) + '</button></div>';
            html += '</div></div>';

            var api = editor.windowManager.open( {
                title: table ? t( 'Edit <table> tag' ) : t( 'New <table> tag' ),
                size: 'normal',
                body: { type: 'panel', items: [ { type: 'htmlpanel', html: html } ] },
                buttons: []
            } );

            var read = function ( root, name ) {
                var el = root.querySelector( '[data-ezoe-field="' + name + '"]' );
                return !el ? '' : ( el.type === 'checkbox' ? el.checked : el.value );
            };
            var unit = function ( root, name ) {
                var sel = root.querySelector( '[data-ezoe-unit="' + name + '"]' );
                return sel ? sel.value : '';
            };
            var warn2 = function ( root, text, field ) {
                editor.notificationManager.open( { text: text, type: 'warning', timeout: 4000 } );
                if ( field )
                {
                    D.showFieldError( field, text );
                    var el = root.querySelector( '[data-ezoe-field="' + field + '"]' );
                    if ( el )
                        el.focus();
                }
            };

            var submit = function ( root ) {
                var d = {}, custom, rows, cols, error, width, border;
                def.attributes.forEach( function ( a ) {
                    var name = D.attributeFieldName( a );
                    d[name] = a.type === 'hidden' ? undefined : read( root, name );
                } );
                summaryFromCaption( def, d );
                custom = collectAttributes( def, d, stored );
                if ( !table )
                {
                    rows = parseInt( read( root, 'rows' ), 10 );
                    cols = parseInt( read( root, 'cols' ), 10 );
                    if ( !( rows >= 1 && rows <= 100 ) )
                        return warn2( root, t( 'Please enter a number from 1 to 100: %s' ).replace( '%s', tx( 'rows', 'Rows' ) ), 'rows' );
                    if ( !( cols >= 1 && cols <= 100 ) )
                        return warn2( root, t( 'Please enter a number from 1 to 100: %s' ).replace( '%s', tx( 'columns', 'Columns' ) ), 'cols' );
                }
                width = String( read( root, 'width' ) ).trim();
                border = String( read( root, 'border' ) ).trim();
                if ( width && !/^\d+$/.test( width ) )
                    return warn2( root, t( 'Please enter a whole number: %s' ).replace( '%s', tx( 'width', 'Width' ) ), 'width' );
                if ( border && !/^\d+$/.test( border ) )
                    return warn2( root, t( 'Please enter a whole number: %s' ).replace( '%s', tx( 'border', 'Border' ) ), 'border' );
                if ( custom.error )
                    return warn2( root, custom.error, custom.field );
                // htmlsize.tpl: the unit is appended, a border of 0 stays 0 so the dotted visual border shows
                width = width ? width + unit( root, 'width' ) : '';
                border = border === '0' || !border ? border : border + unit( root, 'border' );
                api.close();
                commitTable( table, { width: width, border: border, cssClass: read( root, 'cssClass' ) }, custom, rows, cols );
            };

            // the htmlpanel has no event binding of its own: find its root and bind the grid, the buttons and Enter
            var tries = 0, bind = function () {
                var roots = document.querySelectorAll( '.tox-dialog .ezoe-classic' ), root = roots[ roots.length - 1 ];
                if ( !root )
                {
                    if ( tries++ < 100 )
                        setTimeout( bind, 20 );
                    return;
                }
                var cells = Array.prototype.slice.call( root.querySelectorAll( '.ezoe-classic-grid td div' ) ),
                    grid = root.querySelector( '.ezoe-classic-grid' ),
                    colsInput = root.querySelector( '[data-ezoe-field="cols"]' ), rowsInput = root.querySelector( '[data-ezoe-field="rows"]' ),
                    // like tableSizeGridShowChange() of tag_table.tpl: border on hover, background when saved
                    show = function ( rows, cols, save ) {
                        cells.forEach( function ( el, i ) {
                            var r = Math.floor( i / 6 ) + 1, c = i + 1 - ( r - 1 ) * 6, on = r <= rows && c <= cols;
                            if ( save )
                                el.style.backgroundColor = on ? '#cccccc' : '#fff';
                            else
                                el.style.borderColor = on ? '#aaa' : '#fff';
                        } );
                    },
                    fromInput = function () { show( colsInput && rowsInput ? rowsInput.value : 0, colsInput ? colsInput.value : 0, true ); };
                if ( grid )
                {
                    cells.forEach( function ( el, i ) {
                        var r = Math.floor( i / 6 ) + 1, c = i + 1 - ( r - 1 ) * 6;
                        el.addEventListener( 'mouseover', function () { show( r, c, false ); } );
                        el.addEventListener( 'click', function () {
                            show( r, c, true );
                            rowsInput.value = r;
                            colsInput.value = c;
                        } );
                    } );
                    grid.addEventListener( 'mouseout', function () { show( 0, -1, false ); } );
                    colsInput.addEventListener( 'keyup', fromInput );
                    rowsInput.addEventListener( 'keyup', fromInput );
                    fromInput();
                }
                root.addEventListener( 'click', function ( e ) {
                    var button = e.target.closest( '[data-ezoe-classic]' );
                    if ( !button )
                        return;
                    e.preventDefault();
                    if ( button.getAttribute( 'data-ezoe-classic' ) === 'ok' )
                        submit( root );
                    else
                        api.close();
                } );
                root.addEventListener( 'keydown', function ( e ) {
                    if ( e.key === 'Enter' && e.target.tagName === 'INPUT' )
                    {
                        e.preventDefault();
                        submit( root );
                    }
                } );
                var first = root.querySelector( 'input[type=text]' );
                if ( first )
                    first.focus();
            };
            bind();
        };

        // ---- table

        var openTableDialog = function ( element ) {
            var table = getParent( elementOrSelection( element ), 'table' ),
                def = definition( 'table' ),
                stored = table ? D.parseCustomAttributes( table.getAttribute( 'customattributes' ) ) : {},
                data = table ? {
                    width: table.getAttribute( 'width' ) || '',
                    border: table.getAttribute( 'border' ) || '',
                    cssClass: elementClass( table )
                } : {
                    rows: String( def.defaults.rows || 2 ),
                    cols: String( def.defaults.cols || 2 ),
                    width: def.defaults.width || '',
                    border: def.defaults.border !== undefined ? String( def.defaults.border ) : '',
                    cssClass: def.defaults['class'] || ''
                },
                items = [ { type: 'htmlpanel', html: '<h2 class="ezoe-tag-title">' + D.escapeHtml( table ? t( 'Edit <table> tag' ) : t( 'New <table> tag' ) ) + '</h2>' } ];

            if ( editor.options.get( 'ez_table_dialog' ) !== 'modern' )
            {
                // data of the custom attributes, as attributeFields() does for the modern dialog
                def.attributes.forEach( function ( attribute ) {
                    data[ D.attributeFieldName( attribute ) ] = D.toAttributeFieldValue( attribute, stored[attribute.id] );
                } );
                return openClassicTableDialog( table, def, stored, data );
            }

            if ( !table )
                items.push( { type: 'grid', columns: 2, items: [
                    { type: 'input', name: 'rows', label: t( 'Rows' ) },
                    { type: 'input', name: 'cols', label: t( 'Columns' ) }
                ] } );
            items.push(
                { type: 'input', name: 'width', label: t( 'Width' ), placeholder: '100%' },
                { type: 'input', name: 'border', label: t( 'Border' ), placeholder: '0' },
                { type: 'listbox', name: 'cssClass', label: t( 'Class' ), items: classItems( def, data.cssClass ) }
            );
            items = items.concat( attributeFields( def, stored, data ) );

            editor.windowManager.open( {
                title: table ? t( 'Table properties' ) : t( 'Insert table' ),
                size: 'normal',
                body: { type: 'panel', items: items },
                initialData: data,
                buttons: [
                    { type: 'cancel', text: t( 'Cancel' ) },
                    { type: 'submit', text: 'OK', primary: true }
                ],
                onSubmit: function ( api ) {
                    var d = api.getData(), custom, rows, cols, error;

                    summaryFromCaption( def, d );
                    custom = collectAttributes( def, d, stored );

                    if ( !table )
                    {
                        rows = parseInt( d.rows, 10 );
                        cols = parseInt( d.cols, 10 );
                        if ( !( rows >= 1 && rows <= 100 ) )
                            return warn( api, t( 'Please enter a number from 1 to 100: %s' ).replace( '%s', t( 'Rows' ) ), 'rows' );
                        if ( !( cols >= 1 && cols <= 100 ) )
                            return warn( api, t( 'Please enter a number from 1 to 100: %s' ).replace( '%s', t( 'Columns' ) ), 'cols' );
                    }
                    if ( ( error = sizeError( t( 'Width' ), d.width.trim() ) ) )
                        return warn( api, error, 'width' );
                    if ( d.border.trim() && !/^\d+$/.test( d.border.trim() ) )
                        return warn( api, t( 'Please enter a whole number: %s' ).replace( '%s', t( 'Border' ) ), 'border' );
                    if ( custom.error )
                        return warn( api, custom.error, custom.field );

                    api.close();
                    commitTable( table, d, custom, rows, cols );
                }
            } );
        };

        // ---- cells

        // all cells of the column of a cell, considering colspan and rowspan (like tag_table_cell.tpl)
        var columnCells = function ( cell ) {
            var table = getParent( cell, 'table' ), grid = [], column = null, cells = [];
            Array.prototype.forEach.call( table.rows, function ( row, y ) {
                grid[y] = grid[y] || [];
                var x = 0;
                Array.prototype.forEach.call( row.cells, function ( c ) {
                    while ( grid[y][x] )
                        x++;
                    for ( var dy = 0; dy < ( c.rowSpan || 1 ); dy++ )
                    {
                        grid[y + dy] = grid[y + dy] || [];
                        for ( var dx = 0; dx < ( c.colSpan || 1 ); dx++ )
                            grid[y + dy][x + dx] = c;
                    }
                    if ( c === cell )
                        column = x;
                    x += c.colSpan || 1;
                } );
            } );
            grid.forEach( function ( row ) {
                var c = row[column];
                if ( c && cells.indexOf( c ) === -1 )
                    cells.push( c );
            } );
            return cells;
        };

        var openCellDialog = function ( element ) {
            var cell = getParent( elementOrSelection( element ), 'td,th' );
            if ( !cell )
                return;
            var tag = cell.nodeName.toLowerCase(),
                stored = D.parseCustomAttributes( cell.getAttribute( 'customattributes' ) ),
                data = { tag: tag, width: cell.getAttribute( 'width' ) || '', cssClass: elementClass( cell ), applyTo: 'cell' };

            var spec = function () {
                var def = definition( data.tag ), items = [
                    { type: 'htmlpanel', html: '<h2 class="ezoe-tag-title">' + D.escapeHtml( t( data.tag === 'th' ? 'Edit <th> tag' : 'Edit <td> tag' ) ) + '</h2>' },
                    { type: 'listbox', name: 'tag', label: t( 'Tag' ), items: [ { text: t( 'Table cell' ), value: 'td' }, { text: t( 'Table header' ), value: 'th' } ] },
                    { type: 'input', name: 'width', label: t( 'Width' ) },
                    { type: 'listbox', name: 'cssClass', label: t( 'Class' ), items: classItems( def, data.cssClass ) }
                ];
                items = items.concat( attributeFields( def, stored, data ) );
                items.push( { type: 'listbox', name: 'applyTo', label: t( 'Apply to' ), items: [
                    { text: t( 'Cell' ), value: 'cell' }, { text: t( 'Column' ), value: 'column' }, { text: t( 'Row' ), value: 'row' }
                ] } );

                return {
                    title: t( 'Cell properties' ),
                    size: 'normal',
                    body: { type: 'panel', items: items },
                    initialData: data,
                    buttons: [
                        { type: 'cancel', text: t( 'Cancel' ) },
                        { type: 'submit', text: 'OK', primary: true }
                    ],
                    onChange: function ( api, details ) {
                        // td and th have their own classes and custom attributes
                        if ( details.name === 'tag' )
                        {
                            data = Object.assign( {}, data, api.getData(), { cssClass: '' } );
                            api.redial( spec() );
                        }
                    },
                    onSubmit: function ( api ) {
                        var d = api.getData(), def = definition( d.tag ), custom = collectAttributes( def, d, stored ), error, cells;
                        if ( ( error = sizeError( t( 'Width' ), d.width.trim() ) ) )
                            return warn( api, error, 'width' );
                        if ( custom.error )
                            return warn( api, custom.error, custom.field );

                        cells = d.applyTo === 'row' ? Array.prototype.slice.call( cell.parentNode.cells )
                              : d.applyTo === 'column' ? columnCells( cell ) : [ cell ];
                        api.close();
                        editor.undoManager.transact( function () {
                            cells.forEach( function ( c ) {
                                // switchTagTypeIfNeeded() of the old dialog
                                if ( c.nodeName.toLowerCase() !== d.tag )
                                    c = editor.dom.rename( c, d.tag );
                                applyAttributes( c, d.cssClass, custom.values, { width: d.width.trim() } );
                            } );
                        } );
                        editor.nodeChanged();
                    }
                };
            };
            editor.windowManager.open( spec() );
        };

        // ---- rows

        var openRowDialog = function ( element ) {
            var row = getParent( elementOrSelection( element ), 'tr' );
            if ( !row )
                return;
            var def = definition( 'tr' ),
                stored = D.parseCustomAttributes( row.getAttribute( 'customattributes' ) ),
                data = { cssClass: elementClass( row ) },
                items = [
                    { type: 'htmlpanel', html: '<h2 class="ezoe-tag-title">' + D.escapeHtml( t( 'Edit <tr> tag' ) ) + '</h2>' },
                    { type: 'listbox', name: 'cssClass', label: t( 'Class' ), items: classItems( def, data.cssClass ) }
                ].concat( attributeFields( def, stored, data ) );

            editor.windowManager.open( {
                title: t( 'Row properties' ),
                size: 'normal',
                body: { type: 'panel', items: items },
                initialData: data,
                buttons: [
                    { type: 'cancel', text: t( 'Cancel' ) },
                    { type: 'submit', text: 'OK', primary: true }
                ],
                onSubmit: function ( api ) {
                    var d = api.getData(), custom = collectAttributes( def, d, stored );
                    if ( custom.error )
                        return warn( api, custom.error, custom.field );
                    api.close();
                    editor.undoManager.transact( function () {
                        applyAttributes( row, d.cssClass, custom.values );
                    } );
                    editor.nodeChanged();
                }
            } );
        };

        // the commands take an optional element, e.g. from a click on the status bar path
        editor.addCommand( 'ezTable', function ( ui, element ) { openTableDialog( element ); } );
        editor.addCommand( 'ezTableCell', function ( ui, element ) { openCellDialog( element ); } );
        editor.addCommand( 'ezTableRow', function ( ui, element ) { openRowDialog( element ); } );

        var inTable = function ( selector ) {
            return function ( buttonApi ) {
                var handler = function ( e ) {
                    buttonApi.setEnabled( !!getParent( e.element, selector ) );
                };
                editor.on( 'NodeChange', handler );
                return function () {
                    editor.off( 'NodeChange', handler );
                };
            };
        };

        // the table button of ezoe.ini [EditorLayout]: insert a table, or edit the table the cursor is in
        editor.ui.registry.addToggleButton( 'eztable', {
            icon: 'table',
            tooltip: t( 'Table' ),
            onAction: function () { openTableDialog(); },
            onSetup: function ( buttonApi ) {
                var handler = function ( e ) {
                    buttonApi.setActive( !!getParent( e.element, 'table' ) );
                };
                editor.on( 'NodeChange', handler );
                return function () {
                    editor.off( 'NodeChange', handler );
                };
            }
        } );
        editor.ui.registry.addButton( 'eztablecell', {
            icon: 'table-cell-properties',
            tooltip: t( 'Cell properties' ),
            onAction: function () { openCellDialog(); },
            onSetup: inTable( 'td,th' )
        } );
        editor.ui.registry.addButton( 'eztablerow', {
            icon: 'table-row-properties',
            tooltip: t( 'Row properties' ),
            onAction: function () { openRowDialog(); },
            onSetup: inTable( 'tr' )
        } );

        editor.ui.registry.addMenuItem( 'eztable', { icon: 'table', text: t( 'Table properties' ), onAction: function () { openTableDialog(); } } );
        editor.ui.registry.addMenuItem( 'eztablecell', { icon: 'table-cell-properties', text: t( 'Cell properties' ), onAction: function () { openCellDialog(); } } );
        editor.ui.registry.addMenuItem( 'eztablerow', { icon: 'table-row-properties', text: t( 'Row properties' ), onAction: function () { openRowDialog(); } } );

        // context menu: the ez dialogs inside tables, insert table outside
        editor.ui.registry.addContextMenu( 'eztable', {
            update: function ( node ) {
                return getParent( node, 'td,th' ) ? 'eztablecell eztablerow eztable' : '';
            }
        } );
    } );
}());

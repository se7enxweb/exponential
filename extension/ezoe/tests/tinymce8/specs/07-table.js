// Table dialogs: insert with size and custom attributes, cells as th applied to a row,
// attributes applied to a column, table properties with validation, row dialog, table toolbar
'use strict';

const fixtures = require( '../lib/fixtures' );
const { firstDifference, normalizeXml } = require( '../lib/session' );

module.exports = {
    name: 'table dialogs',
    async run( t )
    {
        const s = t.session;
        await s.openDraft();
        await s.setContent( fixtures.basic() );
        const table = () => s.evalEditor( e => {
            const tb = [ ...e.getBody().querySelectorAll( 'table' ) ].pop(), a = el => ( { cls: el.className.replace( /mce[\w-]*/g, '' ).trim(), width: el.getAttribute( 'width' ), border: el.getAttribute( 'border' ), custom: el.getAttribute( 'customattributes' ) } );
            return { table: a( tb ), rows: [ ...tb.rows ].map( r => [ ...r.cells ].map( c => Object.assign( { tag: c.nodeName.toLowerCase() }, a( c ) ) ) ) };
        } );
        let tableClass = null;

        await t.step( 'insert a table', async () => {
            await s.clickIn( 'p', { index: 3, at: 'end' } );
            await s.page.keyboard.press( 'End' );
            await s.clickToolbar( 'eztable' );
            await s.dialogOpen();
            const f = await s.fields();
            t.check( f.Rows && f.Columns, 'size fields for a new table', f );
            await s.type( 'Rows|Zeilen', '3' );
            await s.type( 'Width|Breite', '80%' );
            const classes = await s.options( 'Class|Klasse' );
            if ( classes.length > 1 )
            {
                tableClass = classes[1];
                await s.pick( 'Class|Klasse', tableClass );
            }
            for ( const label of Object.keys( f ).filter( l => !/^(Rows|Columns|Width|Border|Class|Zeilen|Spalten|Breite|Rahmen|Klasse)$/.test( l ) ) )
                await s.type( label.replace( /[()]/g, '\\$&' ), 'Wert ' + label );
            await s.ok();
            await s.dialogClosed();
            const tb = await table();
            t.check( tb.rows.length === 3 && tb.table.width === '80%', 'table 3 rows, width 80%', tb.table );
        } );

        await t.step( 'header row: th applied to the row', async () => {
            await s.clickIn( 'table:last-of-type tr:first-child td' );
            await s.clickPath( '^table cell' );
            await s.pick( 'Tag', 'Table header|Tabellenkopf' );
            await s.pick( 'Apply to|Anwenden auf', 'Row|Zeile' );
            await s.ok();
            await s.dialogClosed();
            const tb = await table();
            t.check( tb.rows[0].every( c => c.tag === 'th' ), 'first row consists of th', tb.rows[0] );
        } );

        await t.step( 'width applied to a column', async () => {
            await s.clickIn( 'table:last-of-type tr:nth-child(2) td', { index: 1 } );
            await s.clickPath( '^table cell' );
            await s.type( 'Width|Breite', '30%' );
            await s.pick( 'Apply to|Anwenden auf', 'Column|Spalte' );
            await s.ok();
            await s.dialogClosed();
            const tb = await table();
            t.check( tb.rows.every( r => r[1].width === '30%' ), 'second column 30% wide', tb.rows.map( r => r[1] ) );
        } );

        await t.step( 'table properties via path with validation', async () => {
            await s.clickIn( 'table:last-of-type tr:nth-child(3) td' );
            await s.clickPath( '^table' );
            await s.type( 'Width|Breite', 'breit' );
            await s.ok();
            t.check( !!( await s.page.$( '.tox-dialog' ) ) && /pixel|Pixel/.test( await s.notification() || '' ), 'invalid width is rejected', await s.notification() );
            await s.type( 'Width|Breite', '100%' );
            await s.type( 'Border|Rahmen', '1' );
            await s.ok();
            await s.dialogClosed();
            const tb = await table();
            t.check( tb.table.width === '100%' && tb.table.border === '1', 'width and border changed', tb.table );
        } );

        await t.step( 'row dialog and table toolbar', async () => {
            await s.clickIn( 'table:last-of-type tr:nth-child(3) td' );
            const toolbar = await s.contextToolbar();
            t.check( toolbar.some( l => /Cell properties|Zelleneigenschaften/.test( l ) ), 'table toolbar has the ez dialogs', toolbar );
            await s.clickPath( '^table row' );
            t.check( /Row properties|Zeileneigenschaften/.test( await s.dialogTitle() ), 'row dialog via path' );
            await s.ok();
            await s.dialogClosed();
        } );

        await t.step( 'store', async () => {
            t.check( !( await s.store() ).length, 'stored without messages' );
            const xml = s.storedXml();
            if ( xml === null )
                return;
            const stored = ( xml.match( /<table[^>]*width="100%"[^>]*border="1"[\s\S]*?<\/table>/ ) || [ '' ] )[0];
            t.check( !!stored, 'table stored with width and border', ( xml.match( /<table[^>]*>/g ) || [] ) );
            // the second header cell became a td again: "apply to column" sets the chosen tag on all cells like tag_table_cell.tpl
            t.check( /^<table[^>]*><tr><th[ />]/.test( stored ) && ( stored.match( /<th[ />]/g ) || [] ).length === 1, 'header cell stored, column cells are td', stored.slice( 0, 300 ) );
            t.check( ( stored.match( /xhtml:width="30%"/g ) || [] ).length === 3, 'column width stored', stored.slice( 0, 300 ) );
            if ( tableClass )
                t.check( /<table class="[^"]+"/.test( stored ), 'table class stored', stored.slice( 0, 160 ) );
            await s.store();
            const again = s.storedXml();
            t.check( normalizeXml( again ) === normalizeXml( xml ), 'storing again changes nothing (besides xmlns:tmp of the parser)', firstDifference( normalizeXml( xml ), normalizeXml( again ) ) );
        } );
    }
};

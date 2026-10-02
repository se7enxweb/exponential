// Embed dialog: search with class filter, browse, properties of an existing embed, alignment, table cells
'use strict';

const fixtures = require( '../lib/fixtures' );
const { firstDifference, normalizeXml } = require( '../lib/session' );

module.exports = {
    name: 'embed dialog',
    async run( t )
    {
        const s = t.session, env = t.env;
        await s.openDraft();
        await s.setContent( fixtures.withEmbed() );

        await t.step( 'search with class filter, image centered', async () => {
            await s.clickIn( 'p', { index: 1, at: 'end' } );
            await s.clickToolbar( 'ezembed' );
            await s.dialogOpen();
            t.check( /Embed object|Objekt einbetten/.test( await s.dialogTitle() ), 'dialog opens on the upload tab', await s.dialogTitle() );
            await s.clickTab( 'Search|Suchen' );
            await s.pickClassFilter( env.imageClass );
            await s.type( 'Search|Suchen', env.imageSearch );
            await s.page.keyboard.press( 'Enter' );
            await s.idle();
            const rows = await s.listRows();
            t.check( rows.length > 0 && rows.every( r => r.includes( env.imageClass ) ), 'results only of class ' + env.imageClass, rows.slice( 0, 3 ) );
            t.check( ( await s.classFilterSelection() ).join() === env.imageClass, 'class filter kept after the search', await s.classFilterSelection() );
            t.check( !/&amp;/.test( rows.join() ), 'names are not encoded twice', rows.slice( 0, 3 ) );
            await s.clickRow( 0 );
            const f = await s.fields();
            t.check( f.Relation && /Object|Objekt/.test( f.Relation ), 'properties show the relation', f );
            t.check( f.Size === 'medium' || f['Größe'] === 'medium', 'image size defaults to medium', f );
            await s.pick( 'Align|Ausrichtung', 'Center|Mitte' );
            await s.ok();
            await s.dialogClosed();
            const img = await s.evalEditor( e => { const n = e.getBody().querySelectorAll( 'img[id^=eZObject_]' )[0]; return n ? { align: n.getAttribute( 'align' ), cls: n.className } : null; } );
            t.check( img && img.align === 'middle' && /ezoeAlignmiddle/.test( img.cls ), 'image embedded with align middle like TinyMCE 3', img );
        } );

        await t.step( 'existing embed: double click, class, align, custom attributes, preview', async () => {
            await s.doubleClickIn( 'div[id^=eZObject_]' );
            await s.dialogOpen();
            const tabs = await s.page.evaluate( () => [ ...document.querySelectorAll( '.tox-dialog__body-nav-item' ) ].map( x => x.textContent.trim() ) );
            t.check( tabs.length === 1, 'only the properties tab for an existing embed', tabs );
            t.check( /<embed>/.test( await s.dialogText( '.ezoe-tag-title' ) ), 'heading "Edit <embed> tag"', await s.dialogText( '.ezoe-tag-title' ) );
            t.check( ( await s.dialogText( '.ezoe-embed-preview' ) ).length > 0, 'preview of the embed is shown' );
            const classes = await s.options( 'Class|Klasse' );
            t.info( 'embed classes: ' + classes.join( ', ' ) );
            if ( classes.length > 1 )
                await s.pick( 'Class|Klasse', classes[1] );
            await s.pick( 'Align|Ausrichtung', 'Right|Rechts' );
            await s.ok();
            await s.dialogClosed();
            const div = await s.evalEditor( e => { const n = e.getBody().querySelector( 'div[id^=eZObject_]' ); return { align: n.getAttribute( 'align' ), cls: n.className, custom: n.getAttribute( 'customattributes' ) }; } );
            t.check( div.align === 'right', 'alignment changed', div );
            if ( classes.length > 1 )
                t.check( div.cls.split( ' ' ).length > 2, 'class set on the embed', div );
        } );

        await t.step( 'path click and switch embed object via browse', async () => {
            await s.clickIn( 'div[id^=eZObject_]' );
            await s.clickPath( '^embed' );
            await s.clickButton( '^(Switch embed object|Anderes Objekt wählen)$' );
            await s.idle();
            await s.clickTab( 'Browse|Durchsuchen' );
            const path = await s.dialogText( '.ezoe-list-path' );
            t.check( path.length > 0, 'browse shows the path', path );
            const container = await s.page.$( '.tox-dialog table.ezoe-list td.ezoe-list-name a' );
            if ( container )
            {
                await container.click();
                await s.idle();
            }
            await s.clickRow( 0, 'class' );
            t.check( /Properties|Eigenschaften/.test( await s.page.evaluate( () => document.querySelector( '.tox-dialog__body-nav-item--active' ).textContent ) ), 'choosing continues on properties' );
            await s.ok();
            await s.dialogClosed();
        } );

        await t.step( 'images in table cells', async () => {
            for ( const [ cell, align ] of [ [ 0, 'Right|Rechts' ], [ 3, 'Left|Links' ] ] )
            {
                await s.clickIn( 'td', { index: cell } );
                await s.clickToolbar( 'ezembed' );
                await s.dialogOpen();
                await s.clickTab( 'Search|Suchen' );
                await s.type( 'Search|Suchen', env.imageSearch );
                await s.page.keyboard.press( 'Enter' );
                await s.idle();
                await s.clickRow( 0 );
                await s.pick( 'Align|Ausrichtung', align );
                await s.ok();
                await s.dialogClosed();
            }
            const cells = await s.evalEditor( e => [ ...e.getBody().querySelectorAll( 'td' ) ].map( td => { const i = td.querySelector( 'img[id^=eZObject_]' ); return i ? i.getAttribute( 'align' ) : null; } ) );
            t.check( cells[0] === 'right' && cells[3] === 'left', 'images in the cells with their alignment', cells );
        } );

        await t.step( 'store', async () => {
            t.check( !( await s.store() ).length, 'stored without messages' );
            const xml = s.storedXml();
            if ( xml === null )
                return t.skip( 'ezxml checks need EZ_DB' );
            const embeds = xml.match( /<embed[^>]*\/>/g ) || [];
            t.check( embeds.some( e => /align="center"/.test( e ) ), 'centered image stored as align="center"', embeds );
            t.check( embeds.filter( e => /align="(right|left)"/.test( e ) ).length >= 2, 'aligned images in the table stored', embeds );
            t.check( !/ezembed|ezoeItem/.test( xml ), 'no editor markup in the ezxml' );
            await s.store();
            const again = s.storedXml();
            t.check( normalizeXml( again ) === normalizeXml( xml ), 'storing again changes nothing (besides xmlns:tmp of the parser)', firstDifference( normalizeXml( xml ), normalizeXml( again ) ) );
        } );
    }
};

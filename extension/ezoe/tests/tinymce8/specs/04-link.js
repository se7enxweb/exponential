// Link dialog: external link, browse with paging, search with class filter as object link,
// anchors, link without selection, mailto, context toolbar
'use strict';

const fixtures = require( '../lib/fixtures' );
const { firstDifference, normalizeXml } = require( '../lib/session' );

module.exports = {
    name: 'link dialog',
    async run( t )
    {
        const s = t.session, env = t.env;
        await s.openDraft();
        await s.setContent( fixtures.basic() );
        const link = text => s.evalEditor( ( e, txt ) => { const a = [ ...e.getBody().querySelectorAll( 'a[href]' ) ].find( x => x.textContent.trim() === txt ); return a ? { href: a.getAttribute( 'href' ), title: a.getAttribute( 'title' ), target: a.getAttribute( 'target' ), view: a.getAttribute( 'view' ) } : null; }, text );

        await t.step( 'external link with title, target and view', async () => {
            await s.typeAndSelect( 'p', 1, 'Extern' );
            await s.clickToolbar( 'ezlink' );
            await s.dialogOpen();
            await s.type( 'URL', 'https://example.org/seite?a=1&b=2' );
            await s.type( 'Title|Titel', 'Beispiel & Titel' );
            await s.pick( 'Open link in|Öffnen in', 'New window|Neues Fenster' );
            const views = await s.options( 'View|Ansicht' );
            if ( views.length > 1 )
                await s.pick( 'View|Ansicht', views[1] );
            await s.ok();
            await s.dialogClosed();
            const a = await link( 'Extern' );
            t.check( a && a.href === 'https://example.org/seite?a=1&b=2' && a.title === 'Beispiel & Titel' && a.target === '_blank', 'external link created', a );
        } );

        await t.step( 'edit via path, new target from browse', async () => {
            await s.clickIn( 'a[href^="https://example.org"]' );
            await s.clickPath( '^link' );
            t.check( /Edit link|Link bearbeiten/.test( await s.dialogTitle() ), 'edit dialog opens prefilled', await s.fields() );
            await s.clickTab( 'Browse|Durchsuchen' );
            const container = await s.page.$( '.tox-dialog table.ezoe-list td.ezoe-list-name a' );
            if ( container )
            {
                await container.click();
                await s.idle();
            }
            const paging = await s.page.$( '.tox-dialog .ezoe-list-paging a[data-ezoe-value]' );
            if ( paging )
            {
                await paging.click();
                await s.idle();
                t.check( /\d+ (to|bis) \d+/.test( await s.dialogText( '.ezoe-list-paging' ) ), 'paging works', await s.dialogText( '.ezoe-list-paging' ) );
            }
            await s.clickRow( 0, 'class' );
            const f = await s.fields();
            t.check( /^eznode:\/\/\d+$/.test( f.URL ), 'node link taken over', f );
            t.check( /(Node|Knoten) \d+:/.test( await s.dialogText( '.ezoe-selected' ) ), 'link target shown with its name', await s.dialogText( '.ezoe-selected' ) );
            await s.ok();
            await s.dialogClosed();
            const a = await link( 'Extern' );
            t.check( a && /^eznode:\/\/\d+$/.test( a.href ) && a.target === '_blank', 'link changed, target kept', a );
        } );

        await t.step( 'search with class filter, object link', async () => {
            await s.typeAndSelect( 'p', 2, 'Objektlink' );
            await s.clickToolbar( 'ezlink' );
            await s.dialogOpen();
            await s.clickTab( 'Search|Suchen' );
            await s.checkbox( 'Link to the object instead of the node \\(ezobject://\\)|Objekt statt Knoten verlinken \\(ezobject://\\)' );
            await s.pickClassFilter( env.objectClass );
            await s.type( 'Search|Suchen', env.objectSearch );
            await s.page.keyboard.press( 'Enter' );
            await s.idle();
            const rows = await s.listRows();
            t.check( rows.length > 0 && rows.every( r => r.includes( env.objectClass ) ), 'results only of class ' + env.objectClass, rows.slice( 0, 3 ) );
            await s.clickRow( 0 );
            await s.ok();
            await s.dialogClosed();
            const a = await link( 'Objektlink' );
            t.check( a && /^ezobject:\/\/\d+$/.test( a.href ), 'object link created', a );
        } );

        await t.step( 'anchor and link to it', async () => {
            await s.clickIn( 'p', { index: 3 } );
            await s.page.keyboard.press( 'Home' );
            await s.clickToolbar( 'anchor' );
            await s.dialogOpen();
            await s.page.keyboard.type( 'sprungmarke' );
            await s.clickButton( '^(Save|Speichern|OK)$' );
            await s.dialogClosed();
            await s.typeAndSelect( 'p', 1, 'Ankerlink' );
            await s.clickToolbar( 'ezlink' );
            await s.dialogOpen();
            await s.pick( 'Anchor in this text|Anker in diesem Text', 'sprungmarke' );
            t.check( ( await s.fields() ).URL === '#sprungmarke', 'anchor taken over into the URL', await s.fields() );
            await s.ok();
            await s.dialogClosed();
        } );

        await t.step( 'link without selection, mailto, context toolbar', async () => {
            await s.clickIn( 'p', { index: 2, at: 'end' } );
            await s.page.keyboard.press( 'End' );
            await s.page.keyboard.type( ' ' );
            await s.clickToolbar( 'ezlink' );
            await s.dialogOpen();
            await s.type( 'URL', 'eznode://2' );
            await s.type( 'Text to display|Linktext', 'Startseite' );
            await s.ok();
            await s.dialogClosed();
            t.check( !!( await link( 'Startseite' ) ), 'link with own text inserted' );

            await s.typeAndSelect( 'p', 2, 'Mail' );
            await s.clickToolbar( 'ezlink' );
            await s.dialogOpen();
            await s.type( 'URL', 'mailto:info@example.org' );
            await s.ok();
            await s.dialogClosed();
            await s.clickIn( 'a[href^="mailto"]' );
            await new Promise( r => setTimeout( r, 600 ) );
            t.check( ( await s.contextToolbar() ).length >= 2, 'context toolbar on links', await s.contextToolbar() );
            await s.clickContextToolbar( 'Remove link|Link entfernen' );
            t.check( !( await link( 'Mail' ) ), 'link removed, text kept' );
        } );

        await t.step( 'store', async () => {
            t.check( !( await s.store() ).length, 'stored without messages' );
            const xml = s.storedXml();
            if ( xml === null )
                return t.skip( 'ezxml checks need EZ_DB' );
            t.check( /<link target="_blank"[^>]*xhtml:title="Beispiel &amp; Titel"[^>]*node_id="\d+"/.test( xml ), 'node link with target and title', ( xml.match( /<link[^>]*>/g ) || [] ) );
            t.check( /<link object_id="\d+">Objektlink/.test( xml ), 'object link stored' );
            t.check( /<anchor name="sprungmarke"\/>/.test( xml ) && /<link anchor_name="sprungmarke">/.test( xml ), 'anchor and anchor link stored' );
            t.check( /<link node_id="2">Startseite/.test( xml ), 'link with own text stored' );
            await s.store();
            const again = s.storedXml();
            t.check( normalizeXml( again ) === normalizeXml( xml ), 'storing again changes nothing (besides xmlns:tmp of the parser)', firstDifference( normalizeXml( xml ), normalizeXml( again ) ) );
        } );
    }
};

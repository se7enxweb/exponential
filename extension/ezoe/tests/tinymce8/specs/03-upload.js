// Upload tab of the embed dialog: file name shown, invalid types rejected, image and PDF embedded.
// The uploaded objects are removed again after the run.
'use strict';

const fixtures = require( '../lib/fixtures' );

module.exports = {
    name: 'upload in the embed dialog',
    async run( t )
    {
        const s = t.session, env = t.env;
        await s.openDraft();
        await s.setContent( fixtures.basic() );

        const chooseFile = async file => {
            await ( await s.page.$( '.tox-dialog input[type=file]' ) ).uploadFile( env.fixtures + '/' + file );
            await new Promise( r => setTimeout( r, 700 ) );
        };

        await t.step( 'invalid file type', async () => {
            await s.clickIn( 'p', { index: 1, at: 'end' } );
            await s.clickToolbar( 'ezembed' );
            await s.dialogOpen();
            await chooseFile( 'ezoe-test-invalid.xyz' );
            t.check( /can not be uploaded|nicht hochgeladen/.test( await s.notification() || '' ), 'invalid type is rejected with a notification', await s.notification() );
            await s.cancel();
            await s.dialogClosed();
        } );

        await t.step( 'image with OK: file name shown, embedded right away', async () => {
            await s.clickIn( 'p', { index: 1, at: 'end' } );
            await s.clickToolbar( 'ezembed' );
            await s.dialogOpen();
            await chooseFile( 'ezoe-test-image.png' );
            t.check( /ezoe-test-image\.png/.test( await s.dialogText( '.ezoe-upload-file' ) ), 'chosen file is shown', await s.dialogText( '.ezoe-upload-file' ) );
            const f = await s.fields();
            t.check( f.Name === 'ezoe-test-image', 'name is suggested from the file name', f );
            await s.type( 'Alternative text \\(images\\)|Alternativtext \\(Bilder\\)', 'Alternativtext Test' );
            await s.ok();
            await s.dialogClosed();
            const img = await s.evalEditor( e => { const n = [ ...e.getBody().querySelectorAll( 'img[id^=eZObject_]' ) ].pop(); return n ? { title: n.getAttribute( 'title' ), src: n.getAttribute( 'src' ) } : null; } );
            t.check( img && img.title === 'Alternativtext Test' && /_medium\./.test( img.src ), 'image embedded with its alternative text and the medium alias', img );
        } );

        await t.step( 'PDF with "Upload local file": continues on properties', async () => {
            await s.clickIn( 'p', { index: 2, at: 'end' } );
            await s.clickToolbar( 'ezembed' );
            await s.dialogOpen();
            await chooseFile( 'ezoe-test-document.pdf' );
            await s.clickButton( '^(Upload local file|Datei hochladen)$' );
            await s.idle();
            await new Promise( r => setTimeout( r, 1200 ) );
            const active = await s.page.evaluate( () => document.querySelector( '.tox-dialog__body-nav-item--active' ).textContent );
            t.check( /Properties|Eigenschaften/.test( active ), 'continues on the properties tab', active );
            t.check( /ezoe-test-document/.test( ( await s.fields() ).Relation || '' ), 'the uploaded object is selected', await s.fields() );
            await s.ok();
            await s.dialogClosed();
            const div = await s.evalEditor( e => { const n = [ ...e.getBody().querySelectorAll( 'div[id^=eZObject_]' ) ].pop(); return n ? n.className : null; } );
            t.check( /ezoeItemContentTypeFiles/.test( div || '' ), 'PDF embedded as file', div );
        } );

        await t.step( 'store', async () => {
            t.check( !( await s.store() ).length, 'stored without messages' );
            const xml = s.storedXml();
            if ( xml !== null )
                t.check( ( xml.match( /<embed[^>]*object_id="\d+"/g ) || [] ).length >= 2, 'both uploads stored as embeds', xml.match( /<embed[^>]*\/>/g ) );
            t.info( 'uploaded objects are removed after the run: ' + s.uploaded.map( o => o.objectId ).join( ', ' ) );
        } );
    }
};

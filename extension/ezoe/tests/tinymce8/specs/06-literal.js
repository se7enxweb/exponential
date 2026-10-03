// Literal dialog: multi line code with html characters, paragraph after a new literal, edit via path,
// plain text paste, remove keeps the text
'use strict';

const fixtures = require( '../lib/fixtures' );

module.exports = {
    name: 'literal dialog',
    async run( t )
    {
        const s = t.session;
        await s.openDraft();
        await s.setContent( fixtures.basic() );

        await t.step( 'insert a literal', async () => {
            await s.clickIn( 'p', { index: 1, at: 'end' } );
            await s.page.keyboard.press( 'End' );
            await s.clickToolbar( 'ezliteral' );
            await s.dialogOpen();
            await s.page.click( '.tox-dialog textarea' );
            await s.page.keyboard.type( '<b>Code</b> & "Zeichen"' );
            await s.page.keyboard.press( 'Enter' );
            await s.page.keyboard.type( '    eingerückt' );
            await s.ok();
            await s.dialogClosed();
            const r = await s.evalEditor( e => { const pre = e.getBody().querySelector( 'pre' ); return { html: pre.innerHTML, next: pre.nextElementSibling && pre.nextElementSibling.nodeName, cursor: e.selection.getNode().nodeName }; } );
            t.check( r.html === '&lt;b&gt;Code&lt;/b&gt; &amp; "Zeichen"<br>    eingerückt', 'text with line break and indentation', r.html );
            t.check( r.next === 'P' && r.cursor === 'P', 'a paragraph follows and gets the cursor', r );
        } );

        await t.step( 'edit via path', async () => {
            await s.clickIn( 'pre' );
            await s.clickPath( '^literal' );
            const text = await s.page.evaluate( () => document.querySelector( '.tox-dialog textarea' ).value );
            t.check( text === '<b>Code</b> & "Zeichen"\n    eingerückt', 'dialog shows the text', text );
            await s.page.evaluate( () => { const ta = document.querySelector( '.tox-dialog textarea' ); ta.focus(); ta.setSelectionRange( ta.value.length, ta.value.length ); } );
            await s.page.keyboard.press( 'Enter' );
            await s.page.keyboard.type( 'dritte Zeile' );
            await s.ok();
            await s.dialogClosed();
        } );

        await t.step( 'paste html into the literal as plain text', async () => {
            await s.clickIn( 'pre' );
            // the cursor to the end of the literal (a click lands in its first line)
            await s.evalEditor( e => { const pre = e.getBody().querySelector( 'pre' ); e.selection.select( pre, true ); e.selection.collapse( false ); } );
            await s.evalEditor( e => e.execCommand( 'mceInsertClipboardContent', false, { html: '<p><strong>fett</strong> eingefügt</p>' } ) );
            const html = await s.evalEditor( e => e.getBody().querySelector( 'pre' ).innerHTML );
            t.check( !/<strong>/.test( html ) && /fett eingefügt/.test( html ), 'pasted as plain text', html );
        } );

        await t.step( 'store', async () => {
            t.check( !( await s.store() ).length, 'stored without messages' );
            const xml = s.storedXml();
            if ( xml !== null )
                t.check( /<literal>&lt;b&gt;Code&lt;\/b&gt; &amp; "Zeichen"\n    eingerückt\ndritte Zeilefett eingefügt<\/literal>/.test( xml ), 'literal stored with its line breaks', ( xml.match( /<literal>[^<]*<\/literal>/ ) || [ 'none' ] )[0] );
        } );

        await t.step( 'remove keeps the text', async () => {
            await s.clickIn( 'pre' );
            await new Promise( r => setTimeout( r, 600 ) );
            await s.clickContextToolbar( 'Remove literal|Literal entfernen' );
            t.check( await s.evalEditor( e => !e.getBody().querySelector( 'pre' ) && e.getBody().innerHTML.includes( '&lt;b&gt;Code' ) ), 'literal removed, text kept as paragraph' );
        } );
    }
};

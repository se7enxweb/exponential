// Custom tag dialog: inline tag on a word, block tag around a paragraph with attributes,
// empty block tag with placeholder, edit via path, context toolbar edit / remove
'use strict';

const fixtures = require( '../lib/fixtures' );
const { firstDifference, normalizeXml } = require( '../lib/session' );

module.exports = {
    name: 'custom tag dialog',
    async run( t )
    {
        const s = t.session;
        await s.openDraft();
        await s.setContent( fixtures.basic() );
        const customTags = () => s.evalEditor( e => [ ...e.getBody().querySelectorAll( '[type=custom]' ) ].map( n => ( {
            tag: n.nodeName.toLowerCase(), name: n.className.replace( /ezoeItemCustomTag\s*/, '' ).trim(), attributes: n.getAttribute( 'customattributes' ), text: n.textContent.trim()
        } ) ) );

        let tags = [], blockTag = null, inlineTag = null;

        await t.step( 'inline tag on a selected word', async () => {
            await s.typeAndSelect( 'p', 1, 'Markiert' );
            await s.clickToolbar( 'ezcustomtag' );
            await s.dialogOpen();
            tags = await s.options( 'Tag' );
            t.info( 'custom tags: ' + tags.join( ', ' ) );
            inlineTag = [ 'strike', 'underline' ].find( x => tags.includes( x ) );
            blockTag = [ 'factbox', 'quote' ].find( x => tags.includes( x ) );
            if ( !inlineTag )
                throw new Error( 'no inline custom tag (strike / underline) configured' );
            await s.pick( 'Tag', inlineTag );
            await s.ok();
            await s.dialogClosed();
            const c = ( await customTags() ).find( x => x.name === inlineTag );
            t.check( c && c.text === 'Markiert', inlineTag + ' around the selected word', await customTags() );
        } );

        await t.step( 'block tag around a paragraph with attributes', async () => {
            if ( !blockTag )
                return t.skip( 'no block custom tag (factbox / quote) configured' );
            await s.clickIn( 'p', { index: 2 } );
            await s.page.keyboard.press( 'Home' );
            const k = s.page.keyboard;
            await k.down( 'Shift' ); await k.press( 'End' ); await k.up( 'Shift' );
            await s.clickToolbar( 'ezcustomtag' );
            await s.dialogOpen();
            await s.pick( 'Tag', blockTag );
            const f = await s.fields();
            const textField = Object.keys( f ).find( k2 => k2 !== 'Tag' && typeof f[k2] === 'string' );
            if ( textField )
                await s.type( textField.replace( /[()]/g, '\\$&' ), 'Testwert' );
            await s.ok();
            await s.dialogClosed();
            const c = ( await customTags() ).find( x => x.name === blockTag );
            t.check( c && c.tag === 'div' && /Dritter Absatz/.test( c.text ), blockTag + ' wraps the paragraph', c );
            if ( textField )
                t.check( c && /Testwert/.test( c.attributes || '' ), 'attribute stored on the tag', c );
        } );

        await t.step( 'edit via path, context toolbar edit and remove', async () => {
            if ( !blockTag )
                return t.skip( 'no block custom tag configured' );
            await s.clickIn( 'div[type=custom] p' );
            await s.clickPath( '^custom\\.' + blockTag );
            t.check( /Edit custom tag|Custom Tag bearbeiten/.test( await s.dialogTitle() ), 'edit dialog via path', await s.fields() );
            const tagDisabled = await s.page.evaluate( () => { const b = document.querySelector( '.tox-dialog .tox-listbox' ); return b.getAttribute( 'aria-disabled' ) === 'true' || b.disabled; } );
            t.check( tagDisabled, 'tag can not be changed when editing' );
            await s.cancel();
            await s.dialogClosed();
            await s.clickIn( 'span[type=custom], u[type=custom]' );
            await new Promise( r => setTimeout( r, 600 ) );
            await s.clickContextToolbar( 'Remove custom tag|Custom Tag entfernen' );
            const left = await customTags();
            t.check( !left.some( x => x.name === inlineTag ) && ( await s.evalEditor( e => e.getBody().textContent.includes( 'Markiert' ) ) ), 'inline tag removed, text kept', left );
        } );

        await t.step( 'empty block tag with placeholder', async () => {
            if ( !blockTag )
                return t.skip( 'no block custom tag configured' );
            await s.clickIn( 'p', { index: 3, at: 'end' } );
            await s.page.keyboard.press( 'End' );
            await s.clickToolbar( 'ezcustomtag' );
            await s.dialogOpen();
            await s.pick( 'Tag', blockTag );
            await s.ok();
            await s.dialogClosed();
        } );

        await t.step( 'store', async () => {
            t.check( !( await s.store() ).length, 'stored without messages' );
            const xml = s.storedXml();
            if ( xml === null || !blockTag )
                return;
            const customs = xml.match( new RegExp( '<custom name="' + blockTag + '"[^>]*\\/?>', 'g' ) ) || [];
            t.check( customs.length === 2, 'both ' + blockTag + ' tags stored', customs );
            t.check( customs.some( c => /\/>$/.test( c ) ), 'the empty tag is stored without the placeholder', customs );
            await s.store();
            const again = s.storedXml();
            t.check( normalizeXml( again ) === normalizeXml( xml ), 'storing again changes nothing (besides xmlns:tmp of the parser)', firstDifference( normalizeXml( xml ), normalizeXml( again ) ) );
        } );
    }
};

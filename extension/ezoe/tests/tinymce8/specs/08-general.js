// General tag dialog: class and custom attributes of paragraphs, headings, lists and strong / emphasize.
// Classes and attributes come from content.ini; tags without them show a notice instead.
'use strict';

const fixtures = require( '../lib/fixtures' );
const { firstDifference, normalizeXml } = require( '../lib/session' );

module.exports = {
    name: 'general tag dialog',
    async run( t )
    {
        const s = t.session;
        await s.openDraft();
        await s.setContent( fixtures.withTags() );
        const changed = {};

        const editTag = async ( selector, path, label ) => {
            await s.clickIn( selector );
            await s.clickPath( path );
            t.check( /properties|Eigenschaften/.test( await s.dialogTitle() ), label + ': dialog via path', await s.dialogTitle() );
            const classes = await s.options( 'Class|Klasse' );
            if ( classes.length > 1 )
            {
                await s.pick( 'Class|Klasse', classes[1] );
                changed[label] = classes[1];
            }
            else
                t.check( /no classes|keine Klassen/.test( await s.dialogText( '.tox-dialog__body-content p' ) ), label + ': notice without configured classes or attributes', await s.dialogText( '.tox-dialog__body-content p' ) );
            await s.ok();
            await s.dialogClosed();
            if ( changed[label] )
            {
                const cls = await s.evalEditor( ( e, sel ) => e.getBody().querySelector( sel ).className, selector );
                t.check( cls.trim().length > 0, label + ': class set', cls );
            }
        };

        await t.step( 'paragraph', () => editTag( 'p', '^paragraph', 'paragraph' ) );
        await t.step( 'heading', () => editTag( 'h2', '^header 2', 'header' ) );
        await t.step( 'strong', () => editTag( 'strong', '^strong', 'strong' ) );
        await t.step( 'emphasize', () => editTag( 'em', '^emphasize', 'emphasize' ) );
        await t.step( 'list item', () => editTag( 'li', '^list item', 'list item' ) );

        await t.step( 'cancel changes nothing, alignment is kept', async () => {
            await s.clickIn( 'p', { index: 1 } );
            await s.clickToolbar( 'aligncenter' );
            const before = await s.evalEditor( e => e.getBody().querySelectorAll( 'p' )[1].outerHTML );
            await s.clickIn( 'p', { index: 1 } );
            await s.clickPath( '^paragraph' );
            await s.cancel();
            await s.dialogClosed();
            t.check( before === await s.evalEditor( e => e.getBody().querySelectorAll( 'p' )[1].outerHTML ), 'cancel leaves the paragraph unchanged' );
            await s.clickIn( 'p', { index: 1 } );
            await s.clickPath( '^paragraph' );
            await s.ok();
            await s.dialogClosed();
            t.check( /text-align: center|align="center"/.test( await s.evalEditor( e => e.getBody().querySelectorAll( 'p' )[1].outerHTML ) ), 'OK keeps the alignment' );
        } );

        await t.step( 'context menu entry', async () => {
            await s.clickIn( 'p', { index: 2, button: 'right' } );
            await new Promise( r => setTimeout( r, 700 ) );
            const items = await s.page.evaluate( () => [ ...document.querySelectorAll( '.tox-menu .tox-collection__item' ) ].map( i => i.textContent.trim() ) );
            t.check( items.some( i => /Tag properties|Tag-Eigenschaften/.test( i ) ), 'context menu has "Tag properties"', items );
            await s.page.keyboard.press( 'Escape' );
        } );

        await t.step( 'store', async () => {
            t.check( !( await s.store() ).length, 'stored without messages' );
            const xml = s.storedXml();
            if ( xml === null )
                return;
            t.check( /<paragraph[^>]*align="center"/.test( xml ), 'alignment stored' );
            Object.keys( changed ).forEach( label => {
                const tag = { paragraph: 'paragraph', header: 'header', strong: 'strong', emphasize: 'emphasize', 'list item': 'li' }[label];
                t.check( new RegExp( '<' + tag + '[^>]*class="' ).test( xml ), label + ': class stored' );
            } );
            await s.store();
            const again = s.storedXml();
            t.check( normalizeXml( again ) === normalizeXml( xml ), 'storing again changes nothing (besides xmlns:tmp of the parser)', firstDifference( normalizeXml( xml ), normalizeXml( again ) ) );
        } );
    }
};

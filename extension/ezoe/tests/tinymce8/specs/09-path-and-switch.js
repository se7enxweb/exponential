// Status bar path with ezxml names, path clicks open the dialogs, toolbar look, engine switch
'use strict';

const fixtures = require( '../lib/fixtures' );

module.exports = {
    name: 'path, toolbar and engine switch',
    async run( t )
    {
        const s = t.session;
        await s.openDraft();
        await s.setContent( fixtures.withTags() );

        await t.step( 'ezxml names in the path', async () => {
            const expected = [
                [ 'h2', [ 'header 2' ] ],
                [ 'strong', [ 'paragraph', 'strong' ] ],
                [ 'a[href]', [ 'paragraph', 'link' ] ],
                [ 'td', null ],
                [ 'div.factbox p', [ 'custom.factbox', 'paragraph' ] ],
                [ 'pre', [ 'literal' ] ],
                [ 'u', [ 'paragraph', 'custom.underline' ] ],
                [ 'li', null ]
            ];
            for ( const [ selector, names ] of expected )
            {
                await s.clickIn( selector );
                const path = await s.pathItems();
                if ( names )
                    t.check( JSON.stringify( path ) === JSON.stringify( names ), selector + ': ' + path.join( ' » ' ), names.join( ' » ' ) );
                else
                    t.check( path.length >= 2 && !path.some( p => /^(tbody|td|tr|ul|li|div|p)$/.test( p ) ), selector + ': ' + path.join( ' » ' ) );
            }
            const label = await s.page.evaluate( id => getComputedStyle( document.querySelector( '#' + id + ' + .tox-tinymce .tox-statusbar__path' ), '::before' ).content, s.editorId );
            t.check( /Path|Pfad/.test( label ), 'path label in front', label );
        } );

        await t.step( 'path clicks open the dialogs', async () => {
            const cases = [ [ 'a[href]', '^link$', /link/i ], [ 'div.factbox p', '^custom\\.factbox', /custom tag/i ], [ 'pre', '^literal$', /literal/i ], [ 'td', '^table cell$', /cell|zelle/i ], [ 'h2', '^header 2', /header|Eigenschaften/i ] ];
            for ( const [ selector, path, title ] of cases )
            {
                await s.clickIn( selector );
                await s.clickPath( path );
                const actual = await s.dialogTitle();
                t.check( title.test( actual || '' ), path + ' → ' + actual );
                await s.cancel();
                await s.dialogClosed();
            }
            await s.clickIn( 'div[id^=eZObject_]' );
            await s.clickPath( '^embed' );
            t.check( /embedded|eingebettet/i.test( await s.dialogTitle() || '' ), 'embed → ' + await s.dialogTitle() );
            await s.cancel();
            await s.dialogClosed();
        } );

        await t.step( 'o2k7 look and old toolbar buttons', async () => {
            const r = await s.page.evaluate( id => {
                const c = document.querySelector( '#' + id + ' + .tox-tinymce' );
                const icon = c.querySelector( '[data-mce-name="bold"] .tox-icon' );
                return { skin: c.classList.contains( 'ezoe-skin-o2k7' ), sprite: icon ? getComputedStyle( icon ).backgroundImage : '', buttons: [ ...c.querySelectorAll( '.tox-toolbar [data-mce-name]' ) ].map( b => b.getAttribute( 'data-mce-name' ) ) };
            }, s.editorId );
            if ( !r.skin )
                return t.skip( 'skin is not o2k7' );
            t.check( /icons\.png/.test( r.sprite ), 'toolbar uses the icon sprite of the TinyMCE 3 ez theme', r.sprite );
            t.check( [ 'ezembed', 'ezlink', 'ezcustomtag', 'ezliteral', 'eztable' ].every( b => r.buttons.includes( b ) ), 'ezoe buttons in the toolbar', r.buttons.join( ' ' ) );
        } );

        await t.step( 'engine switch keeps the text', async () => {
            const button = '[name="CustomActionButton[' + s.attributeId + '_switch_engine_tinymce3]"]';
            if ( !( await s.page.$( button ) ) )
                return t.skip( 'ezoe.ini [EditorSettings] EngineSwitch is disabled' );
            const mark = 'SWITCH-' + Date.now();
            await s.clickIn( 'p', { index: 1, at: 'end' } );
            await s.page.keyboard.press( 'End' );
            await s.page.keyboard.type( ' ' + mark );
            await Promise.all( [ s.page.waitForNavigation(), s.page.evaluate( b => document.querySelector( b ).click(), button ) ] );
            await s.waitForEditors( 'tinymce3' );
            t.check( await s.page.evaluate( ( id, m ) => tinyMCE.get( id ).getContent().includes( m ), s.editorId, mark ), 'TinyMCE 3 shows the changed text' );
            await Promise.all( [ s.page.waitForNavigation(), s.page.evaluate( a => document.querySelector( '[name="CustomActionButton[' + a + '_switch_engine_tinymce8]"]' ).click(), s.attributeId ) ] );
            await s.waitForEditors( 'tinymce8' );
            t.check( await s.evalEditor( ( e, m ) => e.getContent().includes( m ), mark ), 'back in TinyMCE 8 with the text' );
        } );
    }
};

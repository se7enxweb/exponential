// Browser session for the TinyMCE 8 tests: login, drafts, editor and dialog helpers.
// All interaction with the editor and the dialogs uses real mouse clicks and key presses.
'use strict';

const fs = require( 'fs' );
const { execFileSync } = require( 'child_process' );
const puppeteer = require( 'puppeteer-core' );
const env = require( './env' );

const sleep = ms => new Promise( r => setTimeout( r, ms ) );

class Session
{
    constructor( browser, page )
    {
        this.browser = browser;
        this.page = page;
        this.editorId = null;
        this.version = null;
        this.logs = [];
        this.uploaded = [];
        page.on( 'dialog', d => d.accept() );
        // the current test step, set by run.js, so browser errors can be attributed
        this.currentStep = '';
        page.on( 'pageerror', e => this.logs.push( 'pageerror' + ( this.currentStep ? ' [' + this.currentStep + ']' : '' ) + ': ' + ( e.message || e ) ) );
        page.on( 'console', m => {
            if ( /warn|error/.test( m.type() ) && !/JQMIGRATE|TagsStructureMenu|Base'|lacks a "sizes"|Failed to load resource/.test( m.text() ) )
                this.logs.push( 'console.' + m.type() + ( this.currentStep ? ' [' + this.currentStep + ']' : '' ) + ': ' + m.text() );
        } );
        // objects created by uploads, removed again by cleanup()
        page.on( 'response', async r => {
            if ( /\/ezoe\/upload\/.*\/auto\/1/.test( r.url() ) && r.request().method() === 'POST' )
            {
                try
                {
                    const m = ( await r.text() ).match( /selectByEmbedId\(\s*(\d+)\s*,\s*(\d+)/ );
                    if ( m )
                        this.uploaded.push( { objectId: m[1], nodeId: m[2] } );
                }
                catch ( e ) {}
            }
        } );
    }

    static async start()
    {
        const options = { executablePath: env.browserPath, headless: env.headless };
        if ( env.browser === 'firefox' )
            Object.assign( options, { browser: 'firefox', defaultViewport: null, args: [ '--width=1400', '--height=1100' ] } );
        else
            Object.assign( options, { defaultViewport: { width: 1400, height: 1100 }, args: [ '--no-sandbox' ] } );
        const browser = await puppeteer.launch( options );
        const session = new Session( browser, await browser.newPage() );
        await session.login();
        return session;
    }

    async login()
    {
        const page = this.page;
        await page.goto( env.baseUrl + '/user/login' );
        await page.type( 'input[name=Login]', env.user );
        await page.type( 'input[name=Password]', env.password() );
        await Promise.all( [ page.waitForNavigation(), page.click( 'input[name=LoginButton]' ) ] );
    }

    async setEngine( engine )
    {
        await this.page.goto( env.baseUrl + '/user/preferences/set/ezoe_engine/' + engine );
    }

    // Opens a new draft of the object (never an existing draft of somebody) and waits for the editor
    async openDraft( { engine = 'tinymce8', objectId = env.objectId, language = env.language } = {} )
    {
        const page = this.page;
        await this.setEngine( engine );
        await page.goto( env.baseUrl + '/content/edit/' + objectId + '/f/' + language );
        if ( await page.$( 'input[name=NewDraftButton]' ) )
        {
            // edit conflict page: create a new draft and edit the newest version
            await Promise.all( [ page.waitForNavigation(), page.evaluate( () => document.querySelector( 'input[name=NewDraftButton]' ).click() ) ] );
            if ( await page.$( 'input[name=SelectedVersion]' ) )
            {
                await page.evaluate( () => {
                    const radios = [ ...document.querySelectorAll( 'input[name=SelectedVersion]:not([disabled])' ) ];
                    radios.sort( ( a, b ) => parseInt( b.value, 10 ) - parseInt( a.value, 10 ) )[0].checked = true;
                } );
                await Promise.all( [ page.waitForNavigation(), page.evaluate( () => document.querySelector( 'input[name=EditButton]' ).click() ) ] );
            }
        }
        const m = page.url().match( /\/edit\/(\d+)\/(\d+)\// );
        if ( !m )
            throw new Error( 'no edit page: ' + page.url() );
        this.objectId = m[1];
        this.version = m[2];
        await this.waitForEditors( engine );
        if ( engine === 'tinymce8' )
            this.editorId = await page.evaluate( () => tinymce.get().slice().sort( ( a, b ) => b.getContent().length - a.getContent().length )[0].id );
        else
            this.editorId = await page.evaluate( () => Object.values( tinymce.editors ).sort( ( a, b ) => b.getContent().length - a.getContent().length )[0].id );
        return this;
    }

    async waitForEditors( engine = 'tinymce8' )
    {
        await this.page.waitForFunction( e => e === 'tinymce8'
            ? window.tinymce && tinymce.majorVersion === '8' && tinymce.get().length && tinymce.get().every( x => x.initialized )
            : window.tinymce && tinymce.majorVersion === '3' && Object.keys( tinymce.editors ).length && Object.values( tinymce.editors ).every( x => x.initialized ),
            { timeout: 30000 }, engine );
        await sleep( 500 );
    }

    // attribute id of the edited xml field, e.g. 539 for ContentObjectAttribute_data_text_539
    get attributeId()
    {
        return this.editorId.replace( /^\D+_/, '' );
    }

    async store()
    {
        await Promise.all( [ this.page.waitForNavigation(), this.page.evaluate( () => document.querySelector( 'input[name=StoreButton]' ).click() ) ] );
        const messages = await this.page.evaluate( () => [ ...document.querySelectorAll( '.message-warning, .message-error' ) ].map( n => n.innerText.trim() ) );
        await this.waitForEditors( await this.page.evaluate( () => window.tinymce ? tinymce.majorVersion === '8' ? 'tinymce8' : 'tinymce3' : 'tinymce8' ) );
        return messages;
    }

    async discard()
    {
        if ( await this.page.$( 'input[name=DiscardButton]' ) )
            await Promise.all( [ this.page.waitForNavigation(), this.page.evaluate( () => document.querySelector( 'input[name=DiscardButton]' ).click() ) ] ).catch( () => {} );
    }

    // ezxml of the edited attribute in the current draft (needs EZ_DB)
    storedXml( attributeId = this.attributeId, version = this.version )
    {
        if ( !env.db )
            return null;
        return execFileSync( 'sqlite3', [ env.db, `select data_text from ezcontentobject_attribute where id=${parseInt( attributeId, 10 )} and version=${parseInt( version, 10 )}` ] ).toString();
    }

    // ---- editor

    async setContent( html )
    {
        await this.page.evaluate( ( id, h ) => { tinymce.get( id ).setContent( h ); tinymce.get( id ).undoManager.clear(); }, this.editorId, html );
        await sleep( 300 );
    }

    evalEditor( fn, ...args )
    {
        return this.page.evaluate( new Function( 'id', '...args', 'const editor = tinymce.get(id); return (' + fn + ')(editor, ...args);' ), this.editorId, ...args );
    }

    async scrollEditorIntoView()
    {
        await this.page.evaluate( id => { document.querySelector( '#' + id + ' + .tox-tinymce' ).scrollIntoView( { block: 'start' } ); window.scrollBy( 0, -120 ); }, this.editorId );
        await sleep( 200 );
    }

    // position of an element inside the editor iframe in page coordinates
    async editorBox( selector, index = 0 )
    {
        const r = await this.page.evaluate( ( id, s, i ) => {
            const el = tinymce.get( id ).getBody().querySelectorAll( s )[i];
            if ( !el )
                return null;
            el.scrollIntoView( { block: 'center' } );
            const b = el.getBoundingClientRect();
            return { x: b.x, y: b.y, w: b.width, h: b.height };
        }, this.editorId, selector, index );
        if ( !r )
            throw new Error( 'not in the editor: ' + selector + '[' + index + ']' );
        const f = await ( await this.page.$( '#' + this.editorId + '_ifr' ) ).boundingBox();
        return { x: f.x + r.x, y: f.y + r.y, w: r.w, h: r.h };
    }

    async clickIn( selector, { index = 0, at = 'start', button = 'left' } = {} )
    {
        const b = await this.editorBox( selector, index );
        await this.page.mouse.click( at === 'end' ? b.x + b.w - 2 : b.x + Math.min( b.w / 2, 25 ), b.y + Math.min( b.h / 2, 9 ), { button } );
        await sleep( 400 );
    }

    // a real double click: two clicks within a short time
    async doubleClickIn( selector, index = 0 )
    {
        const b = await this.editorBox( selector, index );
        await this.page.mouse.click( b.x + 20, b.y + Math.min( b.h / 2, 9 ) );
        await sleep( 120 );
        await this.page.mouse.click( b.x + 20, b.y + Math.min( b.h / 2, 9 ) );
        await sleep( 500 );
    }

    // types a word at the end of an element and selects it with the keyboard
    async typeAndSelect( selector, index, word )
    {
        await this.clickIn( selector, { index, at: 'end' } );
        await this.page.keyboard.press( 'End' );
        await this.page.keyboard.type( ' ' + word );
        await this.selectWordLeft();
    }

    async selectWordLeft()
    {
        const k = this.page.keyboard;
        await k.down( 'Shift' ); await k.down( 'Control' ); await k.press( 'ArrowLeft' ); await k.up( 'Control' ); await k.up( 'Shift' );
        await sleep( 300 );
    }

    async clickToolbar( name )
    {
        await this.scrollEditorIntoView();
        await this.page.click( '#' + this.editorId + ' + .tox-tinymce .tox-toolbar [data-mce-name="' + name + '"]' );
        await sleep( 600 );
    }

    pathItems()
    {
        return this.page.evaluate( id => [ ...document.querySelectorAll( '#' + id + ' + .tox-tinymce .tox-statusbar__path-item' ) ].map( i => i.textContent.trim() ), this.editorId );
    }

    async clickPath( pattern )
    {
        const item = await this.page.evaluateHandle( ( id, p ) => [ ...document.querySelectorAll( '#' + id + ' + .tox-tinymce .tox-statusbar__path-item' ) ].find( i => new RegExp( p ).test( i.textContent.trim() ) ), this.editorId, pattern );
        if ( !item.asElement() )
            throw new Error( 'no path item ' + pattern + ' in ' + ( await this.pathItems() ).join( ' » ' ) );
        await item.asElement().click();
        await this.dialogOpen();
    }

    // ---- dialogs

    async dialogOpen()
    {
        await this.page.waitForSelector( '.tox-dialog', { timeout: 10000 } );
        await sleep( 900 );
    }

    async dialogClosed()
    {
        await this.page.waitForFunction( () => !document.querySelector( '.tox-dialog' ), { timeout: 20000 } );
        await sleep( 500 );
    }

    async idle()
    {
        await this.page.waitForFunction( () => !document.querySelector( '.tox-dialog .tox-dialog__busy-spinner' ), { timeout: 30000 } );
        await sleep( 600 );
    }

    dialogTitle()
    {
        return this.page.evaluate( () => ( document.querySelector( '.tox-dialog__title' ) || {} ).textContent || null );
    }

    dialogText( selector )
    {
        return this.page.evaluate( s => [ ...document.querySelectorAll( '.tox-dialog ' + s ) ].map( n => n.innerText.trim() ).join( ' | ' ), selector );
    }

    async clickTab( name )
    {
        const h = await this.page.evaluateHandle( n => [ ...document.querySelectorAll( '.tox-dialog__body-nav-item' ) ].find( b => new RegExp( '^(' + n + ')$' ).test( b.textContent.trim() ) ), name );
        if ( !h.asElement() )
            throw new Error( 'no tab ' + name );
        await h.asElement().click();
        await sleep( 500 );
        await this.idle();
    }

    async clickButton( pattern )
    {
        const h = await this.page.evaluateHandle( p => [ ...document.querySelectorAll( '.tox-dialog button' ) ].find( b => new RegExp( p ).test( b.textContent.trim() ) && b.offsetParent ), pattern );
        if ( !h.asElement() )
            throw new Error( 'no button ' + pattern );
        await h.asElement().click();
        await sleep( 500 );
    }

    ok() { return this.clickButton( '^OK$' ); }
    cancel() { return this.clickButton( '^(Cancel|Abbrechen)$' ); }

    field( label )
    {
        return this.page.evaluateHandle( l => [ ...document.querySelectorAll( '.tox-dialog .tox-form__group' ) ].find( g => {
            const lb = g.querySelector( ':scope > .tox-label' );
            return lb && new RegExp( '^(' + l + ')( \\*)?$' ).test( lb.textContent.trim() ) && g.offsetParent;
        } ), label );
    }

    async pick( label, option )
    {
        const g = ( await this.field( label ) ).asElement();
        if ( !g )
            throw new Error( 'no field ' + label );
        await ( await g.$( '.tox-listbox' ) ).click();
        await sleep( 400 );
        const it = await this.page.evaluateHandle( o => [ ...document.querySelectorAll( '.tox-menu .tox-collection__item' ) ].find( i => new RegExp( '^(' + o + ')$' ).test( i.textContent.trim() ) ), option );
        if ( !it.asElement() )
            throw new Error( 'no option ' + option + ' in ' + label + ': ' + await this.page.evaluate( () => [ ...document.querySelectorAll( '.tox-menu .tox-collection__item' ) ].map( i => i.textContent.trim() ).join( ' | ' ) ) );
        await it.asElement().click();
        await sleep( 400 );
    }

    async options( label )
    {
        const g = ( await this.field( label ) ).asElement();
        if ( !g )
            throw new Error( 'no field ' + label );
        await ( await g.$( '.tox-listbox' ) ).click();
        await sleep( 400 );
        const items = await this.page.evaluate( () => [ ...document.querySelectorAll( '.tox-menu .tox-collection__item' ) ].map( i => i.textContent.trim() ) );
        await this.page.keyboard.press( 'Escape' );
        await sleep( 300 );
        return items;
    }

    async type( label, text )
    {
        const g = ( await this.field( label ) ).asElement();
        if ( !g )
            throw new Error( 'no field ' + label );
        await ( await g.$( 'input, textarea' ) ).click();
        const k = this.page.keyboard;
        await k.down( 'Control' ); await k.press( 'a' ); await k.up( 'Control' ); await k.press( 'Backspace' );
        await k.type( text );
    }

    async checkbox( label )
    {
        const h = await this.page.evaluateHandle( l => [ ...document.querySelectorAll( '.tox-dialog .tox-checkbox' ) ].find( c => new RegExp( '^(' + l + ')$' ).test( c.textContent.trim() ) && c.offsetParent ), label );
        if ( !h.asElement() )
            throw new Error( 'no checkbox ' + label );
        await h.asElement().click();
        await sleep( 300 );
    }

    // label=value of all visible fields of the dialog
    fields()
    {
        return this.page.evaluate( () => {
            const result = {};
            [ ...document.querySelectorAll( '.tox-dialog .tox-dialog__body-content .tox-form__group' ) ].filter( g => g.offsetParent ).forEach( g => {
                const l = g.querySelector( ':scope > .tox-label' ), lb = g.querySelector( '.tox-listbox' ), inp = g.querySelector( 'input.tox-textfield, textarea' ), cb = g.querySelector( '.tox-checkbox input' );
                if ( l )
                    result[ l.textContent.trim().replace( / \*$/, '' ) ] = lb ? lb.textContent.trim() : inp ? inp.value : '';
                else if ( cb )
                    result[ g.textContent.trim() ] = cb.checked;
            } );
            return result;
        } );
    }

    // rows of a content list (search, browse, bookmarks)
    listRows()
    {
        return this.page.evaluate( () => [ ...document.querySelectorAll( '.tox-dialog table.ezoe-list tr' ) ].map( r => r.innerText.replace( /\s+/g, ' ' ).trim() ) );
    }

    async clickRow( index = 0, column = 'name' )
    {
        const cell = await this.page.$( '.tox-dialog table.ezoe-list tr:nth-child(' + ( index + 1 ) + ') td.ezoe-list-' + column );
        if ( !cell )
            throw new Error( 'no list row ' + index );
        await cell.click();
        await this.idle();
        await sleep( 800 );
    }

    // clicks an option of the class filter of the search with the mouse
    async pickClassFilter( name )
    {
        await this.page.evaluate( n => {
            const s = document.querySelector( '.tox-dialog select.ezoe-class-filter' ), o = [ ...s.options ].find( x => x.textContent === n );
            s.scrollTop = o.offsetTop - s.offsetTop - 10;
        }, name );
        const p = await this.page.evaluate( n => {
            const s = document.querySelector( '.tox-dialog select.ezoe-class-filter' ), opts = [ ...s.options ], o = opts.find( x => x.textContent === n ), r = s.getBoundingClientRect(), h = s.scrollHeight / opts.length;
            return { x: r.x + 30, y: r.y + ( opts.indexOf( o ) * h - s.scrollTop ) + h / 2 };
        }, name );
        await this.page.mouse.click( p.x, p.y );
        await sleep( 300 );
    }

    classFilterSelection()
    {
        return this.page.evaluate( () => [ ...document.querySelectorAll( '.tox-dialog select.ezoe-class-filter option' ) ].filter( o => o.selected ).map( o => o.textContent ) );
    }

    notification()
    {
        return this.page.evaluate( () => [ ...document.querySelectorAll( '.tox-notification__body' ) ].map( n => n.textContent.trim() ).pop() || null );
    }

    contextToolbar()
    {
        return this.page.evaluate( () => [ ...document.querySelectorAll( '.tox-pop .tox-tbtn' ) ].map( b => b.getAttribute( 'aria-label' ) ) );
    }

    async clickContextToolbar( pattern )
    {
        const h = await this.page.evaluateHandle( p => [ ...document.querySelectorAll( '.tox-pop .tox-tbtn' ) ].find( b => new RegExp( p ).test( b.getAttribute( 'aria-label' ) ) ), pattern );
        if ( !h.asElement() )
            throw new Error( 'no context toolbar button ' + pattern );
        await h.asElement().click();
        await sleep( 500 );
    }

    async screenshot( name )
    {
        fs.mkdirSync( env.screenshots, { recursive: true } );
        await this.page.screenshot( { path: env.screenshots + '/' + name.replace( /\W+/g, '_' ) + '.png' } );
    }

    // ---- cleanup

    // removes objects created by uploads through the admin interface, without moving them to the trash
    async removeUploads()
    {
        const page = this.page;
        for ( const o of this.uploaded.splice( 0 ) )
        {
            await page.goto( env.baseUrl + '/content/view/full/' + o.nodeId );
            const clicked = await page.evaluate( oid => {
                const form = [ ...document.querySelectorAll( 'form' ) ].find( f => f.querySelector( 'input[name=ActionRemove][type=submit]' ) && ( f.querySelector( 'input[name=ContentObjectID]' ) || {} ).value === oid );
                if ( !form )
                    return false;
                form.querySelector( 'input[name=ActionRemove][type=submit]' ).click();
                return true;
            }, o.objectId );
            if ( !clicked )
            {
                this.logs.push( 'cleanup: no remove button for object ' + o.objectId );
                continue;
            }
            await page.waitForNavigation().catch( () => {} );
            // the checkbox, not the hidden SupportsMoveToTrash field
            await page.evaluate( () => { const c = document.querySelector( 'input[name=MoveToTrash][type=checkbox]' ); if ( c ) c.checked = false; } );
            if ( await page.$( 'input[name=ConfirmButton]' ) )
                await Promise.all( [ page.waitForNavigation(), page.evaluate( () => document.querySelector( 'input[name=ConfirmButton]' ).click() ) ] );
            if ( env.db )
            {
                const left = execFileSync( 'sqlite3', [ env.db, 'select count(*) from ezcontentobject where id=' + parseInt( o.objectId, 10 ) ] ).toString().trim();
                if ( left !== '0' )
                    this.logs.push( 'cleanup: object ' + o.objectId + ' could not be removed' );
            }
        }
    }

    async close()
    {
        await this.removeUploads().catch( e => this.logs.push( 'cleanup: ' + e.message ) );
        if ( env.db )
        {
            // the tests set the ezoe_engine preference, the configured engine applies again afterwards
            try { execFileSync( 'sqlite3', [ env.db, "delete from ezpreferences where name='ezoe_engine' and user_id=(select contentobject_id from ezuser where login='" + env.user.replace( /'/g, "''" ) + "')" ] ); }
            catch ( e ) {}
        }
        await this.browser.close();
    }
}

// position and context of the first difference of two strings, for failed comparisons
const firstDifference = ( a = '', b = '' ) => {
    a = a || ''; b = b || '';
    let i = 0;
    while ( i < a.length && a[i] === b[i] )
        i++;
    return 'first: …' + a.slice( Math.max( 0, i - 60 ), i + 100 ) + '\n      then:  …' + b.slice( Math.max( 0, i - 60 ), i + 100 );
};

// ezxml for comparisons: eZOEInputParser leaves xmlns:tmp declarations of its temporary namespace on
// elements it creates while normalizing (e.g. paragraphs in table cells), they disappear on the next save
const normalizeXml = xml => ( xml || '' ).replace( / xmlns:tmp="[^"]*"/g, '' );

module.exports = { Session, sleep, firstDifference, normalizeXml };

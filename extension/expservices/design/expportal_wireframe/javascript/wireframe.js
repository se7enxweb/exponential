// Wireframe viewer: each screen is a list of blocks; a block names the services that feed it.
// "Notes" shows the annotations; when the catalogue is readable each service is marked present or missing.
const root = document.getElementById( 'wf' );
const BASE = root.dataset.services.replace( /\/$/, '' );

// kind: nav | hero | cards | list | text | form | table | search | tabs | pager | buttons
const SCREENS = {
    home: { title: 'Home', desc: 'Entry page: teasers of every area.', blocks: [
        { k: 'nav', label: 'Navigation', svc: [ 'expsession::whoami' ], note: 'Signed-in user decides Sign in / Profile.' },
        { k: 'hero', label: 'Hero', svc: [], note: 'Static text.' },
        { k: 'cards', label: 'Latest news (4 cards)', svc: [ 'expnode::children' ], note: 'children of the news node, limit 4.' },
        { k: 'cards', label: 'Media strip (4 cards)', svc: [ 'expmedia::list' ], note: 'offset 0, limit 4.' } ] },
    news: { title: 'News', desc: 'Paged list of articles.', blocks: [
        { k: 'nav', label: 'Navigation', svc: [ 'expsession::whoami' ] },
        { k: 'cards', label: 'Article cards', svc: [ 'expnode::children' ], note: 'Paged: offset, limit; envelope meta.total drives the pager.' },
        { k: 'pager', label: 'Pager', svc: [], note: 'From meta.total / offset / limit.' } ] },
    article: { title: 'Article', desc: 'One article.', blocks: [
        { k: 'text', label: 'Title, image, body', svc: [ 'expnode::get' ], note: 'Node id from the route.' },
        { k: 'buttons', label: 'Back to news', svc: [] } ] },
    shop: { title: 'Shop', desc: 'Products and the add-to-basket action.', blocks: [
        { k: 'cards', label: 'Product cards', svc: [ 'expnode::children' ], note: 'Class filter product.' },
        { k: 'buttons', label: 'Add to basket (per card)', svc: [ 'expbasket::add' ], note: 'WRITE: POST + form token.' },
        { k: 'pager', label: 'Pager', svc: [] } ] },
    basket: { title: 'Basket', desc: 'Lines, totals, removal.', blocks: [
        { k: 'table', label: 'Basket lines', svc: [ 'expbasket::get' ] },
        { k: 'buttons', label: 'Remove line', svc: [ 'expbasket::remove' ], note: 'WRITE: POST + form token.' } ] },
    forums: { title: 'Forums', desc: 'Forum list, topics, replies.', blocks: [
        { k: 'list', label: 'Forum list', svc: [ 'expforum::forums' ] } ] },
    topics: { title: 'Forum topics', desc: 'Topics of a forum.', blocks: [
        { k: 'list', label: 'Topic list', svc: [ 'expforum::topics' ], note: 'Paged.' },
        { k: 'pager', label: 'Pager', svc: [] } ] },
    topic: { title: 'Topic', desc: 'Replies and the reply form.', blocks: [
        { k: 'list', label: 'Replies', svc: [ 'expforum::replies' ], note: 'Paged.' },
        { k: 'form', label: 'Reply form', svc: [ 'expforum::reply' ], note: 'WRITE: POST + form token; needs login (401 otherwise).' } ] },
    media: { title: 'Media', desc: 'Gallery.', blocks: [
        { k: 'cards', label: 'Media tiles', svc: [ 'expmedia::list' ], note: 'Paged.' },
        { k: 'pager', label: 'Pager', svc: [] } ] },
    feeds: { title: 'Feeds', desc: 'Available feeds.', blocks: [
        { k: 'list', label: 'Feed list', svc: [ 'expfeed::list' ] } ] },
    search: { title: 'Search', desc: 'Full text search.', blocks: [
        { k: 'search', label: 'Search box', svc: [] },
        { k: 'cards', label: 'Results', svc: [ 'expsearch::search' ], note: 'Paged; empty result is a message, not an error.' },
        { k: 'pager', label: 'Pager', svc: [] } ] },
    login: { title: 'Sign in', desc: 'Session login for browsers and remote clients.', blocks: [
        { k: 'form', label: 'Login form', svc: [ 'expsession::login', 'expsession::token' ], note: 'WRITE: POST + form token; the session cookie carries the login.' } ] },
    profile: { title: 'Profile', desc: 'The signed-in user.', blocks: [
        { k: 'text', label: 'Name, e-mail', svc: [ 'expsession::whoami' ] },
        { k: 'buttons', label: 'Sign out', svc: [ 'expsession::logout' ], note: 'WRITE.' } ] }
};

const el = ( tag, cls, text ) => { const e = document.createElement( tag ); if ( cls ) e.className = cls; if ( text ) e.textContent = text; return e; };
let catalog = null; // set of "domain::method" names when readable

function lines( n ) { const f = document.createDocumentFragment(); for ( let i = 0; i < n; i++ ) f.append( el( 'div', 'wf-line' + ( i === n - 1 ? ' s' : '' ) ) ); return f; }

function body( k, box ) {
    switch ( k ) {
        case 'nav': { const n = el( 'div', 'wf-nav' ); [ 'Logo', 'News', 'Shop', 'Forums', 'Media', 'Feeds', 'Search', 'Sign in' ].forEach( t => n.append( el( 'span', '', t ) ) ); box.append( n ); break; }
        case 'hero': box.append( el( 'div', 'wf-img' ), lines( 2 ) ); break;
        case 'cards': { const r = el( 'div', 'wf-row' ); for ( let i = 0; i < 4; i++ ) { const c = el( 'div', 'wf-box' ); c.append( el( 'div', 'wf-img' ), lines( 2 ) ); r.append( c ); } box.append( r ); break; }
        case 'list': for ( let i = 0; i < 4; i++ ) { const c = el( 'div', 'wf-box' ); c.append( lines( 1 ) ); box.append( c ); } break;
        case 'text': box.append( el( 'div', 'wf-img' ), lines( 4 ) ); break;
        case 'form': box.append( el( 'div', 'wf-field', 'field' ), el( 'div', 'wf-field', 'field' ), el( 'span', 'wf-btn', 'Submit' ) ); break;
        case 'table': for ( let i = 0; i < 3; i++ ) { const c = el( 'div', 'wf-box' ); c.append( lines( 1 ) ); box.append( c ); } break;
        case 'search': box.append( el( 'div', 'wf-field', 'Search' ), el( 'span', 'wf-btn', 'Go' ) ); break;
        case 'pager': box.append( el( 'span', 'wf-btn', '<' ), ' 1 / n ', el( 'span', 'wf-btn', '>' ) ); break;
        case 'buttons': box.append( el( 'span', 'wf-btn', 'Button' ) ); break;
    }
}

function svcNote( b ) {
    const n = el( 'div', 'wf-note' );
    if ( b.svc.length ) {
        n.append( el( 'b', '', 'Service: ' ) );
        b.svc.forEach( ( s, i ) => {
            if ( i ) n.append( ', ' );
            const m = el( 'span', catalog ? ( catalog.has( s ) ? 'ok' : 'miss' ) : '', s );
            m.dataset.svc = s; m.title = catalog ? ( catalog.has( s ) ? 'in the catalogue' : 'not in the catalogue' ) : 'catalogue not checked';
            n.append( m );
        } );
    } else n.append( el( 'b', '', 'No service' ) );
    if ( b.note ) n.append( ' | ' + b.note );
    return n;
}

function draw() {
    const route = ( location.hash.replace( /^#\/?/, '' ) || 'home' ), name = SCREENS[ route ] ? route : 'home';
    const s = SCREENS[ name ];
    const phone = root.dataset.phone === '1', notes = root.dataset.notes !== '0';
    root.replaceChildren();
    const bar = el( 'div', 'wf-bar' );
    Object.keys( SCREENS ).forEach( k => { const a = el( 'a', k === name ? 'on' : '', SCREENS[ k ].title ); a.href = '#/' + k; bar.append( a ); } );
    bar.append( el( 'span', 'sp' ) );
    const bp = el( 'button', '', phone ? 'Desktop' : 'Phone' ); bp.onclick = () => { root.dataset.phone = phone ? '0' : '1'; draw(); };
    const bn = el( 'button', '', notes ? 'Hide notes' : 'Show notes' ); bn.onclick = () => { root.dataset.notes = notes ? '0' : '1'; draw(); };
    bar.append( bp, bn );
    const stage = el( 'div', 'wf-stage' + ( notes ? ' notes' : '' ) ), dev = el( 'div', 'wf-device' + ( phone ? ' phone' : '' ) );
    dev.dataset.screen = name;
    dev.append( el( 'h2', '', s.title ), el( 'p', 'wf-desc', s.desc ) );
    s.blocks.forEach( b => { const box = el( 'div', 'wf-box' ); box.append( el( 'div', 'wf-label', b.label ) ); body( b.k, box ); box.append( svcNote( b ) ); dev.append( box ); } );
    stage.append( dev ); root.append( bar, stage );
}

async function loadCatalog() {
    try {
        const r = await fetch( BASE + '/expservices::catalog?ContentType=json', { credentials: 'same-origin', headers: { Accept: 'application/json' } } );
        const j = await r.json(), env = j.content || j, d = env.data || {};
        const list = Array.isArray( d ) ? d : ( d.services || [] );
        catalog = new Set( list.map( x => ( x.domain && x.domain.indexOf( 'exp' ) === 0 ? x.domain : 'exp' + x.domain ) + '::' + x.method ) );
        if ( !catalog.size ) catalog = null;
    } catch ( e ) { catalog = null; }
    draw();
}

root.dataset.phone = '0'; root.dataset.notes = '1';
window.addEventListener( 'hashchange', draw );
draw(); loadCatalog();

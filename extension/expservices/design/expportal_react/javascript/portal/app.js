// The portal shell: hash routing, navigation, the signed-in user, one component per page.
import { React, h, Navbar, Nav, Container } from './ui.js';
import * as api from './api.js';
import Home from './pages/home.js';
import News from './pages/news.js';
import Shop from './pages/shop.js';
import Forums, { Topic } from './pages/forums.js';
import Media from './pages/media.js';
import Feeds from './pages/feeds.js';
import Search from './pages/search.js';
import Login, { Profile } from './pages/login.js';

const NAV = [ [ 'news', 'News' ], [ 'shop', 'Shop' ], [ 'forums', 'Forums' ], [ 'media', 'Media' ], [ 'feeds', 'Feeds' ], [ 'search', 'Search' ] ];

function parse() {
    const parts = ( location.hash.replace( /^#\/?/, '' ) || 'home' ).split( '/' );
    return { page: parts[ 0 ] || 'home', id: parts[ 1 ] };
}

function App() {
    const [ route, setRoute ] = React.useState( parse() );
    const [ user, setUser ] = React.useState( null );
    React.useEffect( () => {
        const on = () => setRoute( parse() );
        window.addEventListener( 'hashchange', on );
        api.whoami().then( u => setUser( u && ( u.logged_in === false || u.anonymous ) ? null : u ), () => setUser( null ) );
        return () => window.removeEventListener( 'hashchange', on );
    }, [] );
    const { page, id } = route;
    let view;
    switch ( page ) {
        case 'news': view = h( News, { id } ); break;
        case 'shop': view = h( Shop, { id } ); break;
        case 'forums': view = h( Forums, { id } ); break;
        case 'topic': view = h( Topic, { id } ); break;
        case 'media': view = h( Media ); break;
        case 'feeds': view = h( Feeds ); break;
        case 'search': view = h( Search, { id } ); break;
        case 'login': view = h( Login, { user, onUser: setUser } ); break;
        case 'profile': view = h( Profile, { user, onUser: setUser } ); break;
        default: view = h( Home );
    }
    return h( React.Fragment, null,
        h( Navbar, { bg: 'dark', variant: 'dark', expand: 'md', className: 'mb-4' }, h( Container, null,
            h( Navbar.Brand, { href: '#/' }, 'Exponential' ), h( Navbar.Toggle, { 'aria-controls': 'portal-nav' } ),
            h( Navbar.Collapse, { id: 'portal-nav' },
                h( Nav, { className: 'me-auto', activeKey: page }, NAV.map( ( [ k, t ] ) => h( Nav.Link, { key: k, href: '#/' + k, eventKey: k }, t ) ) ),
                h( Nav, null, user ? h( Nav.Link, { href: '#/profile', 'data-user': 1 }, user.name || user.login || 'Profile' ) : h( Nav.Link, { href: '#/login' }, 'Sign in' ) ) ) ) ),
        h( Container, { as: 'main', className: 'pb-5' }, view ) );
}

ReactDOM.createRoot( document.getElementById( 'root' ) ).render( h( App ) );

// Shared UI helpers: createElement shortcut, the React Bootstrap kit, an async data hook, pager, state boxes.
export const React = window.React;
export const RB = window.ReactBootstrap;
export const h = React.createElement;
export const { Navbar, Nav, Container, Row, Col, Card, Button, Form, Alert, Spinner, ListGroup, Badge, Pagination, Table } = RB;

// useAsync( fn, deps ) -> { loading, error, data, reload }
export function useAsync( fn, deps ) {
    const [ state, setState ] = React.useState( { loading: true, error: null, data: null } );
    const [ tick, setTick ] = React.useState( 0 );
    React.useEffect( () => {
        let live = true;
        setState( s => ( { loading: true, error: null, data: s.data } ) );
        Promise.resolve().then( fn ).then(
            data => live && setState( { loading: false, error: null, data } ),
            error => live && setState( { loading: false, error, data: null } ) );
        return () => { live = false; };
    }, [ ...( deps || [] ), tick ] );
    return Object.assign( {}, state, { reload: () => setTick( t => t + 1 ) } );
}

export function Loading() {
    return h( 'div', { className: 'py-4 text-center', 'data-state': 'loading' }, h( Spinner, { animation: 'border', size: 'sm' } ), ' Loading' );
}

export function ErrorBox( { error } ) {
    const code = error && error.code ? ' (' + error.code + ')' : '';
    return h( Alert, { variant: error && error.code === 401 ? 'warning' : 'danger', 'data-state': 'error' },
        error && error.code === 401
            ? [ 'Please ', h( 'a', { key: 'l', href: '#/login' }, 'sign in' ), ' to see this.' ]
            : ( error && error.message ? error.message : 'Error' ) + code );
}

// Renders loading / error / content for a useAsync result.
export function Async( { res, children } ) {
    if ( res.error ) return h( ErrorBox, { error: res.error } );
    if ( res.loading && !res.data ) return h( Loading );
    return children( res.data );
}

export function Pager( { total, offset, limit, onPage } ) {
    const pages = Math.max( 1, Math.ceil( total / limit ) );
    if ( pages < 2 ) return null;
    const cur = Math.floor( offset / limit );
    return h( Pagination, { size: 'sm', className: 'mt-3' },
        h( Pagination.Prev, { disabled: cur === 0, onClick: () => onPage( ( cur - 1 ) * limit ) } ),
        h( Pagination.Item, { active: true }, ( cur + 1 ) + ' / ' + pages ),
        h( Pagination.Next, { disabled: cur >= pages - 1, onClick: () => onPage( ( cur + 1 ) * limit ) } ) );
}

export function Title( { children, note } ) {
    return h( 'div', { className: 'mb-3' }, h( 'h1', { className: 'h3 mb-0' }, children ),
        note ? h( 'small', { className: 'portal-muted' }, note ) : null );
}

export function ItemCard( { item, href } ) {
    return h( Card, { className: 'h-100', 'data-item': item.id },
        item.image ? h( Card.Img, { variant: 'top', src: item.image, className: 'portal-card-img', alt: '' } ) : null,
        h( Card.Body, null,
            h( Card.Title, { className: 'h6' }, href ? h( 'a', { href }, item.name ) : item.name ),
            item.intro ? h( Card.Text, { className: 'portal-muted small' }, item.intro ) : null ) );
}

import { React, h, useAsync, Async, Title, ItemCard, Pager, Row, Col, Form, Button } from '../ui.js';
import * as api from '../api.js';

export default function Search( { id } ) {
    const q = id ? decodeURIComponent( id ) : '';
    const [ text, setText ] = React.useState( q );
    const [ offset, setOffset ] = React.useState( 0 );
    React.useEffect( () => { setText( q ); setOffset( 0 ); }, [ q ] );
    const res = useAsync( () => q ? api.search( q, offset, api.pageSize ) : Promise.resolve( { items: [], total: 0, offset: 0 } ), [ q, offset ] );
    return h( 'div', { 'data-page': 'search' }, h( Title, null, 'Search' ),
        h( Form, { className: 'd-flex gap-2 mb-3', onSubmit: e => { e.preventDefault(); location.hash = '#/search/' + encodeURIComponent( text ); } },
            h( Form.Control, { value: text, onChange: e => setText( e.target.value ), placeholder: 'Search', 'aria-label': 'Search' } ), h( Button, { type: 'submit' }, 'Search' ) ),
        h( Async, { res }, d => h( 'div', null,
            q && !d.items.length ? h( 'p', { className: 'portal-muted' }, 'Nothing found.' ) : null,
            h( Row, { xs: 1, sm: 2, lg: 3, className: 'g-3' }, d.items.map( i => h( Col, { key: i.id }, h( ItemCard, { item: i, href: '#/news/' + i.id } ) ) ) ),
            h( Pager, { total: d.total, offset: d.offset, limit: api.pageSize, onPage: setOffset } ) ) ) );
}

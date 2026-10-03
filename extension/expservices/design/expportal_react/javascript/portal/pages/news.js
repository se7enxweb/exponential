import { React, h, useAsync, Async, Title, ItemCard, Pager, Row, Col, Card, Button } from '../ui.js';
import * as api from '../api.js';

export default function News( { id } ) {
    if ( id ) return h( Article, { id } );
    const [ offset, setOffset ] = React.useState( 0 );
    const res = useAsync( () => api.children( 2, offset, api.pageSize ), [ offset ] );
    return h( 'div', { 'data-page': 'news' }, h( Title, { note: 'children of the front page node' }, 'News' ),
        h( Async, { res }, d => h( 'div', null,
            h( Row, { xs: 1, sm: 2, lg: 3, className: 'g-3' },
                d.items.map( i => h( Col, { key: i.id }, h( ItemCard, { item: i, href: '#/news/' + i.id } ) ) ) ),
            h( Pager, { total: d.total, offset: d.offset, limit: api.pageSize, onPage: setOffset } ) ) ) );
}

function Article( { id } ) {
    const res = useAsync( () => api.node( id ), [ id ] );
    return h( 'div', { 'data-page': 'article' }, h( Button, { variant: 'link', href: '#/news', className: 'ps-0' }, 'Back to news' ),
        h( Async, { res }, n => h( Card, null, h( Card.Body, null, h( 'h1', { className: 'h4' }, n.name ),
            n.image ? h( 'img', { src: n.image, className: 'img-fluid mb-3', alt: '' } ) : null, h( 'p', null, n.intro ) ) ) ) );
}

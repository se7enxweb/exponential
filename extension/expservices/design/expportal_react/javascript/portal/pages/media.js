import { React, h, useAsync, Async, Title, ItemCard, Pager, Row, Col } from '../ui.js';
import * as api from '../api.js';

export default function Media() {
    const [ offset, setOffset ] = React.useState( 0 );
    const res = useAsync( () => api.media( offset, api.pageSize ), [ offset ] );
    return h( 'div', { 'data-page': 'media' }, h( Title, null, 'Media' ),
        h( Async, { res }, d => h( 'div', null,
            h( Row, { xs: 2, md: 3, lg: 4, className: 'g-3' }, d.items.map( i => h( Col, { key: i.id }, h( ItemCard, { item: i } ) ) ) ),
            h( Pager, { total: d.total, offset: d.offset, limit: api.pageSize, onPage: setOffset } ) ) ) );
}

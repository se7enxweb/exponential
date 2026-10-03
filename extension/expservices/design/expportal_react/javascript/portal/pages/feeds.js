import { h, useAsync, Async, Title, ListGroup, Badge } from '../ui.js';
import * as api from '../api.js';

export default function Feeds() {
    const res = useAsync( () => api.feeds(), [] );
    return h( 'div', { 'data-page': 'feeds' }, h( Title, null, 'Feeds' ),
        h( Async, { res }, d => h( ListGroup, null, d.items.map( ( f, i ) =>
            h( ListGroup.Item, { key: f.id || i, className: 'd-flex justify-content-between' }, f.title || f.name || f.url, h( Badge, { bg: 'secondary' }, f.type || 'rss' ) ) ) ) ) );
}

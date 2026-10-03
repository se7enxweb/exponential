import { h, useAsync, Async, Title, ItemCard, Row, Col, Card } from '../ui.js';
import * as api from '../api.js';

export default function Home() {
    const news = useAsync( () => api.children( 2, 0, 4 ), [] );
    const media = useAsync( () => api.media( 0, 4 ), [] );
    return h( 'div', { 'data-page': 'home' },
        h( 'div', { className: 'portal-hero mb-4 rounded px-3' }, h( 'h1', { className: 'h2' }, 'Exponential Portal' ),
            h( 'p', { className: 'mb-0' }, 'News, shop, forums, media and feeds, served by the expservices library.' ) ),
        h( Title, null, 'Latest news' ),
        h( Async, { res: news }, d => h( Row, { xs: 1, sm: 2, lg: 4, className: 'g-3 mb-4' },
            d.items.map( i => h( Col, { key: i.id }, h( ItemCard, { item: i, href: '#/news/' + i.id } ) ) ) ) ),
        h( Title, null, 'Media' ),
        h( Async, { res: media }, d => h( Row, { xs: 1, sm: 2, lg: 4, className: 'g-3' },
            d.items.map( i => h( Col, { key: i.id }, h( ItemCard, { item: i, href: '#/media' } ) ) ) ) ) );
}

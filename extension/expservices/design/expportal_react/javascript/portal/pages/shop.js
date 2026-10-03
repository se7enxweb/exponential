import { React, h, useAsync, Async, Title, ItemCard, Pager, Row, Col, Card, Button, Table, Alert, ErrorBox } from '../ui.js';
import * as api from '../api.js';

export default function Shop( { id } ) {
    if ( id === 'basket' ) return h( Basket );
    const [ offset, setOffset ] = React.useState( 0 );
    const [ msg, setMsg ] = React.useState( null );
    const res = useAsync( () => api.children( 2, offset, api.pageSize, 'product' ), [ offset ] );
    const add = async i => {
        try { await api.addToBasket( i.objectId, 1 ); setMsg( { ok: true, text: i.name + ' added to the basket' } ); }
        catch ( e ) { setMsg( { ok: false, error: e } ); }
    };
    return h( 'div', { 'data-page': 'shop' },
        h( 'div', { className: 'd-flex justify-content-between align-items-center' }, h( Title, null, 'Shop' ),
            h( Button, { href: '#/shop/basket', variant: 'outline-primary', size: 'sm' }, 'Basket' ) ),
        msg ? ( msg.ok ? h( Alert, { variant: 'success', dismissible: true, onClose: () => setMsg( null ) }, msg.text ) : h( ErrorBox, { error: msg.error } ) ) : null,
        h( Async, { res }, d => h( 'div', null,
            h( Row, { xs: 1, sm: 2, lg: 3, className: 'g-3' }, d.items.map( i => h( Col, { key: i.id },
                h( Card, { className: 'h-100' }, h( Card.Body, null, h( Card.Title, { className: 'h6' }, i.name ),
                    h( Button, { size: 'sm', onClick: () => add( i ) }, 'Add to basket' ) ) ) ) ) ),
            h( Pager, { total: d.total, offset: d.offset, limit: api.pageSize, onPage: setOffset } ) ) ) );
}

function Basket() {
    const res = useAsync( () => api.basket(), [] );
    const rm = async id => { await api.removeFromBasket( id ); res.reload(); };
    return h( 'div', { 'data-page': 'basket' }, h( Button, { variant: 'link', href: '#/shop', className: 'ps-0' }, 'Back to the shop' ),
        h( Title, null, 'Basket' ),
        h( Async, { res }, b => {
            const items = ( b && b.items ) || [];
            if ( !items.length ) return h( Alert, { variant: 'light' }, 'The basket is empty.' );
            return h( Table, { responsive: true }, h( 'tbody', null, items.map( i => h( 'tr', { key: i.id },
                h( 'td', null, i.name ), h( 'td', null, i.quantity ), h( 'td', null, i.total || '' ),
                h( 'td', null, h( Button, { size: 'sm', variant: 'outline-danger', onClick: () => rm( i.id ) }, 'Remove' ) ) ) ) ) );
        } ) );
}

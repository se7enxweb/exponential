import { React, h, useAsync, Async, Title, Pager, ListGroup, Button, Form, Card, ErrorBox } from '../ui.js';
import * as api from '../api.js';

export default function Forums( { id } ) {
    if ( id ) return h( TopicList, { id } );
    const res = useAsync( () => api.forums( 0, 50 ), [] );
    return h( 'div', { 'data-page': 'forums' }, h( Title, null, 'Forums' ),
        h( Async, { res }, d => h( ListGroup, null, d.items.map( f =>
            h( ListGroup.Item, { key: f.id, action: true, href: '#/forums/' + f.id }, f.name ) ) ) ) );
}

function TopicList( { id } ) {
    const [ offset, setOffset ] = React.useState( 0 );
    const res = useAsync( () => api.topics( id, offset, api.pageSize ), [ id, offset ] );
    return h( 'div', { 'data-page': 'topics' }, h( Button, { variant: 'link', href: '#/forums', className: 'ps-0' }, 'All forums' ),
        h( Title, null, 'Topics' ),
        h( Async, { res }, d => h( 'div', null,
            h( ListGroup, null, d.items.map( t => h( ListGroup.Item, { key: t.id, action: true, href: '#/topic/' + t.id }, t.name ) ) ),
            h( Pager, { total: d.total, offset: d.offset, limit: api.pageSize, onPage: setOffset } ) ) ) );
}

export function Topic( { id } ) {
    const [ offset, setOffset ] = React.useState( 0 );
    const [ text, setText ] = React.useState( '' );
    const [ err, setErr ] = React.useState( null );
    const res = useAsync( () => api.replies( id, offset, api.pageSize ), [ id, offset ] );
    const send = async e => {
        e.preventDefault(); setErr( null );
        try { await api.reply( id, text ); setText( '' ); res.reload(); } catch ( x ) { setErr( x ); }
    };
    return h( 'div', { 'data-page': 'topic' }, h( Button, { variant: 'link', href: '#/forums', className: 'ps-0' }, 'Forums' ),
        h( Async, { res }, d => h( 'div', null,
            d.items.map( r => h( Card, { key: r.id || r.node_id, className: 'mb-2' }, h( Card.Body, null, r.message || r.name || '' ) ) ),
            h( Pager, { total: d.total, offset: d.offset, limit: api.pageSize, onPage: setOffset } ) ) ),
        err ? h( ErrorBox, { error: err } ) : null,
        h( Form, { onSubmit: send, className: 'mt-3' }, h( Form.Control, { as: 'textarea', rows: 3, value: text, onChange: e => setText( e.target.value ), placeholder: 'Your reply' } ),
            h( Button, { type: 'submit', className: 'mt-2', disabled: !text }, 'Reply' ) ) );
}

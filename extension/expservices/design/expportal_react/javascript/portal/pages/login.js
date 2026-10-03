import { React, h, Title, Form, Button, Card, ErrorBox, Alert } from '../ui.js';
import * as api from '../api.js';

export default function Login( { user, onUser } ) {
    const [ name, setName ] = React.useState( '' );
    const [ pass, setPass ] = React.useState( '' );
    const [ err, setErr ] = React.useState( null );
    const submit = async e => {
        e.preventDefault(); setErr( null );
        try { onUser( await api.login( name, pass ) ); location.hash = '#/profile'; } catch ( x ) { setErr( x ); }
    };
    return h( 'div', { 'data-page': 'login', style: { maxWidth: 420 } }, h( Title, null, 'Sign in' ),
        err ? h( ErrorBox, { error: err, plain: true } ) : null,
        h( Form, { onSubmit: submit },
            h( Form.Group, { className: 'mb-3' }, h( Form.Label, null, 'User name' ), h( Form.Control, { name: 'login', autoComplete: 'username', value: name, onChange: e => setName( e.target.value ) } ) ),
            h( Form.Group, { className: 'mb-3' }, h( Form.Label, null, 'Password' ), h( Form.Control, { type: 'password', name: 'password', autoComplete: 'current-password', value: pass, onChange: e => setPass( e.target.value ) } ) ),
            h( Button, { type: 'submit', disabled: !name || !pass }, 'Sign in' ) ) );
}

export function Profile( { user, onUser } ) {
    const out = async () => { await api.logout(); onUser( null ); location.hash = '#/'; };
    if ( !user ) return h( 'div', { 'data-page': 'profile' }, h( Alert, { variant: 'warning' }, 'You are not signed in. ', h( 'a', { href: '#/login' }, 'Sign in' ) ) );
    return h( 'div', { 'data-page': 'profile' }, h( Title, null, 'Profile' ),
        h( Card, null, h( Card.Body, null, h( 'p', null, h( 'strong', null, user.name || user.login || '' ) ), h( 'p', { className: 'portal-muted' }, user.email || '' ),
            h( Button, { variant: 'outline-secondary', onClick: out }, 'Sign out' ) ) ) );
}

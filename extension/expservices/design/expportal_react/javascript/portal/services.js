// Services client for the expservices library: fetch + session cookie + form token + envelope handling.
// Every call is ezjscore/call/<service>[::arg...]; ezjscore wraps the answer as { error_text, content },
// the service's own envelope is { ok, data, meta } or { ok:false, error:{ code, message } }.

const root = document.getElementById( 'root' );
const BASE = root.dataset.services.replace( /\/$/, '' );
export const SITE = root.dataset.site.replace( /\/$/, '' );

export class ServiceError extends Error {
    constructor( code, message ) { super( message ); this.code = code; }
}

let token = null;

async function unwrap( response ) {
    let body;
    try { body = await response.json(); }
    catch ( e ) { throw new ServiceError( response.status, 'Not a JSON answer (HTTP ' + response.status + ')' ); }
    if ( body && body.error_text ) throw new ServiceError( response.status || 500, body.error_text );
    const env = body && 'content' in body ? body.content : body;
    if ( env && env.ok === false ) throw new ServiceError( env.error.code, env.error.message );
    return env && env.ok === true ? env : { ok: true, data: env, meta: {} };
}

function url( name, args ) {
    const tail = ( args || [] ).map( a => encodeURIComponent( String( a ) ) ).join( '::' );
    return BASE + '/' + name + ( tail ? '::' + tail : '' ) + '?ContentType=json';
}

// Read: GET, positional arguments. Resolves to the envelope { ok, data, meta }.
export async function read( name, args ) {
    const r = await fetch( url( name, args ), { credentials: 'same-origin', headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } } );
    return unwrap( r );
}

export async function formToken() {
    if ( token === null ) {
        const env = await read( 'expsession::token' );
        token = ( env.data && ( env.data.token || env.data ) ) || '';
    }
    return token;
}

// Write: POST with the form token (field and header), POST fields as an object.
export async function write( name, fields, args ) {
    const t = await formToken();
    const body = new URLSearchParams( Object.assign( { ezxform_token: t }, fields || {} ) );
    const r = await fetch( url( name, args ), { method: 'POST', body, credentials: 'same-origin',
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': t } } );
    return unwrap( r );
}

export function resetToken() { token = null; }

// Paging helper: { items, total, offset, limit } from a paged envelope.
export function pageOf( env ) {
    const m = env.meta || {};
    const items = Array.isArray( env.data ) ? env.data : ( env.data && env.data.items ) || [];
    return { items, total: m.total != null ? m.total : items.length, offset: m.offset || 0, limit: m.limit || items.length };
}

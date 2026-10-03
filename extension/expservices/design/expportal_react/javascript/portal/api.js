// The portal's view of the catalogue: one function per screen need, mapped to expservices calls.
// If a service is renamed in the catalogue, this is the only file to change.
import { read, write, postPlain, resetToken, pageOf } from './services.js';

const PAGE = 12;
export const pageSize = PAGE;

// Normalises a content node as exported by the services to what the cards need.
export function card( n ) {
    n = n || {};
    const id = n.node_id || n.id || ( n.node && n.node.node_id );
    return { id, objectId: n.contentobject_id || n.object_id,
        name: n.name || n.title || ( n.object && n.object.name ) || ( 'Node ' + id ),
        intro: n.intro || n.summary || '', image: n.image_url || n.image || null,
        class: n.class_identifier || n.class || '', published: n.published || null, raw: n };
}

export async function whoami() { return ( await read( 'expsession::whoami' ) ).data; }
export async function login( name, pass ) {
    await postPlain( 'expsession::login', { username: name, password: pass } );
    return whoami();
}
export async function logout() { const r = await write( 'expsession::logout' ); resetToken(); return r; }

export async function children( nodeId, offset, limit, classes ) {
    const env = await read( 'expnode::children', [ nodeId, offset || 0, limit || PAGE ].concat( classes ? [ classes ] : [] ) );
    const p = pageOf( env );
    p.items = p.items.map( card );
    return p;
}
export async function node( nodeId ) { return card( ( await read( 'expnode::get', [ nodeId ] ) ).data ); }

export async function search( q, offset, limit ) {
    const p = pageOf( await read( 'expsearch::search', [ q, offset || 0, limit || PAGE ] ) );
    p.items = p.items.map( card );
    return p;
}

export async function basket() { return ( await read( 'expbasket::get' ) ).data; }
export async function addToBasket( objectId, qty ) { return write( 'expbasket::add', { object_id: objectId, quantity: qty || 1 } ); }
export async function removeFromBasket( itemId ) { return write( 'expbasket::remove', { item_id: itemId } ); }

export async function forums( offset, limit ) {
    const p = pageOf( await read( 'expforum::forums', [ offset || 0, limit || PAGE ] ) );
    p.items = p.items.map( card );
    return p;
}
export async function topics( forumNodeId, offset, limit ) {
    const p = pageOf( await read( 'expforum::topics', [ forumNodeId, offset || 0, limit || PAGE ] ) );
    p.items = p.items.map( card );
    return p;
}
export async function replies( topicNodeId, offset, limit ) {
    return pageOf( await read( 'expforum::replies', [ topicNodeId, offset || 0, limit || PAGE ] ) );
}
export async function reply( topicNodeId, message ) { return write( 'expforum::reply', { topic: topicNodeId, message } ); }

export async function media( offset, limit ) {
    const p = pageOf( await read( 'expmedia::list', [ offset || 0, limit || PAGE ] ) );
    p.items = p.items.map( card );
    return p;
}
export async function feeds() { return pageOf( await read( 'expfeed::list', [ 0, 50 ] ) ); }

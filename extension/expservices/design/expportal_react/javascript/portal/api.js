// The portal's view of the catalogue: one function per screen need, mapped to expservices calls.
// If a service is renamed in the catalogue, this is the only file to change.
import { read, write, postPlain, resetToken, pageOf } from './services.js';

const PAGE = 12;
// Content roots as in expportal.ini ( MediaNode, ForumsNode ).
const MEDIA_NODE = 43;
const FORUMS_NODE = 2;
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

// expnode::children args: node_id, sort, order, limit, offset, filter (json)
export async function children( nodeId, offset, limit, classes ) {
    const args = [ nodeId, 'published', 'desc', limit || PAGE, offset || 0 ];
    if ( classes ) args.push( JSON.stringify( { class: [].concat( classes ) } ) );
    const p = pageOf( await read( 'expnode::children', args ) );
    p.items = p.items.map( card );
    return p;
}
// expproduct::list args: parent_node_id, limit, offset
export async function products( parentId, offset, limit ) {
    const p = pageOf( await read( 'expproduct::list', [ parentId, limit || PAGE, offset || 0 ] ) );
    p.items = p.items.map( card );
    return p;
}
export async function node( nodeId ) { return card( ( await read( 'expnode::get', [ nodeId ] ) ).data ); }

export async function search( q, offset, limit ) {
    const p = pageOf( await read( 'expsearch::search', [ q, limit || PAGE, offset || 0 ] ) );
    p.items = p.items.map( card );
    return p;
}

export async function basket() { return ( await read( 'expbasket::view' ) ).data; }
export async function addToBasket( objectId, qty ) { return write( 'expbasket::add', { object_id: objectId, quantity: qty || 1 } ); }
export async function removeFromBasket( itemId ) { return write( 'expbasket::remove', { item_id: itemId } ); }

// expforum::list args: parent_node_id, limit, offset
export async function forums( offset, limit ) {
    const p = pageOf( await read( 'expforum::list', [ FORUMS_NODE, limit || PAGE, offset || 0 ] ) );
    p.items = p.items.map( card );
    return p;
}
// exptopic::list args: forum_node_id, limit, offset
export async function topics( forumNodeId, offset, limit ) {
    const p = pageOf( await read( 'exptopic::list', [ forumNodeId, limit || PAGE, offset || 0 ] ) );
    p.items = p.items.map( card );
    return p;
}
// expreply::list args: topic_node_id, limit, offset, newest_first
export async function replies( topicNodeId, offset, limit ) {
    return pageOf( await read( 'expreply::list', [ topicNodeId, limit || PAGE, offset || 0 ] ) );
}
// expreply::create: POST topic_node_id, fields (json)
export async function reply( topicNodeId, message ) {
    return write( 'expreply::create', { topic_node_id: topicNodeId, fields: JSON.stringify( { subject: 'Re', message } ) } );
}

export async function media( offset, limit ) {
    const p = pageOf( await read( 'expmedia::list', [ MEDIA_NODE, limit || PAGE, offset || 0 ] ) );
    p.items = p.items.map( card );
    return p;
}
// expfeed::exports args: limit, offset, only_active
export async function feeds() { return pageOf( await read( 'expfeed::exports', [ 50, 0, 1 ] ) ); }

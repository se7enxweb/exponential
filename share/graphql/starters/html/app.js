// A site from the GraphQL API of Exponential, without a build step: the menu (the children of the root), a page per
// URL alias (#/about/company), its fields read with the query of its class, and the list of its children.
( function () {
  'use strict';
  var config = window.EXP_CONFIG, queries = window.EXP_QUERIES;

  function graphql( query, variables ) {
    var headers = { 'Content-Type': 'application/json', 'Accept': 'application/json' };
    if ( config.apiKey ) {
      headers.Authorization = 'Bearer ' + config.apiKey;
    }
    return fetch( config.endpoint, { method: 'POST', headers: headers, body: JSON.stringify( { query: query, variables: variables || {} } ) } )
      .then( function ( response ) { return response.json(); } )
      .then( function ( answer ) {
        if ( answer.errors && !answer.data ) {
          throw new Error( answer.errors[ 0 ].message );
        }
        return answer.data;
      } );
  }

  function el( tag, attributes, children ) {
    var node = document.createElement( tag );
    Object.keys( attributes || {} ).forEach( function ( name ) { node.setAttribute( name, attributes[ name ] ); } );
    ( children || [] ).forEach( function ( child ) {
      node.appendChild( typeof child === 'string' ? document.createTextNode( child ) : child );
    } );
    return node;
  }

  function link( item ) {
    return el( 'a', { href: '#/' + ( item.urlAlias || '' ) }, [ item.name ] );
  }

  // A field's value as the page shows it: an image, rich text (HTML from the site), a relation, a list, text
  function render( value ) {
    if ( value === null || value === undefined || value === '' ) {
      return null;
    }
    if ( Array.isArray( value ) ) {
      var list = el( 'ul' );
      value.forEach( function ( item ) {
        var shown = render( item );
        if ( shown ) {
          list.appendChild( el( 'li', {}, [ shown ] ) );
        }
      } );
      return list.childNodes.length ? list : null;
    }
    if ( typeof value === 'object' ) {
      if ( value.url && value.width !== undefined ) {
        return value.url ? el( 'img', { src: value.url, alt: value.alt || '', width: value.width || '', height: value.height || '', loading: 'lazy' } ) : null;
      }
      if ( value.html !== undefined ) {
        var html = el( 'div', { 'class': 'rich-text' } );
        html.innerHTML = value.html || ''; // rendered by the site, as its own pages show it
        return value.html ? html : null;
      }
      if ( value.urlAlias !== undefined ) {
        return link( value );
      }
      return el( 'span', {}, [ Object.keys( value ).map( function ( key ) { return value[ key ]; } ).filter( Boolean ).join( ', ' ) ] );
    }
    return el( 'span', {}, [ String( value ) ] );
  }

  function showMenu() {
    graphql( queries.MENU ).then( function ( data ) {
      var menu = document.getElementById( 'menu' );
      ( data.node ? data.node.children.nodes : [] ).forEach( function ( item ) { menu.appendChild( link( item ) ); } );
    } );
  }

  function showPage() {
    var page = document.getElementById( 'page' ), path = decodeURIComponent( location.hash.replace( /^#\/?/, '' ) ) || '/';
    page.textContent = 'Loading…';
    graphql( queries.NODE, { path: path } ).then( function ( data ) {
      var node = data.node;
      if ( !node ) {
        page.textContent = 'Not found.';
        return null;
      }
      var query = queries.BY_CLASS[ node.classIdentifier ];
      return ( query ? graphql( query, { id: node.id } ) : Promise.resolve( { content: {} } ) ).then( function ( full ) {
        page.textContent = '';
        page.appendChild( el( 'h1', {}, [ node.name ] ) );
        var fields = full.content || {};
        Object.keys( fields ).forEach( function ( key ) {
          if ( [ 'id', 'name', 'classIdentifier' ].indexOf( key ) !== -1 ) {
            return;
          }
          var shown = render( fields[ key ] );
          if ( shown ) {
            page.appendChild( el( 'section', { 'class': 'field field-' + key }, [ shown ] ) );
          }
        } );
        if ( node.children.totalCount ) {
          var list = el( 'ul', { 'class': 'children' } );
          node.children.nodes.forEach( function ( child ) { list.appendChild( el( 'li', {}, [ link( child ) ] ) ); } );
          page.appendChild( list );
        }
      } );
    } ).catch( function ( error ) {
      page.textContent = error.message;
    } );
  }

  window.addEventListener( 'hashchange', showPage );
  showMenu();
  showPage();
}() );

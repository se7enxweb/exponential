import { NODE } from './queries';

// One request to the GraphQL API of the site, at build time.
export async function graphql<T = any>( query: string, variables: Record<string, unknown> = {} ): Promise<T> {
  const headers: Record<string, string> = { 'Content-Type': 'application/json', Accept: 'application/json' };
  if ( import.meta.env.EXP_GRAPHQL_KEY ) {
    headers.Authorization = `Bearer ${ import.meta.env.EXP_GRAPHQL_KEY }`;
  }
  const response = await fetch( import.meta.env.EXP_GRAPHQL_ENDPOINT, { method: 'POST', headers, body: JSON.stringify( { query, variables } ) } );
  const answer = await response.json();
  if ( answer.errors && !answer.data ) {
    throw new Error( answer.errors[ 0 ].message );
  }
  return answer.data as T;
}

// The URL aliases of the static build: the content tree from the root, EXP_BUILD_DEPTH levels deep.
export async function allPaths(): Promise<string[]> {
  const depth = Number( import.meta.env.EXP_BUILD_DEPTH ?? 3 );
  const paths: string[] = [];
  let level = [ '/' ];
  for ( let i = 0; i <= depth && level.length; i++ ) {
    const next: string[] = [];
    for ( const path of level ) {
      const { node } = await graphql<{ node: { urlAlias: string; children: { nodes: { urlAlias: string }[] } } | null }>( NODE, { path } );
      if ( !node ) {
        continue;
      }
      paths.push( path === '/' ? '' : node.urlAlias );
      next.push( ...node.children.nodes.map( ( child ) => child.urlAlias ).filter( Boolean ) );
    }
    level = next;
  }
  return [ ...new Set( paths ) ];
}

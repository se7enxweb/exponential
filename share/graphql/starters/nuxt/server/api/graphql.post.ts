// The app's own route to the site's GraphQL API: the pages post { query, variables } here, the server adds the key.
export default defineEventHandler( async ( event ) => {
  const config = useRuntimeConfig( event );
  const body = await readBody<{ query: string; variables?: Record<string, unknown> }>( event );
  const headers: Record<string, string> = { 'Content-Type': 'application/json', Accept: 'application/json' };
  if ( config.graphqlKey ) {
    headers.Authorization = `Bearer ${ config.graphqlKey }`;
  }
  return await $fetch( config.graphqlEndpoint, { method: 'POST', headers, body: { query: body.query, variables: body.variables ?? {} } } );
} );

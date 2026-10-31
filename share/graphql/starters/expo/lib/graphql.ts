// One request to the GraphQL API of the site from the app.
export async function graphql<T = any>( query: string, variables: Record<string, unknown> = {} ): Promise<T> {
  const headers: Record<string, string> = { 'Content-Type': 'application/json', Accept: 'application/json' };
  if ( process.env.EXPO_PUBLIC_GRAPHQL_KEY ) {
    headers.Authorization = `Bearer ${ process.env.EXPO_PUBLIC_GRAPHQL_KEY }`;
  }
  const response = await fetch( process.env.EXPO_PUBLIC_GRAPHQL_ENDPOINT as string, { method: 'POST', headers, body: JSON.stringify( { query, variables } ) } );
  const answer = await response.json();
  if ( answer.errors && !answer.data ) {
    throw new Error( answer.errors[ 0 ].message );
  }
  return answer.data as T;
}

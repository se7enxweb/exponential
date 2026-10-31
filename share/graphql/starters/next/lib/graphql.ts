// One request to the GraphQL API of the site, on the server. Answers are cached by Next.js for a minute; the site's own
// response cache answers persisted queries in about a millisecond (./console exp:graphql:persist graphql/ --apply).
export async function graphql<T = any>( query: string, variables: Record<string, unknown> = {} ): Promise<T> {
  const headers: Record<string, string> = { 'Content-Type': 'application/json', Accept: 'application/json' };
  if ( process.env.EXP_GRAPHQL_KEY ) {
    headers.Authorization = `Bearer ${ process.env.EXP_GRAPHQL_KEY }`;
  }
  const response = await fetch( process.env.EXP_GRAPHQL_ENDPOINT as string, {
    method: 'POST',
    headers,
    body: JSON.stringify( { query, variables } ),
    next: { revalidate: 60 },
  } );
  const answer = await response.json();
  if ( answer.errors && !answer.data ) {
    throw new Error( answer.errors[ 0 ].message );
  }
  return answer.data as T;
}

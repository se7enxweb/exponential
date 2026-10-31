// A query of the site through the app's server route, rendered on the server and handed to the browser.
export async function useGraphQL<T = any>( key: string, query: string, variables: Record<string, unknown> = {} ) {
  const { data, error } = await useFetch<{ data?: T; errors?: { message: string }[] }>( '/api/graphql', {
    key,
    method: 'POST',
    body: { query, variables },
  } );
  if ( error.value || ( data.value?.errors && !data.value?.data ) ) {
    throw createError( { statusCode: 502, statusMessage: data.value?.errors?.[ 0 ]?.message ?? 'The site did not answer' } );
  }
  return data.value?.data as T;
}

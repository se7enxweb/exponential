// The end point and the key are server-side runtime settings (NUXT_GRAPHQL_ENDPOINT, NUXT_GRAPHQL_KEY in .env):
// the pages ask the server route /api/graphql of this app, which asks the site, so the key never reaches a browser.
export default defineNuxtConfig( {
  compatibilityDate: '2024-10-01',
  app: { head: { title: '__EXP_SITE__' } },
  runtimeConfig: {
    graphqlEndpoint: '__EXP_ENDPOINT__',
    graphqlKey: '',
  },
} );

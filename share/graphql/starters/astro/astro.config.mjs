import { defineConfig } from 'astro/config';

// A static build: every page is read from the site's GraphQL API once, at build time (npm run build -> dist/).
export default defineConfig( { output: 'static' } );

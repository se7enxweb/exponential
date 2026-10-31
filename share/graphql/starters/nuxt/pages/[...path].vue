<script setup lang="ts">
import { BY_CLASS, NODE } from '~/utils/queries';

// Every address of the site: / is the root, /about/company the content at that URL alias. The fields come from the
// query of the content's class (utils/queries.ts, generated from the schema).
const route = useRoute();
const segments = ( [] as string[] ).concat( route.params.path ?? [] );
const { node } = await useGraphQL<{ node: { id: number; name: string; classIdentifier: string;
  children: { totalCount: number; nodes: { name: string; urlAlias: string }[] } } | null }>( `node:${ route.path }`, NODE, { path: '/' + segments.join( '/' ) } );
if ( !node ) {
  throw createError( { statusCode: 404, statusMessage: 'Not found' } );
}
const query = BY_CLASS[ node.classIdentifier ];
const full = query ? await useGraphQL<{ content: Record<string, any> | null }>( `content:${ node.id }`, query, { id: node.id } ) : { content: null };
const fields = Object.entries( full.content ?? {} ).filter( ( [ key ] ) => ![ 'id', 'name', 'classIdentifier' ].includes( key ) );
useHead( { title: node.name } );
</script>

<template>
  <article>
    <h1>{{ node!.name }}</h1>
    <section v-for="[ key, value ] in fields" :key="key" :class="`field field-${ key }`"><Field :value="value" /></section>
    <ul v-if="node!.children.totalCount" class="children">
      <li v-for="child in node!.children.nodes" :key="child.urlAlias"><NuxtLink :to="`/${ child.urlAlias }`">{{ child.name }}</NuxtLink></li>
    </ul>
  </article>
</template>

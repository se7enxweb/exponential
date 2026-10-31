<script setup lang="ts">
import { MENU } from '~/utils/queries';

const data = await useGraphQL<{ node: { children: { nodes: { name: string; urlAlias: string }[] } } | null }>( 'menu', MENU );
</script>

<template>
  <header>
    <NuxtLink to="/"><strong>__EXP_SITE__</strong></NuxtLink>
    <nav>
      <NuxtLink v-for="item in data?.node?.children.nodes ?? []" :key="item.urlAlias" :to="`/${ item.urlAlias }`"> {{ item.name }} </NuxtLink>
    </nav>
  </header>
  <main><NuxtPage /></main>
</template>

<script setup lang="ts">
// A field's value as the page shows it: an image, rich text (HTML rendered by the site), a relation, a list, text.
defineProps<{ value: any }>();
</script>

<template>
  <template v-if="value === null || value === undefined || value === ''" />
  <ul v-else-if="Array.isArray( value )"><li v-for="( item, i ) in value" :key="i"><Field :value="item" /></li></ul>
  <img v-else-if="typeof value === 'object' && 'width' in value && value.url" :src="value.url" :alt="value.alt ?? ''" :width="value.width" :height="value.height" loading="lazy">
  <div v-else-if="typeof value === 'object' && 'html' in value && value.html" class="rich-text" v-html="value.html" />
  <NuxtLink v-else-if="typeof value === 'object' && 'urlAlias' in value" :to="`/${ value.urlAlias ?? '' }`">{{ value.name }}</NuxtLink>
  <span v-else-if="typeof value === 'object'">{{ Object.values( value ).filter( Boolean ).join( ', ' ) }}</span>
  <span v-else>{{ value }}</span>
</template>

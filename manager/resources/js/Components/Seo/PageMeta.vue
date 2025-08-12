<script setup>
import { computed } from 'vue'
import { Head, usePage } from '@inertiajs/inertia-vue3'
import { BASE_APP_NAME } from '@/Helpers/config.js'

const props = defineProps({
  title: { type: String, default: '' },
  description: { type: String, default: '' },
  image: { type: String, default: '' },
  canonical: { type: String, default: '' },
})

const page = usePage()

const fullTitle = computed(() => {
  const baseTitle = `${BASE_APP_NAME}`
  return props.title ? `${props.title} - ${baseTitle}` : baseTitle
})

const fullImageUrl = computed(() => {
  if (!props.image) return ''
  return props.image.startsWith('http')
    ? props.image
    : `${page.props.value.assetBaseUrl}${props.image}`
})

const currentUrl = computed(() => {
  return typeof window !== 'undefined' ? window.location.href : ''
})

const canonicalUrl = computed(() => {
  if (props.canonical) return props.canonical
  return typeof window !== 'undefined' ? window.location.href.split('?')[0] : ''
})

const titleVal = computed(() => fullTitle.value)
const descVal = computed(() => props.description)
const imageVal = computed(() => fullImageUrl.value)
const urlVal = computed(() => currentUrl.value)
const canonicalVal = computed(() => canonicalUrl.value)
</script>

<template>
  <Head>
    <title>{{ titleVal }}</title>
    <meta name="description" :content="descVal" />

    <meta property="og:title" :content="titleVal" />
    <meta property="og:description" :content="descVal" />
    <meta property="og:type" content="website" />
    <meta property="og:url" :content="urlVal" />
    <meta property="og:image" :content="imageVal" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" :content="titleVal" />
    <meta name="twitter:description" :content="descVal" />
    <meta name="twitter:image" :content="imageVal" />

    <link rel="canonical" :href="canonicalVal" />
  </Head>
</template>

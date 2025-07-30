<script setup>
import { computed } from 'vue'
import MarkdownIt from 'markdown-it'
import markdownItAnchor from 'markdown-it-anchor'
import markdownItTaskLists from 'markdown-it-task-lists'
import prism from 'prismjs'
import 'prismjs/themes/prism-tomorrow.css'
import 'prismjs/components/prism-markup.js'
import 'prismjs/components/prism-php-extras.js'
import 'prismjs/components/prism-javascript.js'
import 'prismjs/components/prism-typescript.js'
import 'prismjs/components/prism-bash.js'
import 'prismjs/components/prism-json.js'

const props = defineProps({
  content: String,
})

const md = new MarkdownIt({
  html: true,
  linkify: true,
  typographer: true,
  highlight: (str, lang) => {
    const language = (lang || '').toLowerCase()
    if (language && prism.languages[language]) {
      try {
        const code = prism.highlight(str, prism.languages[language], language)
        return `<pre class="language-${language}"><code class="language-${language}">${code}</code></pre>`
      } catch (e) {
        console.error('Prism highlight error:', e)
        return `<pre class="language-text"><code>${md.utils.escapeHtml(str)}</code></pre>`
      }
    }
    return `<pre class="language-text"><code>${md.utils.escapeHtml(str)}</code></pre>`
  },
})
  .use(markdownItAnchor)
  .use(markdownItTaskLists)

const renderedHtml = computed(() => md.render(props.content))
</script>

<template>
  <div class="prose dark:prose-invert max-w-none" v-html="renderedHtml" />
</template>

<style scoped>
  .prose input[type='checkbox'] {
    margin-right: 0.5rem;
  }
</style>

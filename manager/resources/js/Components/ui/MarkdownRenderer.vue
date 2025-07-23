<script setup lang="ts">
import { computed } from 'vue'
import MarkdownIt from 'markdown-it'
import markdownItAnchor from 'markdown-it-anchor'
import markdownItTaskLists from 'markdown-it-task-lists'
import prism from 'prismjs'
import 'prismjs/themes/prism-tomorrow.css'

// Додай імпорт мов, які точно треба (інакше буде undefined)
import 'prismjs/components/prism-javascript'
import 'prismjs/components/prism-typescript'
import 'prismjs/components/prism-php'
import 'prismjs/components/prism-bash'
import 'prismjs/components/prism-json'

const props = defineProps<{ content: string }>()

const md = new MarkdownIt({
    html: true,
    linkify: true,
    typographer: true,
    highlight: (str, lang) => {
        if (lang && prism.languages[lang]) {
            const code = prism.highlight(str, prism.languages[lang], lang)
            return `<pre class="language-${lang}"><code class="language-${lang}">${code}</code></pre>`
        }
        return `<pre class="language-text"><code>${md.utils.escapeHtml(str)}</code></pre>`
    }
})
    .use(markdownItAnchor)
    .use(markdownItTaskLists)

const renderedHtml = computed(() => md.render(props.content))
</script>

<template>
    <div v-html="renderedHtml" class="prose dark:prose-invert max-w-none" />
</template>

<style scoped>
/* Markdown checkboxes */
.prose input[type="checkbox"] {
    margin-right: 0.5rem;
}
</style>

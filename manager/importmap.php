<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    '@hotwired/turbo' => [
        'version' => '7.3.0',
    ],
    'vite' => [
        'version' => '7.0.4',
    ],
    '@vitejs/plugin-vue' => [
        'version' => '6.0.0',
    ],
    'path' => [
        'version' => '0.12.7',
    ],
    '@inertiajs/inertia-vue3' => [
        'version' => '0.6.0',
    ],
    'lodash.isequal' => [
        'version' => '4.5.0',
    ],
    'vue' => [
        'version' => '3.5.13',
    ],
    'lodash.clonedeep' => [
        'version' => '4.5.0',
    ],
    '@inertiajs/inertia' => [
        'version' => '0.11.0',
    ],
    '@vue/runtime-dom' => [
        'version' => '3.5.13',
    ],
    'axios' => [
        'version' => '0.21.4',
    ],
    'qs' => [
        'version' => '6.10.2',
    ],
    'deepmerge' => [
        'version' => '4.2.2',
    ],
    '@vue/runtime-core' => [
        'version' => '3.5.13',
    ],
    '@vue/shared' => [
        'version' => '3.5.13',
    ],
    'side-channel' => [
        'version' => '1.0.4',
    ],
    '@vue/reactivity' => [
        'version' => '3.5.13',
    ],
    'get-intrinsic' => [
        'version' => '1.0.2',
    ],
    'call-bind/callBound' => [
        'version' => '1.0.0',
    ],
    'object-inspect' => [
        'version' => '1.9.0',
    ],
    'has-symbols' => [
        'version' => '1.0.1',
    ],
    'function-bind' => [
        'version' => '1.1.1',
    ],
    'has' => [
        'version' => '1.0.3',
    ],
    '@tailwindcss/postcss' => [
        'version' => '4.1.11',
    ],
    'vue-router' => [
        'version' => '4.5.1',
    ],
    '@vue/devtools-api' => [
        'version' => '6.6.4',
    ],
];

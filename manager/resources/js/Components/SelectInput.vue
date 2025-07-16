<template>
    <div class="mb-4">
        <InputLabel :for="name" :value="label" />

        <select
            :id="name"
            :name="name"
            v-model="internalValue"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            :disabled="disabled"
            :required="required"
        >
            <option value="">-- Select --</option>
            <option
                v-for="(label, key) in options"
                :key="key"
                :value="key"
            >
                {{ label }}
            </option>
        </select>

        <InputError :message="error" class="mt-2" />
    </div>
</template>

<script setup>
import { computed } from 'vue'
import InputLabel from './InputLabel.vue'
import InputError from './InputError.vue'

const props = defineProps({
    modelValue: [String, Number],
    label: String,
    name: String,
    options: Object,
    error: String,
    required: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['update:modelValue'])

const internalValue = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val),
})
</script>

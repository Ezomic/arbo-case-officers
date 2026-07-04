<script setup lang="ts">
import { ref } from 'vue';

const props = defineProps<{
    name: string;
    searchUrl: string;
    placeholder?: string;
    error?: string;
}>();

type Result = { id: string; label: string };

const query = ref('');
const results = ref<Result[]>([]);
const selected = ref<Result | null>(null);
const open = ref(false);
const loading = ref(false);
let debounceTimer: ReturnType<typeof setTimeout>;

function onInput(e: Event) {
    const val = (e.target as HTMLInputElement).value;
    query.value = val;
    selected.value = null;
    clearTimeout(debounceTimer);
    results.value = [];

    if (val.length < 3) {
        open.value = false;
        return;
    }

    loading.value = true;
    open.value = true;

    debounceTimer = setTimeout(async () => {
        try {
            const res = await fetch(`${props.searchUrl}?q=${encodeURIComponent(val)}`);
            results.value = await res.json();
        } finally {
            loading.value = false;
        }
    }, 250);
}

function select(item: Result) {
    selected.value = item;
    query.value = item.label;
    open.value = false;
}

function onBlur() {
    setTimeout(() => {
        open.value = false;
    }, 150);
}
</script>

<template>
    <div class="relative">
        <input type="hidden" :name="name" :value="selected?.id ?? ''" />

        <input
            type="text"
            :value="query"
            :placeholder="placeholder ?? 'Type at least 3 characters…'"
            autocomplete="off"
            class="h-9 w-full rounded-md border px-3 text-sm shadow-xs transition-colors"
            :class="error ? 'border-destructive' : 'border-input'"
            @input="onInput"
            @focus="open = results.length > 0"
            @blur="onBlur"
        />

        <div
            v-if="open"
            class="absolute z-50 mt-1 w-full rounded-md border bg-popover text-popover-foreground shadow-md"
        >
            <p v-if="loading" class="px-3 py-2 text-sm text-muted-foreground">Searching…</p>
            <ul v-else-if="results.length > 0">
                <li
                    v-for="item in results"
                    :key="item.id"
                    class="cursor-pointer px-3 py-2 text-sm hover:bg-accent hover:text-accent-foreground"
                    @mousedown.prevent="select(item)"
                >
                    {{ item.label }}
                </li>
            </ul>
            <p v-else class="px-3 py-2 text-sm text-muted-foreground">No employees found.</p>
        </div>
    </div>
</template>

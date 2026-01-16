<script setup>
import axios from 'axios';
import { computed, onMounted, ref, watch } from 'vue';
import { t } from '../i18n';

const isLoading = ref(false);
const errorMessage = ref('');
const rows = ref([]);
const meta = ref(null);

const perPageOptions = [10, 25, 50, 100];
const perPage = ref(10);
const page = ref(1);
const pageItems = computed(() => {
    const last = meta.value?.last_page ?? 1;
    const current = meta.value?.current_page ?? 1;

    // Render a compact pager: 1 … (current-2..current+2) … last
    if (last <= 7) {
        return Array.from({ length: last }, (_, i) => i + 1);
    }

    const windowSize = 2;
    const start = Math.max(2, current - windowSize);
    const end = Math.min(last - 1, current + windowSize);

    const items = [1];
    if (start > 2) items.push('…');
    for (let p = start; p <= end; p += 1) items.push(p);
    if (end < last - 1) items.push('…');
    items.push(last);
    return items;
});

function unknown() {
    return t('actors.unknown');
}

function formatCm(value) {
    return value != null ? `${value} ${t('actors.units.cm')}` : unknown();
}

function formatKg(value) {
    return value != null ? `${value} ${t('actors.units.kg')}` : unknown();
}

async function load() {
    errorMessage.value = '';
    isLoading.value = true;

    try {
        const res = await axios.get('/api/actors', {
            params: {
                page: page.value,
                per_page: perPage.value,
            },
        });
        rows.value = res.data?.data ?? [];
        meta.value = res.data?.meta ?? null;

        // Keep local state in sync with server meta (in case server clamps page/per_page).
        if (meta.value?.current_page != null) page.value = meta.value.current_page;
        if (meta.value?.per_page != null) perPage.value = meta.value.per_page;
    } catch (e) {
        errorMessage.value = t('errors.failedToLoad');
    } finally {
        isLoading.value = false;
    }
}

onMounted(load);

watch(perPage, async () => {
    page.value = 1;
    await load();
});

watch(page, async () => {
    await load();
});

function canGoPrev() {
    return (meta.value?.current_page ?? 1) > 1 && !isLoading.value;
}

function canGoNext() {
    const current = meta.value?.current_page ?? 1;
    const last = meta.value?.last_page ?? 1;
    return current < last && !isLoading.value;
}

async function goPrev() {
    if (!canGoPrev()) return;
    page.value = Math.max(1, (meta.value?.current_page ?? 1) - 1);
}

async function goNext() {
    if (!canGoNext()) return;
    page.value = (meta.value?.current_page ?? 1) + 1;
}

function isCurrentPage(p) {
    return (meta.value?.current_page ?? 1) === p;
}

async function goToPage(p) {
    if (isLoading.value) return;
    const last = meta.value?.last_page ?? 1;
    const target = Math.min(Math.max(1, p), last);
    if (target === (meta.value?.current_page ?? 1)) return;
    page.value = target;
}
</script>

<template>
    <div class="rounded-xl border bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold">{{ t('actors.title') }}</h1>
                <p class="text-sm text-slate-600">{{ t('actors.subtitle') }}</p>
            </div>
            <button class="rounded-lg border px-3 py-2 text-sm hover:bg-slate-50" :disabled="isLoading" @click="load">
                {{ t('actors.refresh') }}
            </button>
        </div>

        <div class="mb-4 flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
            <div class="flex items-center gap-2 text-sm text-slate-700">
                <span class="whitespace-nowrap">{{ t('actors.pagination.rowsPerPage') }}</span>
                <select
                    v-model.number="perPage"
                    class="rounded-lg border bg-white px-2 py-1 outline-none focus:border-slate-400"
                    :disabled="isLoading"
                >
                    <option v-for="n in perPageOptions" :key="n" :value="n">{{ n }}</option>
                </select>
            </div>

            <div v-if="meta" class="flex flex-col items-start gap-2 text-sm text-slate-700 sm:items-end">
                <div class="whitespace-nowrap">
                    {{ t('actors.pagination.pageOf', { current: meta.current_page, last: meta.last_page }) }}
                </div>
                <div class="whitespace-nowrap text-slate-500">
                    {{ t('actors.pagination.showing', { from: meta.from ?? 0, to: meta.to ?? 0, total: meta.total ?? 0 }) }}
                </div>
            </div>
        </div>

        <div v-if="errorMessage" class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">
            {{ errorMessage }}
        </div>

        <div v-if="isLoading" class="py-8 text-sm text-slate-600">{{ t('actors.loading') }}</div>

        <div v-else class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-sm">
                <thead class="border-b text-slate-700">
                    <tr>
                        <th class="py-2 pr-4 font-medium">{{ t('actors.columns.firstName') }}</th>
                        <th class="py-2 pr-4 font-medium">{{ t('actors.columns.lastName') }}</th>
                        <th class="py-2 pr-4 font-medium">{{ t('actors.columns.address') }}</th>
                        <th class="py-2 pr-4 font-medium">{{ t('actors.columns.height') }}</th>
                        <th class="py-2 pr-4 font-medium">{{ t('actors.columns.weight') }}</th>
                        <th class="py-2 pr-4 font-medium">{{ t('actors.columns.gender') }}</th>
                        <th class="py-2 pr-4 font-medium">{{ t('actors.columns.age') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="rows.length === 0">
                        <td class="py-4 text-slate-600" colspan="7">{{ t('actors.empty') }}</td>
                    </tr>
                    <tr v-for="(r, idx) in rows" :key="idx" class="border-b last:border-b-0">
                        <td class="py-2 pr-4">{{ r.first_name ?? unknown() }}</td>
                        <td class="py-2 pr-4">{{ r.last_name ?? unknown() }}</td>
                        <td class="py-2 pr-4">{{ r.address ?? unknown() }}</td>
                        <td class="py-2 pr-4">{{ formatCm(r.height_cm) }}</td>
                        <td class="py-2 pr-4">{{ formatKg(r.weight_kg) }}</td>
                        <td class="py-2 pr-4">{{ r.gender ?? unknown() }}</td>
                        <td class="py-2 pr-4">{{ r.age ?? unknown() }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="meta && meta.last_page > 1" class="mt-4 flex flex-wrap items-center justify-end gap-2">
            <button
                class="rounded-lg border px-3 py-2 text-sm hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="!canGoPrev()"
                @click="goPrev"
            >
                {{ t('actors.pagination.previous') }}
            </button>

            <div class="flex flex-wrap items-center gap-1">
                <template v-for="(item, idx) in pageItems" :key="`${item}-${idx}`">
                    <span v-if="item === '…'" class="px-2 text-sm text-slate-500">…</span>
                    <button
                        v-else
                        class="min-w-9 rounded-lg border px-3 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-50"
                        :class="isCurrentPage(item) ? 'border-slate-900 bg-slate-900 text-white' : 'hover:bg-slate-50'"
                        :disabled="isLoading"
                        @click="goToPage(item)"
                    >
                        {{ item }}
                    </button>
                </template>
            </div>

            <button
                class="rounded-lg border px-3 py-2 text-sm hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="!canGoNext()"
                @click="goNext"
            >
                {{ t('actors.pagination.next') }}
            </button>
        </div>
    </div>
</template>


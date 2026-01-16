<template>
    <div class="min-h-screen">
        <header class="border-b bg-white">
            <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4">
                <div class="font-semibold">{{ t('app.title') }}</div>
                <nav class="flex items-center gap-4 text-sm">
                    <RouterLink class="text-slate-700 hover:text-slate-900" to="/">{{ t('nav.form') }}</RouterLink>
                    <RouterLink class="text-slate-700 hover:text-slate-900" to="/actors">{{ t('nav.actors') }}</RouterLink>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-5xl px-4 py-8">
            <RouterView />
        </main>
    </div>
</template>

<script setup>
import { t } from './i18n';
</script>


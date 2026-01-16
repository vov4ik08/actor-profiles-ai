<script setup>
import axios from 'axios';
import { computed, ref } from 'vue';
import { useRouter } from 'vue-router';
import { t } from '../i18n';

const email = ref('');
const description = ref('');
const isSubmitting = ref(false);
const errorMessage = ref('');
const fieldErrors = ref({});

const router = useRouter();

const canSubmit = computed(() => email.value.trim() !== '' && description.value.trim() !== '' && !isSubmitting.value);

async function submit() {
    errorMessage.value = '';
    fieldErrors.value = {};
    isSubmitting.value = true;

    try {
        await axios.post('/api/actors', {
            email: email.value,
            description: description.value,
        });

        await router.push('/actors');
    } catch (e) {
        const status = e?.response?.status;
        const data = e?.response?.data;

        if (status === 422) {
            if (data?.errors) {
                fieldErrors.value = data.errors;
                // Avoid duplicating the same message both under the field and in the global alert.
                const firstFieldError = Object.values(data.errors)
                    ?.find((v) => Array.isArray(v) && v.length > 0)?.[0];
                errorMessage.value = data.message && data.message === firstFieldError ? '' : data.message || t('errors.validation');
            } else if (data?.message) {
                errorMessage.value = data.message;
            } else {
                errorMessage.value = t('errors.validation');
            }
        } else {
            errorMessage.value = data?.message || t('errors.submissionFailed');
        }
    } finally {
        isSubmitting.value = false;
    }
}
</script>

<template>
    <div class="rounded-xl border bg-white p-6 shadow-sm">
        <h1 class="mb-1 text-xl font-semibold">{{ t('form.title') }}</h1>

        <form class="space-y-4" @submit.prevent="submit">
            <div>
                <label class="mb-1 block text-sm font-medium" for="email">{{ t('form.email') }}</label>
                <input
                    id="email"
                    v-model="email"
                    class="w-full rounded-lg border px-3 py-2 outline-none focus:border-slate-400"
                    :placeholder="t('form.emailPlaceholder')"
                    type="email"
                    autocomplete="email"
                />
                <div v-if="fieldErrors.email?.length" class="mt-1 text-sm text-red-700">
                    {{ fieldErrors.email[0] }}
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium" for="description">{{ t('form.descriptionLabel') }}</label>
                <p class="mb-1 text-xs text-slate-500">
                    {{ t('form.help') }}
                </p>
                <textarea
                    id="description"
                    v-model="description"
                    class="min-h-36 w-full rounded-lg border px-3 py-2 outline-none focus:border-slate-400"
                    :placeholder="t('form.descriptionPlaceholder')"
                />
                <div v-if="fieldErrors.description?.length" class="mt-1 text-sm text-red-700">
                    {{ fieldErrors.description[0] }}
                </div>
            </div>

            <div v-if="errorMessage" class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">
                {{ errorMessage }}
            </div>

            <div class="flex items-center justify-end gap-3">
                <button
                    class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="!canSubmit"
                    type="submit"
                >
                    <span v-if="!isSubmitting">{{ t('form.submit') }}</span>
                    <span v-else>{{ t('form.submitting') }}</span>
                </button>
            </div>
        </form>
    </div>
</template>


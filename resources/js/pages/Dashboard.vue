<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { reactive } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Nova Transação',
        href: '#',
    },
];

const metodosPagamento = [
    { value: 1, label: 'Boleto' },
    { value: 2, label: 'PIX' },
    { value: 3, label: 'Cartão de Crédito' },
];

const tiposCobranca = [
    { value: 1, label: 'À Vista' },
    { value: 2, label: 'Parcelamento' },
    { value: 3, label: 'Recorrente' },
];

const tomorrow = new Date();
tomorrow.setDate(tomorrow.getDate() + 1);
const minDate = tomorrow.toISOString().split('T')[0];

const form = reactive({
    titulo: '',
    descricao: '',
    dataLimite: '',
    valor: 0,
    metodoPagamento: '',
    tipoCobranca: '',
    clienteId: null as number | null,
    errors: {} as Record<string, string[]>,
});

const notification = reactive({
    show: false,
    type: 'success' as 'success' | 'error',
    message: '',
    url: ''
});

const submit = async () => {
    try {
        const response = await axios.post('/api/transacoes', {
            titulo: form.titulo,
            descricao: form.descricao,
            dataLimite: form.dataLimite,
            valor: form.valor,
            metodoPagamento: form.metodoPagamento,
            tipoCobranca: form.tipoCobranca,
        });

        if (response.data.data?.detalhes?.url_pagamento) {
            notification.show = true;
            notification.type = 'success';
            notification.message = 'Pagamento criado com sucesso!';
            notification.url = response.data.data.detalhes.url_pagamento;
            form.errors = {};
        } else {
            throw new Error('URL de pagamento não encontrada na resposta');
        }
    } catch (error) {
        notification.show = true;
        notification.type = 'error';
        notification.url = '';

        if (axios.isAxiosError(error)) {
            if (error.response?.status === 422) {
                form.errors = error.response.data.errors;
                notification.message = 'Erro de validação nos dados do formulário';
            } else {
                notification.message = error.response?.data?.message || 'Erro ao processar a solicitação';
            }
        } else {
            notification.message = 'Erro inesperado ao criar a transação';
        }

        setTimeout(() => {
            notification.show = false;
        }, 5000);
    }
};
</script>

<template>
    <Head title="Nova Transação" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-6">
                <!-- Notificação de Erro -->
                <div v-if="notification.show && notification.type === 'error'"
                     class="mb-6 p-4 rounded-lg bg-red-100 border border-red-400 text-red-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium">{{ notification.message }}</p>
                        </div>
                        <button @click="notification.show = false" class="ml-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Formulário -->
                <form v-if="!notification.url" @submit.prevent="submit" class="max-w-2xl mx-auto space-y-6">
                    <!-- Título -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Título *</label>
                        <input
                            v-model="form.titulo"
                            type="text"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700"
                            :class="{ 'border-red-500': form.errors.titulo }"
                        />
                        <div v-if="form.errors.titulo" class="text-red-500 text-sm mt-1">
                            {{ form.errors.titulo[0] }}
                        </div>
                    </div>

                    <!-- Descrição -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Descrição *</label>
                        <textarea
                            v-model="form.descricao"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 h-32"
                            :class="{ 'border-red-500': form.errors.descricao }"
                        ></textarea>
                        <div v-if="form.errors.descricao" class="text-red-500 text-sm mt-1">
                            {{ form.errors.descricao[0] }}
                        </div>
                    </div>

                    <!-- Data Limite -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Data Limite *</label>
                        <input
                            v-model="form.dataLimite"
                            type="date"
                            :min="minDate"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700"
                            :class="{ 'border-red-500': form.errors.dataLimite }"
                        />
                        <div v-if="form.errors.dataLimite" class="text-red-500 text-sm mt-1">
                            {{ form.errors.dataLimite[0] }}
                        </div>
                    </div>

                    <!-- Valor -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Valor *</label>
                        <input
                            v-model="form.valor"
                            type="number"
                            step="0.01"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700"
                            :class="{ 'border-red-500': form.errors.valor }"
                        />
                        <div v-if="form.errors.valor" class="text-red-500 text-sm mt-1">
                            {{ form.errors.valor[0] }}
                        </div>
                    </div>

                    <!-- Método de Pagamento -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Método de Pagamento *</label>
                        <select
                            v-model="form.metodoPagamento"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700"
                            :class="{ 'border-red-500': form.errors.metodoPagamento }"
                        >
                            <option value="">Selecione...</option>
                            <option
                                v-for="option in metodosPagamento"
                                :key="option.value"
                                :value="option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                        <div v-if="form.errors.metodoPagamento" class="text-red-500 text-sm mt-1">
                            {{ form.errors.metodoPagamento[0] }}
                        </div>
                    </div>

                    <!-- Tipo de Cobrança -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Tipo de Cobrança *</label>
                        <select
                            v-model="form.tipoCobranca"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700"
                            :class="{ 'border-red-500': form.errors.tipoCobranca }"
                        >
                            <option value="">Selecione...</option>
                            <option
                                v-for="option in tiposCobranca"
                                :key="option.value"
                                :value="option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                        <div v-if="form.errors.tipoCobranca" class="text-red-500 text-sm mt-1">
                            {{ form.errors.tipoCobranca[0] }}
                        </div>
                    </div>

                    <!-- Botão de Envio -->
                    <button
                        type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors"
                    >
                        Gerar Pagamento
                    </button>
                </form>

                <!-- Área de Sucesso -->
                <div v-else class="max-w-2xl mx-auto text-center">
                    <div class="mb-6 p-4 rounded-lg bg-green-100 border border-green-400 text-green-700">
                        <p class="font-medium">Pagamento criado com sucesso!</p>
                    </div>

                    <div class="mt-8 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="text-center">
                            <p class="text-sm font-medium mb-2">Link de pagamento gerado:</p>
                            <div class="flex items-center justify-between gap-2 bg-white dark:bg-gray-700 p-3 rounded-md">
                                <a
                                    :href="notification.url"
                                    target="_blank"
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 break-all text-sm text-left flex-1"
                                >
                                    {{ notification.url }}
                                </a>
                                <button
                                    @click="navigator.clipboard.writeText(notification.url)"
                                    class="text-gray-500 hover:text-gray-700 dark:text-gray-300"
                                    title="Copiar link"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                Clique no link para acessar a página de pagamento
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

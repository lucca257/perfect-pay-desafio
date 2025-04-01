<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { reactive, ref, onMounted } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Transações',
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

// Estado para a lista de transações
const transacoes = ref<any[]>([]);
const carregando = ref(true);
const mostrarFormulario = ref(true);
const transacaoSelecionada = ref<any>(null);

// Carregar lista de transações
const carregarTransacoes = async () => {
    try {
        carregando.value = true;
        const response = await axios.get('/api/transacoes');
        transacoes.value = response.data.data;
    } catch (error) {
        notification.show = true;
        notification.type = 'error';
        notification.message = 'Erro ao carregar transações';
        setTimeout(() => {
            notification.show = false;
        }, 5000);
    } finally {
        carregando.value = false;
    }
};

// Alternar entre formulário e lista
const alternarView = () => {
    mostrarFormulario.value = !mostrarFormulario.value;
    if (!mostrarFormulario.value) {
        carregarTransacoes();
    }
};

// Ver detalhes da transação incluindo histórico
const verDetalhes = (transacao: any) => {
    transacaoSelecionada.value = transacao;
};

// Fechar modal de detalhes
const fecharDetalhes = () => {
    transacaoSelecionada.value = null;
};

// Formatar valor em reais
const formatarMoeda = (valor: number) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(valor);
};

// Obter label do método de pagamento
const getMetodoPagamento = (id: number) => {
    const metodo = metodosPagamento.find(m => m.value === id);
    return metodo ? metodo.label : 'Desconhecido';
};

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

onMounted(() => {
    if (!mostrarFormulario.value) {
        carregarTransacoes();
    }
});
</script>

<template>
    <Head title="Transações" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-6">
                <!-- Botões de Navegação -->
                <div class="flex justify-between mb-6">
                    <h1 class="text-xl font-semibold">{{ mostrarFormulario ? 'Nova Transação' : 'Transações' }}</h1>
                    <button
                        @click="alternarView"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                    >
                        {{ mostrarFormulario ? 'Listar Transações' : 'Nova Transação' }}
                    </button>
                </div>

                <!-- Notificação -->
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

                <!-- Formulário para Nova Transação -->
                <div v-if="mostrarFormulario">
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

                <!-- Listagem de Transações -->
                <div v-else>
                    <div v-if="carregando" class="flex justify-center my-8">
                        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                    </div>

                    <div v-else-if="transacoes.length === 0" class="text-center py-12">
                        <p class="text-gray-500 dark:text-gray-400">Nenhuma transação encontrada</p>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-800 rounded-lg overflow-hidden">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="py-3 px-4 text-left">ID</th>
                                <th class="py-3 px-4 text-left">Identificador</th>
                                <th class="py-3 px-4 text-left">Título</th>
                                <th class="py-3 px-4 text-left">Valor</th>
                                <th class="py-3 px-4 text-left">Tipo</th>
                                <th class="py-3 px-4 text-left">Método</th>
                                <th class="py-3 px-4 text-left">Status</th>
                                <th class="py-3 px-4 text-center">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="transacao in transacoes" :key="transacao.id" class="border-t border-gray-200 dark:border-gray-700">
                                <td class="py-3 px-4">{{ transacao.id }}</td>
                                <td class="py-3 px-4">{{ transacao.identificador }}</td>
                                <td class="py-3 px-4">{{ transacao.detalhes.titulo }}</td>
                                <td class="py-3 px-4">{{ formatarMoeda(transacao.detalhes.valor) }}</td>
                                <td class="py-3 px-4">{{ transacao.detalhes.tipo_cobranca }}</td>
                                <td class="py-3 px-4">{{ getMetodoPagamento(transacao.metodos_pagamento) }}</td>
                                <td class="py-3 px-4">
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-medium"
                                            :class="{
                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': transacao.status === 'Pendente',
                                                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': transacao.status === 'Confirmado',
                                                'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': transacao.status === 'Cancelado'
                                            }"
                                        >
                                            {{ transacao.status }}
                                        </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex justify-center space-x-2">
                                        <button
                                            @click="verDetalhes(transacao)"
                                            class="p-1.5 bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300"
                                            title="Ver histórico"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                        <a
                                            v-if="transacao.detalhes.url_pagamento"
                                            :href="transacao.detalhes.url_pagamento"
                                            target="_blank"
                                            class="p-1.5 bg-green-100 text-green-600 rounded-md hover:bg-green-200 dark:bg-green-900 dark:text-green-300"
                                            title="Acessar link de pagamento"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal de Detalhes/Histórico -->
                <div v-if="transacaoSelecionada" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                    <div class="bg-white dark:bg-gray-800 rounded-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium">Detalhes da Transação</h3>
                                <button @click="fecharDetalhes" class="text-gray-500 hover:text-gray-700 dark:text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">ID</p>
                                    <p>{{ transacaoSelecionada.id }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Identificador</p>
                                    <p>{{ transacaoSelecionada.identificador }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Título</p>
                                    <p>{{ transacaoSelecionada.detalhes.titulo }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Valor</p>
                                    <p>{{ formatarMoeda(transacaoSelecionada.detalhes.valor) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipo de Cobrança</p>
                                    <p>{{ transacaoSelecionada.detalhes.tipo_cobranca }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</p>
                                    <p>{{ transacaoSelecionada.status }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Descrição</p>
                                    <p>{{ transacaoSelecionada.detalhes.descricao }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">URL de Pagamento</p>
                                    <a
                                        :href="transacaoSelecionada.detalhes.url_pagamento"
                                        target="_blank"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 break-all"
                                    >
                                        {{ transacaoSelecionada.detalhes.url_pagamento }}
                                    </a>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <h4 class="font-medium mb-3">Histórico de Status</h4>

                                <div v-if="transacaoSelecionada.historico.length === 0" class="text-center py-4">
                                    <p class="text-gray-500 dark:text-gray-400">Nenhum histórico encontrado</p>
                                </div>

                                <div v-else class="space-y-4">
                                    <div
                                        v-for="(item, index) in transacaoSelecionada.historico"
                                        :key="item.id"
                                        class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
                                    >
                                        <div class="flex justify-between">
                                            <p class="font-medium">Evento #{{ index + 1 }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ new Date(item.created_at).toLocaleString() }}
                                            </p>
                                        </div>

                                        <div class="mt-2">
                                            <div class="bg-white dark:bg-gray-800 p-3 rounded border border-gray-200 dark:border-gray-600 max-h-48 overflow-y-auto">
                                                <pre class="text-xs whitespace-pre-wrap">{{ JSON.stringify(item.payload, null, 2) }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

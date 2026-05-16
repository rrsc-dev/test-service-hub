<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ToggleButton from '@/Components/ToggleButton.vue';
import { Edit, Trash2, Search, X } from 'lucide-vue-next'; // Importamos o ícone X (opcional)
import { ref } from 'vue';

const props = defineProps({
    companies: Object,
    filters: Object,
});

// Separamos o estado do formulário em nome e documento
const form = ref({
    name: props.filters.name || '',
    document: props.filters.document || ''
});

// 1. Função para formatar os dados exibidos na tabela
const formatDocument = (doc) => {
    if (!doc) return '-';
    // Remove qualquer coisa que não seja número
    const numbers = doc.toString().replace(/\D/g, ''); 
    
        return numbers.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, "$1.$2.$3/$4-$5");
    
    
    return doc; // Retorna como veio se a string for irregular
};

// 2. Função para aplicar a máscara no input enquanto o usuário digita
const applyDocumentMask = (event) => {
    let value = event.target.value.replace(/\D/g, ''); // Pega apenas números
    
    if (value.length > 14) value = value.substring(0, 14); // Trava em 14 dígitos
    
    // Aplica a máscara dinamicamente dependendo da quantidade de caracteres
    if (value.length <= 11) {
        value = value.replace(/(\d{3})(\d)/, "$1.$2")
                     .replace(/(\d{3})(\d)/, "$1.$2")
                     .replace(/(\d{3})(\d{1,2})$/, "$1-$2");
    } else {
        value = value.replace(/(\d{2})(\d)/, "$1.$2")
                     .replace(/(\d{3})(\d)/, "$1.$2")
                     .replace(/(\d{3})(\d)/, "$1/$2")
                     .replace(/(\d{4})(\d{1,2})$/, "$1-$2");
    }
    
    form.value.document = value;
};

// 3. Funções de Submissão e Limpeza
const submitSearch = () => {
    router.get(route('companies.index'), form.value, {
        preserveState: true,
        replace: true,
    });
};

const clearSearch = () => {
    // Zera os inputs
    form.value.name = '';
    form.value.document = '';
    // Faz uma nova requisição vazia, o que limpa os filtros da URL e do Banco
    router.get(route('companies.index'), {}, {
        preserveState: true,
        replace: true,
    });
};

const toggleStatus = (id) => {
    router.patch(route('companies.toggle-status', id), {}, { preserveScroll: true });
};

const deleteCompany = (id) => {
    if (confirm('Tem certeza que deseja excluir esta empresa?')) {
        router.delete(route('companies.destroy', id));
    }
};
</script>

<template>
    <Head title="Empresas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Empresas Cadastradas
                </h2>
                <Link :href="route('companies.create')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Nova Empresa
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                
                <div class="mb-6">
    <form @submit.prevent="submitSearch" class="flex flex-col sm:flex-row items-center gap-3">
        
        <div class="relative w-full sm:w-64">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <Search class="w-5 h-5 text-gray-400" />
            </div>
            <input 
                v-model="form.name" 
                type="text" 
                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 p-2.5 shadow-sm" 
                placeholder="Filtrar por nome..."
            >
        </div>
        
        <div class="relative w-full sm:w-56">
            <input 
                v-model="form.document" 
                @input="applyDocumentMask"
                type="text" 
                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 shadow-sm" 
                placeholder="Documento (CPF/CNPJ)..."
            >
        </div>
        
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button 
                type="submit" 
                class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition w-full sm:w-auto shadow-sm"
            >
                Filtrar
            </button>

            <button 
                type="button" 
                @click="clearSearch"
                class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg font-semibold text-xs uppercase tracking-widest hover:bg-gray-50 transition w-full sm:w-auto shadow-sm"
                title="Limpar Filtros"
            >
                <X class="w-4 h-4 mr-1 hidden sm:block" />
                Limpar
            </button>
        </div>
    </form>
</div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                    
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Documento</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="company in companies.data" :key="company.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ company.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
    {{ formatDocument(company.document) }}
</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <ToggleButton class="mx-auto" :modelValue="company.is_active" @update:modelValue="toggleStatus(company.id)" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-4 text-center">
                                    <Link :href="route('companies.show', company.id)" class="text-blue-600 hover:text-blue-900" title="Ver detalhes">
                                        Detalhes
                                    </Link>
                                    <Link :href="route('companies.edit', company.id)" class="inline-flex text-indigo-600 hover:text-indigo-900" title="Editar empresa">
                                        <Edit class="w-5 h-5" />
                                    </Link>
                                    <button @click="deleteCompany(company.id)" class="inline-flex text-red-600 hover:text-red-900" title="Excluir empresa">
                                        <Trash2 class="w-5 h-5" />
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="companies.data.length === 0">
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Nenhuma empresa encontrada.
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-6 flex justify-center" v-if="companies.links.length > 3">
                        <div class="flex flex-wrap -mb-1">
                            <template v-for="(link, key) in companies.links" :key="key">
                                <div 
                                    v-if="link.url === null" 
                                    class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded" 
                                    v-html="link.label"
                                ></div>
                                <Link 
                                    v-else 
                                    class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500 transition-colors" 
                                    :class="{ 'bg-indigo-50 text-indigo-600 border-indigo-300': link.active, 'bg-white text-gray-700': !link.active }" 
                                    :href="link.url" 
                                    v-html="link.label" 
                                    preserve-scroll
                                />
                            </template>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
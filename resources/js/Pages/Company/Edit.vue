<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

// Recebendo a empresa que será editada
const props = defineProps({
    company: Object
});

// Inicializando o formulário do Inertia mapeado com os dados atuais
const form = useForm({
    name: props.company.name,
    document: props.company.document || '',
    email: props.company.email || '',
    phone: props.company.phone || '',
    address: props.company.address || '',
});

const submit = () => {
    // Executa a rota de atualização via método PUT
    form.put(route('companies.update', props.company.id));
};
</script>

<template>
    <Head title="Editar Empresa" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Editar Empresa: {{ company.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                    
                    <form @submit.prevent="submit" class="space-y-4 max-w-xl">
                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700">Nome</label>
                            <input id="name" v-model="form.name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
                            <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
                        </div>

                        <div>
                            <label for="document" class="block font-medium text-sm text-gray-700">Documento (CNPJ/CPF)</label>
                            <input id="document" v-model="form.document" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            <div v-if="form.errors.document" class="text-red-600 text-sm mt-1">{{ form.errors.document }}</div>
                        </div>

                        <div>
                            <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
                            <input id="email" v-model="form.email" type="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">{{ form.errors.email }}</div>
                        </div>

                        <div class="flex items-center justify-end mt-4 space-x-3">
                            <Link :href="route('companies.index')" class="text-sm text-gray-600 hover:text-gray-900 underline">
                                Cancelar
                            </Link>
                            <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Salvar Alterações
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
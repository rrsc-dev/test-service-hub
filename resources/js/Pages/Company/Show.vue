<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Edit, Folder, CalendarDays, ChevronRight, Activity, FileText } from 'lucide-vue-next';

defineProps({
    company: Object
});

// Função auxiliar para formatar a data
const formatDate = (dateString) => {
    if (!dateString) return 'Não definida';
    const date = new Date(dateString + 'T00:00:00');
    return new Intl.DateTimeFormat('pt-BR').format(date);
};

// Função auxiliar para traduzir o status do projeto (TinyInteger)
const getProjectStatus = (status) => {
    const statuses = {
        1: { label: 'Em Andamento', class: 'bg-blue-100 text-blue-800' },
        2: { label: 'Concluído', class: 'bg-green-100 text-green-800' },
        3: { label: 'Pausado', class: 'bg-yellow-100 text-yellow-800' }
    };
    return statuses[status] || { label: 'Desconhecido', class: 'bg-gray-100 text-gray-800' };
};
</script>

<template>
    <Head :title="`Detalhes - ${company.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <Link :href="route('companies.index')" class="text-gray-600 hover:text-gray-900 transition-colors">
                        <ArrowLeft class="w-6 h-6" />
                    </Link>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        {{ company.name }}
                    </h2>
                </div>
                
                <Link :href="route('companies.edit', company.id)" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition space-x-2">
                    <Edit class="w-4 h-4" />
                    <span>Editar Empresa</span>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="border-b border-gray-200 pb-4 mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Informações Cadastrais</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Documento</span>
                            <span class="text-sm font-medium text-gray-900 mt-1 block">{{ company.document || 'Não informado' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">E-mail</span>
                            <span class="text-sm text-gray-900 mt-1 block">{{ company.email || 'Não informado' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</span>
                            <span class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                  :class="company.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                {{ company.is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="border-b border-gray-200 pb-4 mb-6 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 inline-flex items-center space-x-2">
                                <Folder class="w-5 h-5 text-gray-500" />
                                <span>Projetos Relacionados</span>
                            </h3>
                            <p class="mt-1 text-sm text-gray-600">Selecione um projeto para gerenciar seus tickets.</p>
                        </div>
                    </div>

                    <div v-if="company.projects && company.projects.length > 0" class="grid grid-cols-1 gap-4">
                        <Link 
                            v-for="project in company.projects" 
                            :key="project.id"
                            :href="route('projects.tickets.index', project.id)"
                            class="group block border border-gray-200 rounded-lg p-4 hover:bg-gray-50 hover:border-indigo-300 transition duration-150 ease-in-out"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <h4 class="text-base font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                            {{ project.name }}
                                        </h4>
                                        <span 
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            :class="getProjectStatus(project.status).class"
                                        >
                                            {{ getProjectStatus(project.status).label }}
                                        </span>
                                    </div>
                                    
                                    <p v-if="project.description" class="mt-1 text-sm text-gray-500 line-clamp-1">
                                        <FileText class="w-4 h-4 inline mr-1 text-gray-400" />
                                        {{ project.description }}
                                    </p>

                                    <div class="mt-3 flex items-center space-x-6 text-xs text-gray-500">
                                        <div class="flex items-center space-x-1.5">
                                            <CalendarDays class="w-4 h-4 text-gray-400" />
                                            <span>Início: {{ formatDate(project.start_date) }}</span>
                                        </div>
                                        <div class="flex items-center space-x-1.5">
                                            <Activity class="w-4 h-4 text-gray-400" />
                                            <span>Término: {{ formatDate(project.end_date) }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="ml-4 flex-shrink-0 text-gray-400 group-hover:text-indigo-500">
                                    <ChevronRight class="w-6 h-6" />
                                </div>
                            </div>
                        </Link>
                    </div>

                    <div v-else class="text-center py-12 border-2 border-dashed border-gray-300 rounded-lg">
                        <Folder class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-semibold text-gray-900">Nenhum projeto encontrado</h3>
                        <p class="mt-1 text-sm text-gray-500">Esta empresa ainda não possui projetos cadastrados.</p>
                        </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
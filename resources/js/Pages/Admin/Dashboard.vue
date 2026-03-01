<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
    stats:               Object,
    ultimas_cotizaciones: Array,
    ultimos_pedidos:     Array,
})

function fmt(val) {
    return new Intl.NumberFormat('es-GT', { style: 'currency', currency: 'GTQ' }).format(val || 0)
}

const estadoClass = {
    pendiente:  'bg-yellow-100 text-yellow-700',
    confirmado: 'bg-blue-100 text-blue-700',
    en_camino:  'bg-purple-100 text-purple-700',
    entregado:  'bg-green-100 text-green-700',
    cancelado:  'bg-red-100 text-red-700',
}

const estadoIcon = {
    pendiente:  '⏳',
    confirmado: '✅',
    en_camino:  '🚚',
    entregado:  '🎉',
    cancelado:  '❌',
}
</script>

<template>
    <Head title="Dashboard Admin" />
    <AdminLayout>
        <template #header>
            <h1 class="text-xl font-bold text-gray-800">📊 Dashboard</h1>
        </template>

        <!-- Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-4 text-center border-t-4 border-orange-400">
                <p class="text-2xl font-black text-orange-500">{{ stats.total_cotizaciones }}</p>
                <p class="text-xs text-gray-500 mt-1">Cotizaciones</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center border-t-4 border-blue-400">
                <p class="text-2xl font-black text-blue-500">{{ stats.total_pedidos }}</p>
                <p class="text-xs text-gray-500 mt-1">Pedidos</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center border-t-4 border-yellow-400">
                <p class="text-2xl font-black text-yellow-500">{{ stats.pendientes }}</p>
                <p class="text-xs text-gray-500 mt-1">Pendientes</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center border-t-4 border-indigo-400">
                <p class="text-2xl font-black text-indigo-500">{{ stats.confirmados }}</p>
                <p class="text-xs text-gray-500 mt-1">Confirmados</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center border-t-4 border-purple-400">
                <p class="text-2xl font-black text-purple-500">{{ stats.en_camino }}</p>
                <p class="text-xs text-gray-500 mt-1">En camino</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center border-t-4 border-green-400">
                <p class="text-2xl font-black text-green-500">{{ stats.entregados }}</p>
                <p class="text-xs text-gray-500 mt-1">Entregados</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Últimas cotizaciones -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-5 py-4 border-b flex items-center justify-between">
                    <h2 class="font-bold text-gray-700">🔍 Últimas Cotizaciones</h2>
                    <a :href="route('admin.cotizaciones')"
                       class="text-xs text-orange-500 hover:underline">Ver todas →</a>
                </div>
                <div class="divide-y">
                    <div v-for="c in ultimas_cotizaciones" :key="c.id"
                         class="px-5 py-3 flex items-center gap-3 hover:bg-gray-50">
                        <img v-if="c.imagen_url" :src="c.imagen_url"
                             class="w-10 h-10 object-contain rounded border bg-gray-50 flex-shrink-0" />
                        <div v-else
                             class="w-10 h-10 bg-orange-100 rounded flex items-center justify-center flex-shrink-0">
                            <span class="text-lg">📦</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-700 truncate">
                                {{ c.nombre_producto || c.link_amazon }}
                            </p>
                            <p class="text-xs text-gray-400">{{ c.created_at }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-bold text-orange-600">{{ fmt(c.total_gtq) }}</p>
                            <span :class="c.estado === 'pedido'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-gray-100 text-gray-500'"
                                  class="text-xs px-2 py-0.5 rounded-full">
                                {{ c.estado }}
                            </span>
                        </div>
                    </div>
                    <div v-if="!ultimas_cotizaciones.length"
                         class="px-5 py-8 text-center text-gray-400 text-sm">
                        No hay cotizaciones aún
                    </div>
                </div>
            </div>

            <!-- Últimos pedidos -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-5 py-4 border-b flex items-center justify-between">
                    <h2 class="font-bold text-gray-700">📦 Últimos Pedidos</h2>
                    <a :href="route('admin.pedidos')"
                       class="text-xs text-orange-500 hover:underline">Ver todos →</a>
                </div>
                <div class="divide-y">
                    <div v-for="p in ultimos_pedidos" :key="p.id"
                         class="px-5 py-3 flex items-center gap-3 hover:bg-gray-50">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-lg">{{ estadoIcon[p.estado] }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-700">
                                {{ p.nombre }} {{ p.apellido }}
                            </p>
                            <p class="text-xs text-gray-400">{{ p.telefono }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-bold text-green-600">{{ fmt(p.monto_anticipo) }}</p>
                            <span :class="estadoClass[p.estado]"
                                  class="text-xs px-2 py-0.5 rounded-full capitalize">
                                {{ p.estado.replace('_', ' ') }}
                            </span>
                        </div>
                    </div>
                    <div v-if="!ultimos_pedidos.length"
                         class="px-5 py-8 text-center text-gray-400 text-sm">
                        No hay pedidos aún
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
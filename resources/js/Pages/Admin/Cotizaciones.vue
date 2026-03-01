<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps({ cotizaciones: Object })

function fmt(val) {
    return new Intl.NumberFormat('es-GT', { style: 'currency', currency: 'GTQ' }).format(val || 0)
}
</script>

<template>
    <Head title="Cotizaciones" />
    <AdminLayout>
        <template #header>
            <h1 class="text-xl font-bold text-gray-800">🔍 Cotizaciones</h1>
        </template>

        <div class="bg-white rounded-xl shadow overflow-hidden overflow-x-auto">
            <table class="w-full text-sm min-w-[800px]">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3">Producto</th>
                        <th class="text-right px-4 py-3">Precio USD</th>
                        <th class="text-right px-4 py-3">Total GTQ</th>
                        <th class="text-right px-4 py-3">Anticipo</th>
                        <th class="text-center px-4 py-3">Estado</th>
                        <th class="text-center px-4 py-3">IP</th>
                        <th class="text-center px-4 py-3">Fecha</th>
                        <th class="text-center px-4 py-3">Pedido</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="c in cotizaciones.data" :key="c.id"
                        class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img v-if="c.imagen_url" :src="c.imagen_url"
                                     class="w-10 h-10 object-contain rounded border bg-gray-50" />
                                <div v-else class="w-10 h-10 bg-orange-100 rounded flex items-center justify-center">
                                    <span>📦</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-700 max-w-xs truncate">
                                        {{ c.nombre_producto || 'Sin nombre' }}
                                    </p>
                                    <a :href="c.link_amazon" target="_blank"
                                       class="text-xs text-orange-500 hover:underline">Ver en Amazon →</a>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right font-mono">
                            ${{ Number(c.precio_usd).toFixed(2) }}
                        </td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-orange-600">
                            {{ fmt(c.total_gtq) }}
                        </td>
                        <td class="px-4 py-3 text-right font-mono text-green-600">
                            {{ fmt(c.anticipo_gtq) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span :class="c.estado === 'pedido'
                                    ? 'bg-green-100 text-green-700'
                                    : c.estado === 'cancelada'
                                    ? 'bg-red-100 text-red-700'
                                    : 'bg-gray-100 text-gray-600'"
                                  class="px-2 py-1 rounded-full text-xs font-semibold capitalize">
                                {{ c.estado }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-gray-400">
                            {{ c.ip_cliente }}
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-gray-500">
                            {{ c.created_at }}
                        </td>
                        <td class="px-4 py-3 text-center">
    <a :href="route('admin.cotizaciones.show', c.id)"
       class="bg-orange-500 text-white px-3 py-1 rounded-lg text-xs hover:bg-orange-600">
        Ver detalle
    </a>
</td>
                    </tr>
                    <tr v-if="!cotizaciones.data?.length">
                        <td colspan="8" class="text-center py-10 text-gray-400">
                            No hay cotizaciones registradas
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Paginación -->
            <div v-if="cotizaciones.last_page > 1"
                 class="px-4 py-3 border-t flex gap-2 justify-end text-sm flex-wrap">
                <a v-for="p in cotizaciones.links" :key="p.label"
                   :href="p.url" v-html="p.label"
                   class="px-3 py-1 rounded border"
                   :class="p.active ? 'bg-orange-500 text-white' : 'text-gray-600 hover:bg-gray-50'" />
            </div>
        </div>
    </AdminLayout>
</template>
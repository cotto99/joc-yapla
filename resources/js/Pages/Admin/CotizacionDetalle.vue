<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({ cotizacion: Object })

const nuevoEstado = ref(props.cotizacion.pedido?.estado || '')
const notas       = ref(props.cotizacion.pedido?.notas  || '')
const procesando  = ref(false)

function actualizarEstado() {
    procesando.value = true
    router.patch(
        route('admin.pedidos.estado', props.cotizacion.pedido.id),
        { estado: nuevoEstado.value, notas: notas.value },
        {
            onSuccess: () => { procesando.value = false },
            onError:   () => { procesando.value = false },
        }
    )
}

function fmt(val, usd = false) {
    return new Intl.NumberFormat('es-GT', {
        style: 'currency',
        currency: usd ? 'USD' : 'GTQ'
    }).format(val || 0)
}

const estadoClass = {
    pendiente:  'bg-yellow-100 text-yellow-700',
    confirmado: 'bg-blue-100 text-blue-700',
    en_camino:  'bg-purple-100 text-purple-700',
    entregado:  'bg-green-100 text-green-700',
    cancelado:  'bg-red-100 text-red-700',
}
</script>

<template>
    <Head title="Detalle Cotización" />
    <AdminLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <a :href="route('admin.cotizaciones')"
                   class="text-gray-400 hover:text-gray-600 text-sm">← Volver</a>
                <h1 class="text-xl font-bold text-gray-800">🔍 Detalle de Cotización</h1>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Producto -->
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-white rounded-xl shadow p-5">
                    <img v-if="cotizacion.imagen_url"
                         :src="cotizacion.imagen_url"
                         class="w-full h-48 object-contain rounded-xl border bg-gray-50 mb-4" />
                    <div v-else
                         class="w-full h-48 bg-orange-50 rounded-xl flex items-center justify-center mb-4">
                        <span class="text-6xl">📦</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">
                        {{ cotizacion.nombre_producto || 'Sin nombre' }}
                    </h3>
                    <a :href="cotizacion.link_amazon" target="_blank"
                       class="block text-center bg-orange-100 text-orange-600 py-2 rounded-lg text-sm hover:bg-orange-200 mb-3">
                        Ver en Amazon →
                    </a>
                    <div class="text-xs space-y-2 text-gray-500">
                        <div class="flex justify-between">
                            <span>Fecha cotización:</span>
                            <span>{{ cotizacion.created_at }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>IP cliente:</span>
                            <span>{{ cotizacion.ip_cliente }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Estado:</span>
                            <span :class="cotizacion.estado === 'pedido'
                                    ? 'text-green-600 font-bold'
                                    : 'text-gray-500'">
                                {{ cotizacion.estado }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Desglose y pedido -->
            <div class="lg:col-span-2 space-y-4">

                <!-- Desglose de costos -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="font-bold text-gray-700 mb-4">💰 Desglose de costos</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between py-2 border-b text-sm">
                            <span class="text-gray-600">Precio original (USD)</span>
                            <span class="font-mono font-bold">{{ fmt(cotizacion.precio_usd, true) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b text-sm">
                            <span class="text-gray-600">Tipo de cambio</span>
                            <span class="font-mono">Q{{ cotizacion.tipo_cambio }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b text-sm">
                            <span class="text-gray-600">Precio en GTQ</span>
                            <span class="font-mono">{{ fmt(cotizacion.precio_gtq) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b text-sm">
                            <span class="text-gray-600">Ganancia del negocio</span>
                            <span class="font-mono text-orange-600">{{ fmt(cotizacion.ganancia) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b text-sm">
                            <span class="text-gray-600">Flete internacional</span>
                            <span class="font-mono">{{ fmt(cotizacion.costo_flete) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b text-sm">
                            <span class="text-gray-600">Arancel importación</span>
                            <span class="font-mono">{{ fmt(cotizacion.costo_arancel) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b text-sm">
                            <span class="text-gray-600">Comisión del agente</span>
                            <span class="font-mono">{{ fmt(cotizacion.costo_comision) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b text-sm">
                            <span class="text-gray-600">Entrega local</span>
                            <span class="font-mono">{{ fmt(cotizacion.costo_entrega) }}</span>
                        </div>
                        <div class="flex justify-between py-2 text-base font-bold">
                            <span class="text-gray-800">TOTAL GTQ</span>
                            <span class="text-orange-600 text-xl">{{ fmt(cotizacion.total_gtq) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-t">
                            <span class="text-gray-600 text-sm">Anticipo requerido</span>
                            <span class="font-mono font-bold text-green-600">{{ fmt(cotizacion.anticipo_gtq) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Datos del pedido si existe -->
                <div v-if="cotizacion.pedido" class="bg-white rounded-xl shadow p-6">
                    <h3 class="font-bold text-gray-700 mb-4">👤 Datos del Cliente</h3>
                    <div class="grid grid-cols-2 gap-3 text-sm mb-4">
                        <div>
                            <p class="text-xs text-gray-400">Nombre completo</p>
                            <p class="font-semibold">{{ cotizacion.pedido.nombre }} {{ cotizacion.pedido.apellido }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Teléfono</p>
                            <p class="font-semibold">{{ cotizacion.pedido.telefono }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Email</p>
                            <p class="font-semibold">{{ cotizacion.pedido.email || '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">DPI</p>
                            <p class="font-semibold">{{ cotizacion.pedido.dpi || '—' }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs text-gray-400">Dirección de entrega</p>
                            <p class="font-semibold">{{ cotizacion.pedido.direccion }}</p>
                        </div>
                        <div class="col-span-2" v-if="cotizacion.pedido.notas">
                            <p class="text-xs text-gray-400">Notas del cliente</p>
                            <p class="font-semibold">{{ cotizacion.pedido.notas }}</p>
                        </div>
                    </div>

                    <!-- Comprobante -->
                    <div class="mb-4">
                        <p class="text-xs text-gray-400 mb-2">Comprobante de anticipo</p>
                        <a v-if="cotizacion.pedido.comprobante_anticipo"
                           :href="`/storage/${cotizacion.pedido.comprobante_anticipo}`"
                           target="_blank"
                           class="inline-flex items-center gap-2 bg-blue-50 text-blue-600 px-4 py-2 rounded-lg text-sm hover:bg-blue-100">
                            📎 Ver comprobante
                        </a>
                        <span v-else class="text-gray-400 text-sm">Sin comprobante</span>
                    </div>

                    <!-- Actualizar estado -->
                    <div class="border-t pt-4">
                        <p class="font-bold text-gray-700 mb-3">Actualizar estado del pedido</p>
                        <div class="flex gap-3">
                            <select v-model="nuevoEstado"
                                    class="flex-1 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400">
                                <option value="pendiente">⏳ Pendiente</option>
                                <option value="confirmado">✅ Confirmado</option>
                                <option value="en_camino">🚚 En camino</option>
                                <option value="entregado">🎉 Entregado</option>
                                <option value="cancelado">❌ Cancelado</option>
                            </select>
                            <button @click="actualizarEstado" :disabled="procesando"
                                    class="bg-orange-500 text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-orange-600 disabled:opacity-50">
                                {{ procesando ? '...' : 'Guardar' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sin pedido -->
                <div v-else class="bg-gray-50 rounded-xl p-6 text-center text-gray-400">
                    <p class="text-3xl mb-2">📋</p>
                    <p class="font-medium">Esta cotización aún no tiene pedido asociado</p>
                    <p class="text-sm mt-1">El cliente consultó pero no reservó el producto</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
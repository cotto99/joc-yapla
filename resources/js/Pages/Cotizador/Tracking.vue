<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'

const props  = defineProps({
    pedido: Object,
    error:  String,
})

const page = usePage()

const form = useForm({
    codigo: new URLSearchParams(window.location.search).get('codigo') || ''
})

function buscar() {
    form.post(route('cotizador.tracking.buscar'))
}

const estadoConfig = {
    pendiente:  { icon: '⏳', label: 'Pendiente',   color: 'bg-yellow-100 text-yellow-700 border-yellow-300' },
    confirmado: { icon: '✅', label: 'Confirmado',   color: 'bg-blue-100 text-blue-700 border-blue-300'       },
    en_camino:  { icon: '🚚', label: 'En camino',    color: 'bg-purple-100 text-purple-700 border-purple-300' },
    entregado:  { icon: '🎉', label: 'Entregado',    color: 'bg-green-100 text-green-700 border-green-300'    },
    cancelado:  { icon: '❌', label: 'Cancelado',    color: 'bg-red-100 text-red-700 border-red-300'          },
}

function fmt(val) {
    return new Intl.NumberFormat('es-GT', {
        style: 'currency', currency: 'GTQ'
    }).format(val || 0)
}

function fmtFecha(f) {
    if (!f) return '—'
    return new Date(f).toLocaleString('es-GT', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    })
}

// Si viene con ?codigo= en la URL buscar automáticamente
onMounted(() => {
    if (form.codigo && !props.pedido) buscar()
})
</script>

<template>
    <Head title="Rastrear Pedido — JOC-YAPLA" />

    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-yellow-50">

        <!-- HEADER -->
        <header class="bg-gradient-to-r from-orange-500 to-yellow-500 text-white shadow-lg">
            <div class="max-w-3xl mx-auto px-4 py-5 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-black">📦 JOC-YAPLA</h1>
                    <p class="text-orange-100 text-xs mt-0.5">Rastreo de pedidos</p>
                </div>
                <a href="/" class="text-sm text-orange-100 hover:text-white underline">
                    ← Volver al inicio
                </a>
            </div>
        </header>

        <div class="max-w-3xl mx-auto px-4 py-8">

            <!-- Buscador -->
            <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
                <h2 class="text-xl font-black text-gray-800 mb-2">🔍 Rastrear mi pedido</h2>
                <p class="text-gray-500 text-sm mb-5">
                    Ingresá el código de pedido que recibiste al confirmar tu compra
                </p>

                <div class="flex gap-3">
                    <input v-model="form.codigo" type="text"
                           placeholder="YAP-2026-00001"
                           @keyup.enter="buscar"
                           class="flex-1 border-2 rounded-xl px-4 py-3 text-sm font-mono uppercase focus:ring-2 focus:ring-orange-400 focus:border-orange-400" />
                    <button @click="buscar" :disabled="form.processing"
                            class="bg-orange-500 text-white px-6 py-3 rounded-xl font-bold hover:bg-orange-600 disabled:opacity-50 transition">
                        {{ form.processing ? '...' : 'Buscar' }}
                    </button>
                </div>

                <!-- Error -->
                <div v-if="$page.props.flash?.error"
                     class="mt-4 bg-red-50 border border-red-200 rounded-xl p-4 text-red-600 text-sm">
                    ❌ {{ $page.props.flash.error }}
                </div>
            </div>

            <!-- Resultado del pedido -->
            <div v-if="pedido">

                <!-- Info del pedido -->
                <div class="bg-white rounded-2xl shadow p-6 mb-4">
                    <div class="flex items-start justify-between flex-wrap gap-3 mb-4">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Número de pedido</p>
                            <p class="text-2xl font-black text-orange-600 font-mono">{{ pedido.codigo }}</p>
                        </div>
                        <div :class="estadoConfig[pedido.estado]?.color"
                             class="px-4 py-2 rounded-full border font-bold text-sm flex items-center gap-2">
                            <span>{{ estadoConfig[pedido.estado]?.icon }}</span>
                            <span>{{ estadoConfig[pedido.estado]?.label }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-xs text-gray-400">Cliente</p>
                            <p class="font-semibold">{{ pedido.nombre }} {{ pedido.apellido }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Teléfono</p>
                            <p class="font-semibold">{{ pedido.telefono }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Fecha pedido</p>
                            <p class="font-semibold">{{ fmtFecha(pedido.created_at) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Anticipo pagado</p>
                            <p class="font-semibold text-green-600">{{ fmt(pedido.monto_anticipo) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Total del pedido</p>
                            <p class="font-semibold text-orange-600">{{ fmt(pedido.cotizacion?.total_gtq) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Saldo pendiente</p>
                            <p class="font-semibold text-red-500">
                                {{ fmt((pedido.cotizacion?.total_gtq || 0) - (pedido.monto_anticipo || 0)) }}
                            </p>
                        </div>
                    </div>

                    <!-- Producto -->
                    <div v-if="pedido.cotizacion"
                         class="mt-4 pt-4 border-t flex items-center gap-4">
                        <img v-if="pedido.cotizacion.imagen_url"
                             :src="pedido.cotizacion.imagen_url"
                             class="w-16 h-16 object-contain rounded-lg border bg-gray-50 flex-shrink-0" />
                        <div class="flex-shrink-0 w-16 h-16 bg-orange-50 rounded-lg flex items-center justify-center" v-else>
                            <span class="text-3xl">📦</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-700 text-sm">
                                {{ pedido.cotizacion.nombre_producto || 'Producto de Amazon' }}
                            </p>
                            <a :href="pedido.cotizacion.link_amazon" target="_blank"
                               class="text-xs text-orange-500 hover:underline">
                                Ver en Amazon →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Timeline de tracking -->
                <div class="bg-white rounded-2xl shadow p-6">
                    <h3 class="font-bold text-gray-700 mb-6">📋 Historial del pedido</h3>

                    <div class="relative">
                        <!-- Línea vertical -->
                        <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gray-200"></div>

                        <div class="space-y-6">
                            <div v-for="(t, index) in pedido.trackings" :key="t.id"
                                 class="flex gap-4 relative">
                                <!-- Icono estado -->
                                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-lg z-10"
                                     :class="index === pedido.trackings.length - 1
                                         ? 'bg-orange-500 text-white shadow-lg'
                                         : 'bg-gray-100 text-gray-500'">
                                    {{ estadoConfig[t.estado]?.icon || '📌' }}
                                </div>

                                <div class="flex-1 pb-2">
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <span class="font-bold text-gray-800 text-sm capitalize">
                                            {{ estadoConfig[t.estado]?.label || t.estado }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            {{ fmtFecha(t.created_at) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 bg-gray-50 rounded-lg px-3 py-2">
                                        {{ t.descripcion }}
                                    </p>
                                    <p v-if="t.ubicacion"
                                       class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                                        📍 {{ t.ubicacion }}
                                    </p>
                                </div>
                            </div>

                            <div v-if="!pedido.trackings?.length"
                                 class="flex gap-4">
                                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-lg">
                                    ⏳
                                </div>
                                <div class="flex-1">
                                    <p class="text-gray-500 text-sm">Sin actualizaciones aún</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-800 text-gray-400 text-center py-6 text-xs mt-10">
            <p class="font-bold text-white mb-1">📦 JOC-YAPLA</p>
            <p>Importaciones desde Amazon a Guatemala</p>
        </footer>
    </div>
</template>
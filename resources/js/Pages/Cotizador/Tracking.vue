<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'

const props = defineProps({
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
    pendiente:  { icon: '⏳', label: 'Pendiente',  color: 'bg-yellow-500/20 text-yellow-400 border-yellow-500/40' },
    confirmado: { icon: '✅', label: 'Confirmado',  color: 'bg-blue-500/20 text-blue-400 border-blue-500/40'      },
    en_camino:  { icon: '🚚', label: 'En camino',   color: 'bg-purple-500/20 text-purple-400 border-purple-500/40'},
    entregado:  { icon: '🎉', label: 'Entregado',   color: 'bg-green-500/20 text-green-400 border-green-500/40'  },
    cancelado:  { icon: '❌', label: 'Cancelado',   color: 'bg-red-500/20 text-red-400 border-red-500/40'        },
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

onMounted(() => {
    if (form.codigo && !props.pedido) buscar()
})
</script>

<template>
    <Head title="Rastrear Pedido — JOC-YAPLA" />

    <div class="min-h-screen bg-gray-950">

        <!-- HEADER -->
        <header class="bg-black border-b border-yellow-500/30 shadow-lg shadow-yellow-500/10">
            <div class="max-w-3xl mx-auto px-4 py-4 flex items-center justify-between">
                <img src="/images/logo-safari.png" alt="Safari" class="h-14 w-auto" />
                <a href="/" class="border border-yellow-500 text-yellow-400 hover:bg-yellow-500 hover:text-black px-4 py-2 rounded-lg text-sm font-bold transition">
                    Volver al inicio
                </a>
            </div>
        </header>

        <div class="max-w-3xl mx-auto px-4 py-8">

            <!-- Buscador -->
            <div class="bg-gray-900 border border-yellow-500/20 rounded-2xl shadow-xl p-6 mb-6">
                <h2 class="text-xl font-black text-white mb-2">🔍 Rastrear mi pedido</h2>
                <p class="text-gray-400 text-sm mb-5">
                    Ingresa el codigo de pedido que recibiste al confirmar tu compra
                </p>

                <div class="flex gap-3">
                    <input v-model="form.codigo" type="text"
                           placeholder="YAP-2026-00001"
                           @keyup.enter="buscar"
                           class="flex-1 bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-3 text-sm font-mono uppercase focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 placeholder-gray-500" />
                    <button @click="buscar" :disabled="form.processing"
                            class="bg-yellow-500 hover:bg-yellow-400 text-black px-6 py-3 rounded-xl font-black disabled:opacity-50 transition shadow-lg shadow-yellow-500/30">
                        {{ form.processing ? '...' : '⚡ Buscar' }}
                    </button>
                </div>

                <!-- Error -->
                <div v-if="$page.props.flash?.error"
                     class="mt-4 bg-red-500/10 border border-red-500/30 rounded-xl p-4 text-red-400 text-sm">
                    ❌ {{ $page.props.flash.error }}
                </div>
            </div>

            <!-- Resultado del pedido -->
            <div v-if="pedido">

                <!-- Info del pedido -->
                <div class="bg-gray-900 border border-yellow-500/20 rounded-2xl shadow p-6 mb-4">
                    <div class="flex items-start justify-between flex-wrap gap-3 mb-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Numero de pedido</p>
                            <p class="text-2xl font-black text-yellow-400 font-mono">{{ pedido.codigo }}</p>
                        </div>
                        <div :class="estadoConfig[pedido.estado]?.color"
                             class="px-4 py-2 rounded-full border font-bold text-sm flex items-center gap-2">
                            <span>{{ estadoConfig[pedido.estado]?.icon }}</span>
                            <span>{{ estadoConfig[pedido.estado]?.label }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-xs text-gray-500">Cliente</p>
                            <p class="font-semibold text-white">{{ pedido.nombre }} {{ pedido.apellido }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Telefono</p>
                            <p class="font-semibold text-white">{{ pedido.telefono }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Fecha pedido</p>
                            <p class="font-semibold text-white">{{ fmtFecha(pedido.created_at) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Anticipo pagado</p>
                            <p class="font-semibold text-green-400">{{ fmt(pedido.monto_anticipo) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Total del pedido</p>
                            <p class="font-semibold text-yellow-400">{{ fmt(pedido.cotizacion?.total_gtq) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Saldo pendiente</p>
                            <p class="font-semibold text-red-400">
                                {{ fmt((pedido.cotizacion?.total_gtq || 0) - (pedido.monto_anticipo || 0)) }}
                            </p>
                        </div>
                    </div>

                    <!-- Producto -->
                    <div v-if="pedido.cotizacion"
                         class="mt-4 pt-4 border-t border-gray-700 flex items-center gap-4">
                        <img v-if="pedido.cotizacion.imagen_url"
                             :src="pedido.cotizacion.imagen_url"
                             class="w-16 h-16 object-contain rounded-lg border border-gray-700 bg-gray-800 flex-shrink-0" />
                        <div class="flex-shrink-0 w-16 h-16 bg-gray-800 border border-gray-700 rounded-lg flex items-center justify-center" v-else>
                            <span class="text-3xl">📦</span>
                        </div>
                        <div>
                            <p class="font-semibold text-white text-sm">
                                {{ pedido.cotizacion.nombre_producto || 'Producto de Amazon' }}
                            </p>
                            <a :href="pedido.cotizacion.link_amazon" target="_blank"
                               class="text-xs text-yellow-400 hover:underline">
                                Ver en Amazon →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Timeline de tracking -->
                <div class="bg-gray-900 border border-yellow-500/20 rounded-2xl shadow p-6">
                    <h3 class="font-bold text-yellow-400 mb-6">📋 Historial del pedido</h3>

                    <div class="relative">
                        <!-- Línea vertical -->
                        <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gray-700"></div>

                        <div class="space-y-6">
                            <div v-for="(t, index) in pedido.trackings" :key="t.id"
                                 class="flex gap-4 relative">
                                <!-- Icono estado -->
                                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-lg z-10"
                                     :class="index === pedido.trackings.length - 1
                                         ? 'bg-yellow-500 text-black shadow-lg shadow-yellow-500/30'
                                         : 'bg-gray-800 border border-gray-700 text-gray-400'">
                                    {{ estadoConfig[t.estado]?.icon || '📌' }}
                                </div>

                                <div class="flex-1 pb-2">
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <span class="font-bold text-white text-sm capitalize">
                                            {{ estadoConfig[t.estado]?.label || t.estado }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ fmtFecha(t.created_at) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-300 bg-gray-800 border border-gray-700 rounded-lg px-3 py-2">
                                        {{ t.descripcion }}
                                    </p>
                                    <p v-if="t.ubicacion"
                                       class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                        📍 {{ t.ubicacion }}
                                    </p>
                                </div>
                            </div>

                            <div v-if="!pedido.trackings?.length"
                                 class="flex gap-4">
                                <div class="w-10 h-10 bg-gray-800 border border-gray-700 rounded-full flex items-center justify-center text-lg z-10">
                                    ⏳
                                </div>
                                <div class="flex-1 pt-2">
                                    <p class="text-gray-500 text-sm">Sin actualizaciones aun</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-black border-t border-yellow-500/20 text-gray-500 text-center py-6 text-xs mt-10">
            <img src="/images/logo-safari.png" alt="Safari" class="h-10 w-auto mx-auto mb-2" />
            <p>Importaciones desde Amazon a Guatemala</p>
        </footer>
    </div>
</template>
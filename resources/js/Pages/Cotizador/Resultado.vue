<script setup>
import { useForm, Head } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({ cotizacion: Object })
const mostrarPedido = ref(false)

const formPedido = useForm({
    nombre:    '',
    apellido:  '',
    telefono:  '',
    email:     '',
    dpi:       '',
    direccion: '',
    comprobante_anticipo: null,
    notas:     '',
})

function enviarPedido() {
    formPedido.post(route('cotizador.pedido', props.cotizacion.id), {
        forceFormData: true,
    })
}

function fmt(val, usd = false) {
    return new Intl.NumberFormat('es-GT', {
        style: 'currency',
        currency: usd ? 'USD' : 'GTQ'
    }).format(val || 0)
}
</script>

<template>
    <Head title="Tu Cotización — JOC-YAPLA" />

    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-yellow-50">

        <!-- HEADER -->
        <header class="bg-gradient-to-r from-orange-500 to-yellow-500 text-white shadow-lg">
            <div class="max-w-4xl mx-auto px-4 py-5 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-black tracking-tight">📦 JOC-YAPLA</h1>
                    <p class="text-orange-100 text-xs mt-0.5">Importaciones desde Amazon a Guatemala</p>
                </div>
                <a href="/" class="text-sm text-orange-100 hover:text-white underline">
                    ← Nueva cotización
                </a>
            </div>
        </header>

        <div class="max-w-4xl mx-auto px-4 py-8">

            <!-- TÍTULO -->
            <div class="text-center mb-8">
                <div class="text-5xl mb-3">✅</div>
                <h2 class="text-3xl font-black text-gray-800">¡Tu cotización está lista!</h2>
                <p class="text-gray-500 mt-2">Acá te mostramos el costo total de traer tu producto a Guatemala</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Columna izquierda: producto -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow p-5 sticky top-6">
                        <img v-if="cotizacion.imagen_url"
                             :src="cotizacion.imagen_url"
                             class="w-full h-52 object-contain rounded-xl border bg-gray-50 mb-4" />
                        <div v-else
                             class="w-full h-52 bg-orange-50 rounded-xl flex items-center justify-center mb-4">
                            <span class="text-6xl">📦</span>
                        </div>

                        <h3 class="font-bold text-gray-800 text-sm mb-2">
                            {{ cotizacion.nombre_producto || 'Tu producto de Amazon' }}
                        </h3>

                        <a :href="cotizacion.link_amazon" target="_blank"
                           class="block w-full text-center bg-orange-100 text-orange-600 py-2 rounded-lg text-sm hover:bg-orange-200 transition mb-3">
                            Ver en Amazon →
                        </a>

                        <div class="text-xs text-gray-400 space-y-1">
                            <div class="flex justify-between">
                                <span>Tipo de cambio:</span>
                                <span class="font-semibold">Q{{ cotizacion.tipo_cambio }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Tiempo estimado:</span>
                                <span class="font-semibold">{{ cotizacion.tiempo_estimado }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna derecha: desglose y total -->
                <div class="lg:col-span-2 space-y-4">

                    <!-- Desglose completo -->
                    <div class="bg-white rounded-2xl shadow p-6">
                        <h3 class="font-bold text-gray-700 mb-4 text-lg">💰 Desglose de costos</h3>

                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-3 border-b">
                                <div>
                                    <p class="font-medium text-gray-700">Precio original en Amazon</p>
                                    <p class="text-xs text-gray-400">Precio en dólares</p>
                                </div>
                                <span class="font-mono font-bold text-gray-800 text-lg">
                                    {{ fmt(cotizacion.precio_usd, true) }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b">
                                <div>
                                    <p class="font-medium text-gray-700">Precio convertido a GTQ</p>
                                    <p class="text-xs text-gray-400">Al tipo de cambio de Q{{ cotizacion.tipo_cambio }}</p>
                                </div>
                                <span class="font-mono text-gray-700">{{ fmt(cotizacion.precio_gtq) }}</span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b">
                                <div>
                                    <p class="font-medium text-gray-700">Flete internacional</p>
                                    <p class="text-xs text-gray-400">Envío desde USA a Guatemala</p>
                                </div>
                                <span class="font-mono text-gray-700">{{ fmt(cotizacion.costo_flete) }}</span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b">
                                <div>
                                    <p class="font-medium text-gray-700">Impuestos de importación</p>
                                    <p class="text-xs text-gray-400">Aranceles SAT Guatemala</p>
                                </div>
                                <span class="font-mono text-gray-700">{{ fmt(cotizacion.costo_arancel) }}</span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b">
                                <div>
                                    <p class="font-medium text-gray-700">Comisión del servicio</p>
                                    <p class="text-xs text-gray-400">Gestión y trámites de importación</p>
                                </div>
                                <span class="font-mono text-gray-700">{{ fmt(cotizacion.costo_comision) }}</span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b">
                                <div>
                                    <p class="font-medium text-gray-700">Entrega en Guatemala</p>
                                    <p class="text-xs text-gray-400">Envío a tu dirección</p>
                                </div>
                                <span class="font-mono text-gray-700">{{ fmt(cotizacion.costo_entrega) }}</span>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="bg-gradient-to-r from-orange-500 to-yellow-500 rounded-xl p-5 mt-4 text-white">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-orange-100 text-sm uppercase tracking-wide font-bold">
                                        Total puesto en Guatemala
                                    </p>
                                    <p class="text-4xl font-black mt-1">{{ fmt(cotizacion.total_gtq) }}</p>
                                </div>
                                <span class="text-5xl">🇬🇹</span>
                            </div>
                        </div>
                    </div>

                    <!-- Anticipo y CTA -->
                    <div class="bg-white rounded-2xl shadow p-6">
                        <div class="flex items-center gap-4 mb-5">
                            <div class="bg-green-100 rounded-xl p-3">
                                <span class="text-3xl">💳</span>
                            </div>
                            <div>
                                <p class="font-bold text-gray-700">Anticipo para reservar</p>
                                <p class="text-3xl font-black text-green-600">{{ fmt(cotizacion.anticipo_gtq) }}</p>
                                <p class="text-xs text-gray-400">El resto lo pagás al recibir tu producto</p>
                            </div>
                        </div>

                        <!-- Tiempo estimado -->
                        <div class="bg-blue-50 rounded-xl p-4 mb-5 flex items-center gap-3">
                            <span class="text-3xl">⏱️</span>
                            <div>
                                <p class="text-xs text-blue-600 font-bold uppercase tracking-wide">
                                    Tiempo estimado de entrega
                                </p>
                                <p class="font-bold text-blue-800">{{ cotizacion.tiempo_estimado }}</p>
                            </div>
                        </div>

                        <button @click="mostrarPedido = true"
                                class="w-full bg-green-500 text-white py-4 rounded-xl font-black text-xl hover:bg-green-600 transition shadow-lg">
                            🛒 ¡Lo quiero! Reservar ahora
                        </button>
                        <p class="text-center text-xs text-gray-400 mt-2">
                            Solo necesitás subir el comprobante del anticipo de {{ fmt(cotizacion.anticipo_gtq) }}
                        </p>
                    </div>

                    <!-- Pasos -->
                    <div class="bg-white rounded-2xl shadow p-6">
                        <h3 class="font-bold text-gray-700 mb-4">📋 ¿Cómo funciona?</h3>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <span class="bg-orange-500 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">1</span>
                                <div>
                                    <p class="font-medium text-gray-700 text-sm">Pagás el anticipo</p>
                                    <p class="text-xs text-gray-400">Transferís {{ fmt(cotizacion.anticipo_gtq) }} y subís el comprobante</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="bg-orange-500 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">2</span>
                                <div>
                                    <p class="font-medium text-gray-700 text-sm">Confirmamos tu pedido</p>
                                    <p class="text-xs text-gray-400">Te contactamos por WhatsApp para confirmar</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="bg-orange-500 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">3</span>
                                <div>
                                    <p class="font-medium text-gray-700 text-sm">Compramos en Amazon</p>
                                    <p class="text-xs text-gray-400">Nosotros hacemos todo el proceso de importación</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="bg-orange-500 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">4</span>
                                <div>
                                    <p class="font-medium text-gray-700 text-sm">Te lo entregamos 🇬🇹</p>
                                    <p class="text-xs text-gray-400">En {{ cotizacion.tiempo_estimado }} en tu dirección</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL PEDIDO -->
        <div v-if="mostrarPedido"
             class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">

                <div class="bg-gradient-to-r from-green-500 to-emerald-500 text-white p-5 rounded-t-2xl sticky top-0">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold">🛒 Reservar producto</h3>
                            <p class="text-green-100 text-sm">Anticipo: {{ fmt(cotizacion.anticipo_gtq) }}</p>
                        </div>
                        <button @click="mostrarPedido = false" class="text-white text-2xl">✕</button>
                    </div>
                </div>

                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Nombre *</label>
                            <input v-model="formPedido.nombre" type="text"
                                   class="w-full border-2 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400" />
                            <p v-if="formPedido.errors.nombre" class="text-red-500 text-xs mt-1">{{ formPedido.errors.nombre }}</p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Apellido *</label>
                            <input v-model="formPedido.apellido" type="text"
                                   class="w-full border-2 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Teléfono *</label>
                            <input v-model="formPedido.telefono" type="text"
                                   class="w-full border-2 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Email</label>
                            <input v-model="formPedido.email" type="email"
                                   class="w-full border-2 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">DPI</label>
                            <input v-model="formPedido.dpi" type="text"
                                   class="w-full border-2 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Dirección de entrega *</label>
                        <textarea v-model="formPedido.direccion" rows="2"
                                  class="w-full border-2 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400"
                                  placeholder="Zona, municipio, departamento..."></textarea>
                        <p v-if="formPedido.errors.direccion" class="text-red-500 text-xs mt-1">{{ formPedido.errors.direccion }}</p>
                    </div>

                    <div class="bg-yellow-50 border-2 border-yellow-300 rounded-xl p-4">
                        <p class="font-bold text-yellow-800 mb-1">💳 Comprobante de anticipo</p>
                        <p class="text-sm text-yellow-700 mb-3">
                            Transferí <strong>{{ fmt(cotizacion.anticipo_gtq) }}</strong> y subí la imagen del comprobante
                        </p>
                        <input type="file" accept=".jpg,.jpeg,.png,.pdf"
                               @change="e => formPedido.comprobante_anticipo = e.target.files[0]"
                               class="w-full text-sm" />
                        <p v-if="formPedido.errors.comprobante_anticipo" class="text-red-500 text-xs mt-1">
                            {{ formPedido.errors.comprobante_anticipo }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Notas adicionales</label>
                        <textarea v-model="formPedido.notas" rows="2"
                                  class="w-full border-2 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400"
                                  placeholder="Color, talla, variante del producto..."></textarea>
                    </div>
                </div>

                <div class="p-5 border-t flex gap-3 sticky bottom-0 bg-white rounded-b-2xl">
                    <button @click="mostrarPedido = false"
                            class="flex-1 border-2 border-gray-300 text-gray-600 py-3 rounded-xl text-sm hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button @click="enviarPedido" :disabled="formPedido.processing"
                            class="flex-1 bg-green-500 text-white py-3 rounded-xl text-sm font-bold hover:bg-green-600 disabled:opacity-50">
                        {{ formPedido.processing ? '⏳ Enviando...' : '✅ Confirmar pedido' }}
                    </button>
                </div>
            </div>
        </div>

        <footer class="bg-gray-800 text-gray-400 text-center py-6 text-xs mt-10">
            <p class="font-bold text-white mb-1">📦 JOC-YAPLA</p>
            <p>Importaciones desde Amazon a Guatemala</p>
        </footer>
    </div>
</template>
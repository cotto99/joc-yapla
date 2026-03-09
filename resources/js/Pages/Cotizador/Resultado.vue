<script setup>
import { useForm, Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({ cotizacion: Object })
const mostrarPedido = ref(false)

const formPedido = useForm({
    nombre:               '',
    apellido:             '',
    telefono:             '',
    email:                '',
    dpi:                  '',
    direccion:            '',
    envio_gratis:         false,
    comprobante_anticipo: null,
    notas:                '',
})

const anticipoMostrado = computed(() => {
    if (!props.cotizacion) return 0
    if (formPedido.envio_gratis) {
        const totalSinEnvio = props.cotizacion.total_gtq - props.cotizacion.costo_entrega
        return totalSinEnvio * (props.cotizacion.anticipo_gtq / props.cotizacion.total_gtq)
    }
    return props.cotizacion.anticipo_gtq
})

const totalMostrado = computed(() => {
    if (!props.cotizacion) return 0
    if (formPedido.envio_gratis) {
        return props.cotizacion.total_gtq - props.cotizacion.costo_entrega
    }
    return props.cotizacion.total_gtq
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
    <Head title="Tu Cotizacion - JOC-YAPLA" />

    <div class="min-h-screen bg-gray-950">

        <!-- HEADER -->
        <header class="bg-black border-b border-yellow-500/30 shadow-lg shadow-yellow-500/10">
            <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
                <img src="/images/logo-safari.png" alt="Safari" class="h-14 w-auto" />
                <a href="/" class="border border-yellow-500 text-yellow-400 hover:bg-yellow-500 hover:text-black px-4 py-2 rounded-lg text-sm font-bold transition">
                    Nueva cotizacion
                </a>
            </div>
        </header>

        <div class="max-w-4xl mx-auto px-4 py-8">

            <div class="text-center mb-8">
                <div class="text-5xl mb-3">✅</div>
                <h2 class="text-3xl font-black text-white">Tu cotizacion esta lista!</h2>
                <p class="text-gray-400 mt-2">Aca te mostramos el costo total de traer tu producto a Guatemala</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Columna izquierda: producto -->
                <div class="lg:col-span-1">
                    <div class="bg-gray-900 border border-yellow-500/20 rounded-2xl p-5 sticky top-6">
                        <img v-if="cotizacion.imagen_url"
                             :src="cotizacion.imagen_url"
                             class="w-full h-52 object-contain rounded-xl border border-gray-700 bg-gray-800 mb-4" />
                        <div v-else class="w-full h-52 bg-gray-800 border border-gray-700 rounded-xl flex items-center justify-center mb-4">
                            <span class="text-6xl">📦</span>
                        </div>

                        <h3 class="font-bold text-white text-sm mb-2">
                            {{ cotizacion.nombre_producto || 'Tu producto de Amazon' }}
                        </h3>

                        <a :href="cotizacion.link_amazon" target="_blank"
                           class="block w-full text-center border border-yellow-500 text-yellow-400 hover:bg-yellow-500 hover:text-black py-2 rounded-lg text-sm font-bold transition mb-3">
                            Ver en Amazon →
                        </a>

                        <div class="text-xs text-gray-500 space-y-1">
                            <div class="flex justify-between">
                                <span>Tipo de cambio:</span>
                                <span class="font-semibold text-gray-300">Q{{ cotizacion.tipo_cambio }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Tiempo estimado:</span>
                                <span class="font-semibold text-gray-300">{{ cotizacion.tiempo_estimado }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna derecha -->
                <div class="lg:col-span-2 space-y-4">

                    <!-- Desglose -->
                    <div class="bg-gray-900 border border-yellow-500/20 rounded-2xl p-6">
                        <h3 class="font-bold text-yellow-400 mb-4 text-lg">Desglose de costos</h3>
                        <div class="space-y-1">
                            <div class="flex justify-between items-center py-3 border-b border-gray-700">
                                <div>
                                    <p class="font-medium text-white">Precio original en Amazon</p>
                                    <p class="text-xs text-gray-500">Precio en dolares</p>
                                </div>
                                <span class="font-mono font-bold text-white text-lg">{{ fmt(cotizacion.precio_usd, true) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-700">
                                <div>
                                    <p class="font-medium text-white">Precio convertido a GTQ</p>
                                    <p class="text-xs text-gray-500">Al tipo de cambio de Q{{ cotizacion.tipo_cambio }}</p>
                                </div>
                                <span class="font-mono text-gray-300">{{ fmt(cotizacion.precio_gtq) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-700">
                                <div>
                                    <p class="font-medium text-white">Flete internacional</p>
                                    <p class="text-xs text-gray-500">Envio desde USA a Guatemala</p>
                                </div>
                                <span class="font-mono text-gray-300">{{ fmt(cotizacion.costo_flete) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-700">
                                <div>
                                    <p class="font-medium text-white">Impuestos de importacion</p>
                                    <p class="text-xs text-gray-500">Aranceles SAT Guatemala</p>
                                </div>
                                <span class="font-mono text-gray-300">{{ fmt(cotizacion.costo_arancel) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-700">
                                <div>
                                    <p class="font-medium text-white">Comision del servicio</p>
                                    <p class="text-xs text-gray-500">Gestion y tramites de importacion</p>
                                </div>
                                <span class="font-mono text-gray-300">{{ fmt(cotizacion.costo_comision) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-700">
                                <div>
                                    <p class="font-medium text-white">Entrega en Guatemala</p>
                                    <p class="text-xs text-gray-500">Envio a tu direccion</p>
                                </div>
                                <span class="font-mono text-gray-300">{{ fmt(cotizacion.costo_entrega) }}</span>
                            </div>
                        </div>

                        <div class="bg-yellow-500 rounded-xl p-5 mt-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-yellow-900 text-sm uppercase tracking-wide font-bold">Total puesto en Guatemala</p>
                                    <p class="text-4xl font-black text-black mt-1">{{ fmt(cotizacion.total_gtq) }}</p>
                                </div>
                                <span class="text-5xl">🇬🇹</span>
                            </div>
                        </div>
                    </div>

                    <!-- Anticipo y CTA -->
                    <div class="bg-gray-900 border border-yellow-500/20 rounded-2xl p-6">
                        <div class="flex items-center gap-4 mb-5">
                            <div class="bg-yellow-500/20 rounded-xl p-3">
                                <span class="text-3xl">💳</span>
                            </div>
                            <div>
                                <p class="font-bold text-gray-300">Anticipo para reservar</p>
                                <p class="text-3xl font-black text-yellow-400">{{ fmt(cotizacion.anticipo_gtq) }}</p>
                                <p class="text-xs text-gray-500">El resto lo pagas al recibir tu producto</p>
                            </div>
                        </div>

                        <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 mb-5 flex items-center gap-3">
                            <span class="text-3xl">⏱️</span>
                            <div>
                                <p class="text-xs text-yellow-400 font-bold uppercase tracking-wide">Tiempo estimado de entrega</p>
                                <p class="font-bold text-white">{{ cotizacion.tiempo_estimado }}</p>
                            </div>
                        </div>

                        <button @click="mostrarPedido = true"
                                class="w-full bg-yellow-500 hover:bg-yellow-400 text-black py-4 rounded-xl font-black text-xl transition shadow-lg shadow-yellow-500/30">
                            🛒 Lo quiero! Reservar ahora
                        </button>
                        <p class="text-center text-xs text-gray-500 mt-2">
                            Solo necesitas subir el comprobante del anticipo de {{ fmt(cotizacion.anticipo_gtq) }}
                        </p>
                    </div>

                    <!-- Pasos -->
                    <div class="bg-gray-900 border border-yellow-500/20 rounded-2xl p-6">
                        <h3 class="font-bold text-yellow-400 mb-4">Como funciona?</h3>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <span class="bg-yellow-500 text-black text-xs font-black w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">1</span>
                                <div>
                                    <p class="font-medium text-white text-sm">Pagas el anticipo</p>
                                    <p class="text-xs text-gray-500">Transferis {{ fmt(cotizacion.anticipo_gtq) }} y subis el comprobante</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="bg-yellow-500 text-black text-xs font-black w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">2</span>
                                <div>
                                    <p class="font-medium text-white text-sm">Confirmamos tu pedido</p>
                                    <p class="text-xs text-gray-500">Te contactamos por WhatsApp para confirmar</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="bg-yellow-500 text-black text-xs font-black w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">3</span>
                                <div>
                                    <p class="font-medium text-white text-sm">Compramos en Amazon</p>
                                    <p class="text-xs text-gray-500">Nosotros hacemos todo el proceso de importacion</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="bg-yellow-500 text-black text-xs font-black w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">4</span>
                                <div>
                                    <p class="font-medium text-white text-sm">Te lo entregamos 🇬🇹</p>
                                    <p class="text-xs text-gray-500">En {{ cotizacion.tiempo_estimado }} en tu direccion</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL PEDIDO -->
        <div v-if="mostrarPedido"
             class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4">
            <div class="bg-gray-900 border border-yellow-500/30 rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">

                <div class="bg-yellow-500 text-black p-5 rounded-t-2xl sticky top-0 z-10">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-black">🛒 Reservar producto</h3>
                            <p class="text-yellow-900 text-sm font-medium">
                                Anticipo: <strong>{{ fmt(anticipoMostrado) }}</strong>
                            </p>
                        </div>
                        <button @click="mostrarPedido = false" class="text-black text-2xl font-bold hover:text-yellow-800">✕</button>
                    </div>
                </div>

                <div class="p-5 space-y-4">

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Nombre *</label>
                            <input v-model="formPedido.nombre" type="text"
                                   class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-500" />
                            <p v-if="formPedido.errors.nombre" class="text-red-400 text-xs mt-1">{{ formPedido.errors.nombre }}</p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Apellido *</label>
                            <input v-model="formPedido.apellido" type="text"
                                   class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-500" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Telefono *</label>
                            <input v-model="formPedido.telefono" type="text"
                                   class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-500" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Email</label>
                            <input v-model="formPedido.email" type="email"
                                   class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-500" />
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm text-gray-400 mb-1">DPI</label>
                            <input v-model="formPedido.dpi" type="text"
                                   class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-500" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Direccion de entrega *</label>
                        <textarea v-model="formPedido.direccion" rows="2"
                                  class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-500 placeholder-gray-500"
                                  placeholder="Zona, municipio, departamento..."></textarea>
                        <p v-if="formPedido.errors.direccion" class="text-red-400 text-xs mt-1">{{ formPedido.errors.direccion }}</p>
                    </div>

                    <!-- CHECKBOX ENVIO GRATIS -->
                    <label class="flex items-start gap-3 p-4 border-2 rounded-xl cursor-pointer transition"
                           :class="formPedido.envio_gratis ? 'border-yellow-500 bg-yellow-500/10' : 'border-gray-700 hover:border-yellow-500/50'">
                        <input type="checkbox"
                               v-model="formPedido.envio_gratis"
                               class="mt-0.5 w-5 h-5 accent-yellow-500 flex-shrink-0" />
                        <div>
                            <p class="font-bold text-white text-sm">
                                ⚡ Eres de <span class="text-yellow-400">Puerto Barrios o Santo Tomas</span>?
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                El envio es <strong class="text-yellow-400">gratis</strong> para estas areas.
                                Marca esta casilla y te descontamos <strong class="text-yellow-400">{{ fmt(cotizacion.costo_entrega) }}</strong>.
                            </p>
                        </div>
                    </label>

                    <!-- RESUMEN DINAMICO -->
                    <div class="bg-gray-800 border border-yellow-500/30 rounded-xl p-4 text-sm space-y-2">
                        <div class="flex justify-between text-gray-300">
                            <span>Total del pedido:</span>
                            <span class="font-bold text-white">{{ fmt(totalMostrado) }}</span>
                        </div>
                        <div v-if="formPedido.envio_gratis" class="flex justify-between text-yellow-400 text-xs">
                            <span>Descuento envio gratis (Barrios / Sto. Tomas):</span>
                            <span>- {{ fmt(cotizacion.costo_entrega) }}</span>
                        </div>
                        <div class="flex justify-between font-black text-base border-t border-gray-700 pt-2">
                            <span class="text-gray-300">Anticipo a pagar ahora:</span>
                            <span class="text-yellow-400">{{ fmt(anticipoMostrado) }}</span>
                        </div>
                    </div>

                    <!-- Comprobante anticipo -->
                    <div class="bg-yellow-500/10 border-2 border-yellow-500/40 rounded-xl p-4">
                        <p class="font-bold text-yellow-400 mb-1">💳 Comprobante de anticipo</p>
                        <p class="text-sm text-yellow-300/70 mb-3">
                            Transfiere <strong class="text-yellow-400">{{ fmt(anticipoMostrado) }}</strong> y sube la imagen del comprobante
                        </p>
                        <input type="file" accept=".jpg,.jpeg,.png,.pdf"
                               @change="e => formPedido.comprobante_anticipo = e.target.files[0]"
                               class="w-full text-sm text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-yellow-500 file:text-black hover:file:bg-yellow-400 file:font-bold" />
                        <p v-if="formPedido.errors.comprobante_anticipo" class="text-red-400 text-xs mt-1">
                            {{ formPedido.errors.comprobante_anticipo }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Notas adicionales</label>
                        <textarea v-model="formPedido.notas" rows="2"
                                  class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-500 placeholder-gray-500"
                                  placeholder="Color, talla, variante del producto..."></textarea>
                    </div>
                </div>

                <div class="p-5 border-t border-gray-700 flex gap-3 sticky bottom-0 bg-gray-900 rounded-b-2xl">
                    <button @click="mostrarPedido = false"
                            class="flex-1 border border-gray-600 text-gray-400 py-3 rounded-xl text-sm hover:bg-gray-800">
                        Cancelar
                    </button>
                    <button @click="enviarPedido" :disabled="formPedido.processing"
                            class="flex-1 bg-yellow-500 hover:bg-yellow-400 text-black py-3 rounded-xl text-sm font-black transition disabled:opacity-50">
                        {{ formPedido.processing ? '⏳ Enviando...' : '⚡ Confirmar pedido' }}
                    </button>
                </div>
            </div>
        </div>

        <footer class="bg-black border-t border-yellow-500/20 text-gray-500 text-center py-6 text-xs mt-10">
            <img src="/images/logo-safari.png" alt="Safari" class="h-10 w-auto mx-auto mb-2" />
            <p>Importaciones desde Amazon a Guatemala</p>
        </footer>
    </div>
</template>
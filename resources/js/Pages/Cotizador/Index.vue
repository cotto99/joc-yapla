<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'

const page = usePage()

const form = useForm({
    link_amazon:     '',
    precio_usd:      '',
    nombre_producto: '',
})

const cotizacion    = ref(page.props.flash?.cotizacion || null)
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
    if (!cotizacion.value) return 0
    if (formPedido.envio_gratis) {
        const totalSinEnvio = cotizacion.value.total_gtq - cotizacion.value.costo_entrega
        return totalSinEnvio * (cotizacion.value.anticipo_gtq / cotizacion.value.total_gtq)
    }
    return cotizacion.value.anticipo_gtq
})

const totalMostrado = computed(() => {
    if (!cotizacion.value) return 0
    if (formPedido.envio_gratis) {
        return cotizacion.value.total_gtq - cotizacion.value.costo_entrega
    }
    return cotizacion.value.total_gtq
})

function cotizar() {
    form.post(route('cotizador.cotizar'), {
        onSuccess: () => {
            cotizacion.value = page.props.flash?.cotizacion
        }
    })
}

function enviarPedido() {
    formPedido.post(route('cotizador.pedido', cotizacion.value.id), {
        forceFormData: true,
    })
}

function fmt(val, moneda = 'GTQ') {
    return new Intl.NumberFormat('es-GT', {
        style: 'currency',
        currency: moneda === 'USD' ? 'USD' : 'GTQ'
    }).format(val || 0)
}
</script>

<template>
    <Head title="JOC-YAPLA — Cotizador Amazon Guatemala" />

    <div class="min-h-screen bg-gray-950">

        <!-- HEADER -->
        <header class="bg-black border-b border-yellow-500/30 shadow-lg shadow-yellow-500/10">
            <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">
                <a href="/tracking"
                   class="border border-yellow-500 text-yellow-400 hover:bg-yellow-500 hover:text-black px-4 py-2 rounded-lg text-sm font-bold transition">
                    📦 Rastrear pedido
                </a>
            </div>
        </header>

        <div class="max-w-5xl mx-auto px-4 py-8">

            <!-- HERO -->
            <div class="text-center mb-10">
                <h2 class="text-3xl lg:text-4xl font-black text-white mb-3">
                    !Cotiza tu Producto de <br>
                    <span class="text-yellow-400">Amazon!</span>
                </h2>
                <p class="text-gray-400 text-lg">
                    Pegá el link, ingresá el precio y te calculamos el costo total al instante
                </p>
            </div>

            <!-- FORMULARIO COTIZADOR -->
            <div class="bg-gray-900 border border-yellow-500/20 rounded-2xl shadow-xl p-6 lg:p-8 mb-8">
                <h3 class="text-lg font-bold text-yellow-400 mb-5">🔍 Cotizá tu producto</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Link de Amazon *</label>
                        <input v-model="form.link_amazon" type="url"
                               placeholder="https://www.amazon.com/dp/B08N5WRWNW"
                               class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition placeholder-gray-500" />
                        <p class="text-xs text-gray-500 mt-1">💡 Copiá el link completo del producto en Amazon</p>
                        <p v-if="form.errors.link_amazon" class="text-red-400 text-xs mt-1">{{ form.errors.link_amazon }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Precio en Amazon (USD) *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-yellow-500 font-bold">$</span>
                                <input v-model="form.precio_usd" type="number" step="0.01" min="0"
                                       placeholder="0.00"
                                       class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl pl-8 pr-4 py-3 text-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition placeholder-gray-500" />
                            </div>
                            <p v-if="form.errors.precio_usd" class="text-red-400 text-xs mt-1">{{ form.errors.precio_usd }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">
                                Nombre del producto
                                <span class="text-gray-500 font-normal">(opcional)</span>
                            </label>
                            <input v-model="form.nombre_producto" type="text"
                                   placeholder="Ej: Auriculares Sony WH-1000XM5"
                                   class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition placeholder-gray-500" />
                        </div>
                    </div>
                </div>

                <button 
    @click="cotizar" 
    :disabled="form.processing"
    class="w-full mt-6 bg-yellow-600 hover:bg-yellow-400 text-black py-4 rounded-xl font-black text-lg transition duration-300 disabled:opacity-50 shadow-lg shadow-yellow-500/30">
    
    {{ form.processing ? '⏳ Calculando...' : '⚡ Calcular precio en Guatemala' }}

</button>
            </div>

            <!-- RESULTADO -->
            <div v-if="cotizacion" class="bg-gray-900 border border-yellow-500/20 rounded-2xl shadow-xl overflow-hidden mb-8">
                <div class="bg-yellow-500 p-5">
                    <h3 class="text-xl font-black text-black">✅ Tu cotización está lista</h3>
                    <p class="text-yellow-900 text-sm mt-1">Tipo de cambio: Q{{ cotizacion.tipo_cambio }} por dólar</p>
                </div>

                <div class="p-6">
                    <div class="flex flex-col lg:flex-row gap-6">
                        <div class="lg:w-48 flex-shrink-0">
                            <img v-if="cotizacion.imagen_url"
                                 :src="cotizacion.imagen_url"
                                 :alt="cotizacion.nombre_producto"
                                 class="w-full h-48 object-contain rounded-xl border border-gray-700 bg-gray-800" />
                            <div v-else class="w-full h-48 bg-gray-800 rounded-xl flex items-center justify-center border border-gray-700">
                                <span class="text-5xl">📦</span>
                            </div>
                            <p v-if="cotizacion.nombre_producto" class="text-sm font-medium text-gray-300 mt-2 text-center">
                                {{ cotizacion.nombre_producto }}
                            </p>
                            <a :href="cotizacion.link_amazon" target="_blank"
                               class="block text-center text-xs text-yellow-400 hover:underline mt-1">
                                Ver en Amazon →
                            </a>
                        </div>

                        <div class="flex-1">
                            <p class="text-xs font-bold text-yellow-500 uppercase tracking-wide mb-3">Desglose de costos</p>
                            <div class="space-y-1">
                                <div class="flex justify-between items-center py-2 border-b border-gray-700">
                                    <span class="text-gray-400 text-sm">Precio original en Amazon</span>
                                    <span class="font-mono font-bold text-white">{{ fmt(cotizacion.precio_usd, 'USD') }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-700">
                                    <span class="text-gray-400 text-sm">Precio en Quetzales</span>
                                    <span class="font-mono text-gray-300">{{ fmt(cotizacion.precio_gtq) }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-700">
                                    <span class="text-gray-400 text-sm">Flete internacional</span>
                                    <span class="font-mono text-gray-300">{{ fmt(cotizacion.costo_flete) }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-700">
                                    <span class="text-gray-400 text-sm">Impuestos de importación</span>
                                    <span class="font-mono text-gray-300">{{ fmt(cotizacion.costo_arancel) }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-700">
                                    <span class="text-gray-400 text-sm">Comisión del servicio</span>
                                    <span class="font-mono text-gray-300">{{ fmt(cotizacion.costo_comision) }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-700">
                                    <span class="text-gray-400 text-sm">Entrega en Guatemala</span>
                                    <span class="font-mono text-gray-300">{{ fmt(cotizacion.costo_entrega) }}</span>
                                </div>
                            </div>

                            <div class="bg-yellow-500 rounded-xl p-4 mt-4 flex justify-between items-center">
                                <div>
                                    <p class="text-xs text-yellow-900 uppercase tracking-wide font-bold">Total puesto en Guatemala</p>
                                    <p class="text-3xl font-black text-black mt-1">{{ fmt(cotizacion.total_gtq) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-yellow-900">Anticipo requerido</p>
                                    <p class="text-xl font-bold text-black">{{ fmt(cotizacion.anticipo_gtq) }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 mt-4 bg-gray-800 border border-gray-700 rounded-xl p-3">
                                <span class="text-2xl">⏱️</span>
                                <div>
                                    <p class="text-xs text-yellow-400 font-bold uppercase">Tiempo estimado de entrega</p>
                                    <p class="text-sm font-semibold text-white">{{ cotizacion.tiempo_estimado }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-gray-700 pt-5">
                        <button @click="mostrarPedido = true"
                                class="w-full bg-yellow-500 hover:bg-yellow-400 text-black py-4 rounded-xl font-black text-lg transition shadow-lg shadow-yellow-500/30">
                            🛒 ¡Lo quiero! Reservar ahora
                        </button>
                        <p class="text-center text-xs text-gray-500 mt-2">
                            Solo se te pedirá tus datos y el comprobante del anticipo de {{ fmt(cotizacion.anticipo_gtq) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- CÓMO FUNCIONA -->
            <div v-if="!cotizacion" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gray-900 border border-yellow-500/20 rounded-2xl p-5 text-center">
                    <span class="text-4xl">🔗</span>
                    <h4 class="font-bold text-white mt-3 mb-1">1. Pegá el link</h4>
                    <p class="text-sm text-gray-400">Copiá el link del producto en Amazon y pegalo aquí</p>
                </div>
                <div class="bg-gray-900 border border-yellow-500/20 rounded-2xl p-5 text-center">
                    <span class="text-4xl">⚡</span>
                    <h4 class="font-bold text-white mt-3 mb-1">2. Calculamos todo</h4>
                    <p class="text-sm text-gray-400">Flete, impuestos, comisión y tipo de cambio en tiempo real</p>
                </div>
                <div class="bg-gray-900 border border-yellow-500/20 rounded-2xl p-5 text-center">
                    <span class="text-4xl">🚀</span>
                    <h4 class="font-bold text-white mt-3 mb-1">3. Reservá tu producto</h4>
                    <p class="text-sm text-gray-400">Pagá el anticipo y nosotros nos encargamos del resto</p>
                </div>
            </div>
        </div>

        <!-- MODAL PEDIDO -->
        <div v-if="mostrarPedido && cotizacion"
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
                            <label class="block text-sm text-gray-400 mb-1">Teléfono *</label>
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
                        <label class="block text-sm text-gray-400 mb-1">Dirección de entrega *</label>
                        <textarea v-model="formPedido.direccion" rows="2"
                                  class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-500 placeholder-gray-500"
                                  placeholder="Zona, municipio, departamento..."></textarea>
                        <p v-if="formPedido.errors.direccion" class="text-red-400 text-xs mt-1">{{ formPedido.errors.direccion }}</p>
                    </div>

                    <!-- CHECKBOX ENVÍO GRATIS -->
                    <label class="flex items-start gap-3 p-4 border-2 rounded-xl cursor-pointer transition"
                           :class="formPedido.envio_gratis
                               ? 'border-yellow-500 bg-yellow-500/10'
                               : 'border-gray-700 hover:border-yellow-500/50'">
                        <input type="checkbox"
                               v-model="formPedido.envio_gratis"
                               class="mt-0.5 w-5 h-5 accent-yellow-500 flex-shrink-0" />
                        <div>
                            <p class="font-bold text-white text-sm">
                                ⚡ ¿Sos de <span class="text-yellow-400">Barrios o Santo Tomás</span>?
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                El envío es <strong class="text-yellow-400">gratis</strong> para estas áreas.
                                Marcá esta casilla y te descontamos <strong class="text-yellow-400">{{ fmt(cotizacion.costo_entrega) }}</strong>.
                            </p>
                        </div>
                    </label>

                    <!-- RESUMEN DINÁMICO -->
                    <div class="bg-gray-800 border border-yellow-500/30 rounded-xl p-4 text-sm space-y-2">
                        <div class="flex justify-between text-gray-300">
                            <span>Total del pedido:</span>
                            <span class="font-bold text-white">{{ fmt(totalMostrado) }}</span>
                        </div>
                        <div v-if="formPedido.envio_gratis" class="flex justify-between text-yellow-400 text-xs">
                            <span>✅ Descuento envío gratis (Barrios / Sto. Tomás):</span>
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
                            Transferí <strong class="text-yellow-400">{{ fmt(anticipoMostrado) }}</strong> y subí la imagen del comprobante
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

        <!-- FOOTER -->
        <footer class="bg-black border-t border-yellow-500/20 text-gray-500 text-center py-6 text-xs mt-10">
            <img src="/images/logo-safari.png" alt="Safari" class="h-10 w-auto mx-auto mb-2" />
            <p>Importaciones desde Amazon a Guatemala — Todos los derechos reservados</p>
        </footer>
    </div>
</template>
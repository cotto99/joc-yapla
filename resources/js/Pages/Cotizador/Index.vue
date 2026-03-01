<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'

const page = usePage()

const form = useForm({
    link_amazon:      '',
    precio_usd:       '',
    imagen_url:       '',
    nombre_producto:  '',
})

const cotizacion      = ref(page.props.flash?.cotizacion || null)
const mostrarPedido   = ref(false)

const formPedido = useForm({
    nombre:     '',
    apellido:   '',
    telefono:   '',
    email:      '',
    dpi:        '',
    direccion:  '',
    comprobante_anticipo: null,
    notas:      '',
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

    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-yellow-50">

        <!-- HEADER -->
        <header class="bg-gradient-to-r from-orange-500 to-yellow-500 text-white shadow-lg">
            <div class="max-w-5xl mx-auto px-4 py-5 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-black tracking-tight">📦 JOC-YAPLA</h1>
                    <p class="text-orange-100 text-xs mt-0.5">Importaciones desde Amazon a Guatemala</p>
                </div>
                <div class="text-right text-xs text-orange-100">
                    <p>¿Dudas? Escríbenos</p>
                    <p class="font-bold text-white text-sm">WhatsApp</p>
                </div>
            </div>
        </header>

        <div class="max-w-5xl mx-auto px-4 py-8">

            <!-- HERO -->
            <div class="text-center mb-10">
                <h2 class="text-3xl lg:text-4xl font-black text-gray-800 mb-3">
                    ¿Cuánto cuesta traer tu producto<br>
                    <span class="text-orange-500">de Amazon a Guatemala?</span>
                </h2>
                <p class="text-gray-500 text-lg">
                    Pegá el link, ingresá el precio y te calculamos el costo total al instante
                </p>
            </div>

            <!-- FORMULARIO COTIZADOR -->
            <div class="bg-white rounded-2xl shadow-xl p-6 lg:p-8 mb-8">
                <h3 class="text-lg font-bold text-gray-700 mb-5">🔍 Cotizá tu producto</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Link de Amazon *
                        </label>
                        <input v-model="form.link_amazon" type="url"
                               placeholder="https://www.amazon.com/producto..."
                               class="w-full border-2 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition" />
                        <p v-if="form.errors.link_amazon" class="text-red-500 text-xs mt-1">{{ form.errors.link_amazon }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Precio en Amazon (USD) *
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-400 font-bold">$</span>
                            <input v-model="form.precio_usd" type="number" step="0.01" min="0"
                                   placeholder="0.00"
                                   class="w-full border-2 rounded-xl pl-8 pr-4 py-3 text-sm focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition" />
                        </div>
                        <p v-if="form.errors.precio_usd" class="text-red-500 text-xs mt-1">{{ form.errors.precio_usd }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Nombre del producto
                        </label>
                        <input v-model="form.nombre_producto" type="text"
                               placeholder="Ej: Auriculares Sony WH-1000XM5"
                               class="w-full border-2 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition" />
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            URL de imagen del producto (opcional)
                        </label>
                        <input v-model="form.imagen_url" type="url"
                               placeholder="https://m.media-amazon.com/images/..."
                               class="w-full border-2 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition" />
                        <p class="text-xs text-gray-400 mt-1">
                            💡 En Amazon, clic derecho en la foto del producto → "Copiar dirección de imagen"
                        </p>
                    </div>
                </div>

                <button @click="cotizar" :disabled="form.processing"
                        class="w-full bg-gradient-to-r from-orange-500 to-yellow-500 text-white py-4 rounded-xl font-bold text-lg hover:from-orange-600 hover:to-yellow-600 transition disabled:opacity-50 shadow-lg">
                    {{ form.processing ? '⏳ Calculando...' : '🧮 Calcular precio en Guatemala' }}
                </button>
            </div>

            <!-- RESULTADO COTIZACIÓN -->
            <div v-if="cotizacion" class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">

                <!-- Header resultado -->
                <div class="bg-gradient-to-r from-orange-500 to-yellow-500 p-5 text-white">
                    <h3 class="text-xl font-bold">✅ Tu cotización está lista</h3>
                    <p class="text-orange-100 text-sm mt-1">Tipo de cambio: Q{{ cotizacion.tipo_cambio }} por dólar</p>
                </div>

                <div class="p-6">
                    <div class="flex flex-col lg:flex-row gap-6">

                        <!-- Imagen y nombre -->
                        <div class="lg:w-48 flex-shrink-0">
                            <img v-if="cotizacion.imagen_url"
                                 :src="cotizacion.imagen_url"
                                 :alt="cotizacion.nombre_producto"
                                 class="w-full h-48 object-contain rounded-xl border bg-gray-50" />
                            <div v-else class="w-full h-48 bg-gray-100 rounded-xl flex items-center justify-center">
                                <span class="text-5xl">📦</span>
                            </div>
                            <p v-if="cotizacion.nombre_producto"
                               class="text-sm font-medium text-gray-700 mt-2 text-center">
                                {{ cotizacion.nombre_producto }}
                            </p>
                            <a :href="cotizacion.link_amazon" target="_blank"
                               class="block text-center text-xs text-orange-500 hover:underline mt-1">
                                Ver en Amazon →
                            </a>
                        </div>

                        <!-- Desglose -->
                        <div class="flex-1">
                            <p class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-3">Desglose de costos</p>

                            <div class="space-y-2">
                                <div class="flex justify-between items-center py-2 border-b">
                                    <span class="text-gray-600 text-sm">Precio original en Amazon</span>
                                    <span class="font-mono font-bold text-gray-800">{{ fmt(cotizacion.precio_usd, 'USD') }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b">
                                    <span class="text-gray-600 text-sm">Precio en Quetzales</span>
                                    <span class="font-mono text-gray-700">{{ fmt(cotizacion.precio_gtq) }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b">
                                    <span class="text-gray-600 text-sm">Flete internacional</span>
                                    <span class="font-mono text-gray-700">{{ fmt(cotizacion.costo_flete) }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b">
                                    <span class="text-gray-600 text-sm">Impuestos de importación</span>
                                    <span class="font-mono text-gray-700">{{ fmt(cotizacion.costo_arancel) }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b">
                                    <span class="text-gray-600 text-sm">Comisión del servicio</span>
                                    <span class="font-mono text-gray-700">{{ fmt(cotizacion.costo_comision) }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b">
                                    <span class="text-gray-600 text-sm">Entrega en Guatemala</span>
                                    <span class="font-mono text-gray-700">{{ fmt(cotizacion.costo_entrega) }}</span>
                                </div>
                            </div>

                            <!-- Total -->
                            <div class="bg-orange-50 border-2 border-orange-300 rounded-xl p-4 mt-4 flex justify-between items-center">
                                <div>
                                    <p class="text-xs text-orange-600 uppercase tracking-wide font-bold">Total puesto en Guatemala</p>
                                    <p class="text-3xl font-black text-orange-600 mt-1">{{ fmt(cotizacion.total_gtq) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">Anticipo requerido</p>
                                    <p class="text-xl font-bold text-gray-700">{{ fmt(cotizacion.anticipo_gtq) }}</p>
                                </div>
                            </div>

                            <!-- Tiempo estimado -->
                            <div class="flex items-center gap-2 mt-4 bg-blue-50 rounded-xl p-3">
                                <span class="text-2xl">⏱️</span>
                                <div>
                                    <p class="text-xs text-blue-600 font-bold uppercase">Tiempo estimado de entrega</p>
                                    <p class="text-sm font-semibold text-blue-800">{{ cotizacion.tiempo_estimado }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botón reservar -->
                    <div class="mt-6 border-t pt-5">
                        <button @click="mostrarPedido = true"
                                class="w-full bg-green-500 text-white py-4 rounded-xl font-bold text-lg hover:bg-green-600 transition shadow-lg">
                            🛒 ¡Lo quiero! Reservar ahora
                        </button>
                        <p class="text-center text-xs text-gray-400 mt-2">
                            Solo se te pedirá tus datos y el comprobante del anticipo de {{ fmt(cotizacion.anticipo_gtq) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- CÓMO FUNCIONA -->
            <div v-if="!cotizacion" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow p-5 text-center">
                    <span class="text-4xl">🔗</span>
                    <h4 class="font-bold text-gray-700 mt-3 mb-1">1. Pegá el link</h4>
                    <p class="text-sm text-gray-500">Copiá el link del producto en Amazon y pegalo aquí</p>
                </div>
                <div class="bg-white rounded-2xl shadow p-5 text-center">
                    <span class="text-4xl">🧮</span>
                    <h4 class="font-bold text-gray-700 mt-3 mb-1">2. Calculamos todo</h4>
                    <p class="text-sm text-gray-500">Flete, impuestos, comisión y tipo de cambio en tiempo real</p>
                </div>
                <div class="bg-white rounded-2xl shadow p-5 text-center">
                    <span class="text-4xl">🚀</span>
                    <h4 class="font-bold text-gray-700 mt-3 mb-1">3. Reservá tu producto</h4>
                    <p class="text-sm text-gray-500">Pagá el anticipo y nosotros nos encargamos del resto</p>
                </div>
            </div>
        </div>

        <!-- MODAL PEDIDO -->
        <div v-if="mostrarPedido && cotizacion"
             class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">

                <div class="bg-gradient-to-r from-green-500 to-emerald-500 text-white p-5 rounded-t-2xl sticky top-0">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold">🛒 Reservar producto</h3>
                            <p class="text-green-100 text-sm">Anticipo: {{ fmt(cotizacion.anticipo_gtq) }}</p>
                        </div>
                        <button @click="mostrarPedido = false" class="text-white text-2xl hover:text-green-200">✕</button>
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

                    <!-- Comprobante anticipo -->
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

        <!-- FOOTER -->
        <footer class="bg-gray-800 text-gray-400 text-center py-6 text-xs mt-10">
            <p class="font-bold text-white mb-1">📦 JOC-YAPLA</p>
            <p>Importaciones desde Amazon a Guatemala — Todos los derechos reservados</p>
        </footer>
    </div>
</template>
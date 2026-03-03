<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({ cotizacion: Object })

// Form para actualizar estado del pedido existente
const nuevoEstado = ref(props.cotizacion.pedido?.estado || '')
const notas       = ref(props.cotizacion.pedido?.notas  || '')
const procesando  = ref(false)

// Form para crear pedido desde cotización
const formPedido = useForm({
    nombre:    '',
    apellido:  '',
    telefono:  '',
    email:     '',
    dpi:       '',
    direccion: '',
    notas:     '',
})

const mostrarFormPedido = ref(false)

function crearPedido() {
    formPedido.post(route('admin.cotizaciones.crear-pedido', props.cotizacion.id))
}

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
                <span v-if="cotizacion.pedido"
                      class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-bold">
                    ✅ Con pedido
                </span>
                <span v-else
                      class="bg-gray-100 text-gray-500 text-xs px-2 py-1 rounded-full">
                    Sin pedido
                </span>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Columna izquierda: producto -->
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
                       class="block text-center bg-orange-100 text-orange-600 py-2 rounded-lg text-sm hover:bg-orange-200 mb-4">
                        Ver en Amazon →
                    </a>

                    <div class="text-xs space-y-2 text-gray-500 border-t pt-3">
                        <div class="flex justify-between">
                            <span>Fecha:</span>
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
                        <div class="flex justify-between" v-if="cotizacion.pedido?.codigo">
                            <span>Código pedido:</span>
                            <span class="font-mono font-bold text-orange-600">
                                {{ cotizacion.pedido.codigo }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: desglose + pedido -->
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
                        <div class="flex justify-between py-3 font-black text-lg">
                            <span>TOTAL GTQ</span>
                            <span class="text-orange-600">{{ fmt(cotizacion.total_gtq) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-t">
                            <span class="text-gray-600 text-sm">Anticipo requerido</span>
                            <span class="font-mono font-bold text-green-600">
                                {{ fmt(cotizacion.anticipo_gtq) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- ===== SI YA TIENE PEDIDO ===== -->
                <div v-if="cotizacion.pedido" class="bg-white rounded-xl shadow p-6">
                    <h3 class="font-bold text-gray-700 mb-4">👤 Datos del Cliente</h3>

                    <div class="grid grid-cols-2 gap-3 text-sm mb-5">
                        <div>
                            <p class="text-xs text-gray-400">Nombre completo</p>
                            <p class="font-semibold">
                                {{ cotizacion.pedido.nombre }} {{ cotizacion.pedido.apellido }}
                            </p>
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
                            <p class="text-xs text-gray-400">Notas</p>
                            <p class="font-semibold">{{ cotizacion.pedido.notas }}</p>
                        </div>
                    </div>

                    <!-- Comprobante -->
                    <div class="mb-5">
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
                        <div class="grid grid-cols-1 gap-3">
                            <select v-model="nuevoEstado"
                                    class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400">
                                <option value="pendiente">⏳ Pendiente</option>
                                <option value="confirmado">✅ Confirmado</option>
                                <option value="en_camino">🚚 En camino</option>
                                <option value="entregado">🎉 Entregado</option>
                                <option value="cancelado">❌ Cancelado</option>
                            </select>
                            <textarea v-model="notas" rows="2"
                                      class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400"
                                      placeholder="Descripción del cambio (aparece en el tracking del cliente)..."></textarea>
                            <button @click="actualizarEstado" :disabled="procesando"
                                    class="bg-orange-500 text-white px-5 py-2.5 rounded-lg text-sm font-bold hover:bg-orange-600 disabled:opacity-50">
                                {{ procesando ? '⏳ Guardando...' : '✓ Actualizar estado' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ===== SI NO TIENE PEDIDO ===== -->
                <div v-else class="bg-white rounded-xl shadow overflow-hidden">

                    <!-- Header con botón para mostrar form -->
                    <div class="p-5 flex items-center justify-between border-b">
                        <div>
                            <h3 class="font-bold text-gray-700">📋 Sin pedido aún</h3>
                            <p class="text-sm text-gray-400 mt-0.5">
                                El cliente cotizó pero no completó su pedido
                            </p>
                        </div>
                        <button @click="mostrarFormPedido = !mostrarFormPedido"
                                class="bg-green-500 text-white px-5 py-2.5 rounded-lg text-sm font-bold hover:bg-green-600 transition">
                            {{ mostrarFormPedido ? '✕ Cancelar' : '🛒 Convertir a Pedido' }}
                        </button>
                    </div>

                    <!-- Formulario para crear pedido -->
                    <div v-if="mostrarFormPedido" class="p-6">

                        <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-5 text-sm text-orange-700">
                            📞 Llenás estos datos basándote en la información que te dio el cliente
                            por WhatsApp, teléfono o email.
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Nombre *</label>
                                <input v-model="formPedido.nombre" type="text"
                                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-400" />
                                <p v-if="formPedido.errors.nombre"
                                   class="text-red-500 text-xs mt-1">{{ formPedido.errors.nombre }}</p>
                            </div>

                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Apellido *</label>
                                <input v-model="formPedido.apellido" type="text"
                                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-400" />
                                <p v-if="formPedido.errors.apellido"
                                   class="text-red-500 text-xs mt-1">{{ formPedido.errors.apellido }}</p>
                            </div>

                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Teléfono *</label>
                                <input v-model="formPedido.telefono" type="text"
                                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-400" />
                                <p v-if="formPedido.errors.telefono"
                                   class="text-red-500 text-xs mt-1">{{ formPedido.errors.telefono }}</p>
                            </div>

                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Email</label>
                                <input v-model="formPedido.email" type="email"
                                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-400" />
                            </div>

                            <div>
                                <label class="block text-sm text-gray-600 mb-1">DPI</label>
                                <input v-model="formPedido.dpi" type="text"
                                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-400" />
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm text-gray-600 mb-1">Dirección de entrega *</label>
                                <textarea v-model="formPedido.direccion" rows="2"
                                          class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-400"
                                          placeholder="Zona, municipio, departamento..."></textarea>
                                <p v-if="formPedido.errors.direccion"
                                   class="text-red-500 text-xs mt-1">{{ formPedido.errors.direccion }}</p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm text-gray-600 mb-1">Notas internas</label>
                                <textarea v-model="formPedido.notas" rows="2"
                                          class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-400"
                                          placeholder="Observaciones, variante del producto, etc..."></textarea>
                            </div>
                        </div>

                        <!-- Resumen antes de confirmar -->
                        <div class="mt-5 bg-gray-50 rounded-xl p-4 text-sm">
                            <p class="font-bold text-gray-700 mb-2">Resumen del pedido a crear:</p>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Producto:</span>
                                <span class="font-medium">
                                    {{ cotizacion.nombre_producto || 'Sin nombre' }}
                                </span>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-gray-500">Total:</span>
                                <span class="font-bold text-orange-600">
                                    {{ fmt(cotizacion.total_gtq) }}
                                </span>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-gray-500">Anticipo:</span>
                                <span class="font-bold text-green-600">
                                    {{ fmt(cotizacion.anticipo_gtq) }}
                                </span>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-gray-500">Estado inicial:</span>
                                <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-bold">
                                    ✅ Confirmado
                                </span>
                            </div>
                        </div>

                        <button @click="crearPedido"
                                :disabled="formPedido.processing"
                                class="w-full mt-5 bg-green-500 text-white py-3.5 rounded-xl font-black text-base hover:bg-green-600 disabled:opacity-50 shadow-lg transition">
                            {{ formPedido.processing
                                ? '⏳ Creando pedido...'
                                : '✅ Confirmar y crear pedido' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({ pedidos: Object })

const modalPedido            = ref(null)
const nuevoEstado            = ref('')
const notasEstado            = ref('')
const procesando             = ref(false)
const montoComplemento       = ref('')
const referenciaComplemento  = ref('')
const comprobanteComplemento = ref(null)
const errorComplemento       = ref('')

function abrirModal(p) {
    modalPedido.value            = p
    nuevoEstado.value            = p.estado
    notasEstado.value            = p.notas || ''
    montoComplemento.value       = ''
    referenciaComplemento.value  = p.referencia_complemento || ''
    comprobanteComplemento.value = null
    errorComplemento.value       = ''
}

function actualizarEstado() {
    errorComplemento.value = ''

    // Validacion frontend para estado entregado
    if (nuevoEstado.value === 'entregado') {
        if (!montoComplemento.value) {
            errorComplemento.value = 'El monto del complemento es obligatorio.'
            return
        }
        if (!referenciaComplemento.value) {
            errorComplemento.value = 'La referencia de transferencia es obligatoria.'
            return
        }
        if (!comprobanteComplemento.value && !modalPedido.value.comprobante_complemento) {
            errorComplemento.value = 'El comprobante (imagen) es obligatorio.'
            return
        }
    }

    procesando.value = true

    const formData = new FormData()
    formData.append('_method', 'PATCH')
    formData.append('estado', nuevoEstado.value)
    formData.append('notas', notasEstado.value ?? '')

    if (montoComplemento.value)
        formData.append('monto_complemento', montoComplemento.value)
    if (referenciaComplemento.value)
        formData.append('referencia_complemento', referenciaComplemento.value)
    if (comprobanteComplemento.value)
        formData.append('comprobante_complemento', comprobanteComplemento.value)

    router.post(
        route('admin.pedidos.estado', modalPedido.value.id),
        formData,
        {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                modalPedido.value = null
                procesando.value  = false
            },
            onError: () => { procesando.value = false }
        }
    )
}

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
</script>

<template>
    <Head title="Pedidos" />
    <AdminLayout>
        <template #header>
            <h1 class="text-xl font-bold text-gray-800">📦 Pedidos</h1>
        </template>

        <div class="bg-white rounded-xl shadow overflow-hidden overflow-x-auto">
            <table class="w-full text-sm min-w-[900px]">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3">Cliente</th>
                        <th class="text-left px-4 py-3">Producto</th>
                        <th class="text-right px-4 py-3">Total</th>
                        <th class="text-right px-4 py-3">Anticipo</th>
                        <th class="text-center px-4 py-3">Comprobante</th>
                        <th class="text-center px-4 py-3">Estado</th>
                        <th class="text-center px-4 py-3">Fecha</th>
                        <th class="text-center px-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="p in pedidos.data" :key="p.id"
                        class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <p class="font-medium">{{ p.nombre }} {{ p.apellido }}</p>
                            <p class="text-xs text-gray-400">{{ p.telefono }}</p>
                            <p class="text-xs text-gray-400 truncate max-w-xs">{{ p.direccion }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-xs text-gray-600 max-w-xs truncate">
                                {{ p.cotizacion?.nombre_producto || 'Sin nombre' }}
                            </p>
                            <a v-if="p.cotizacion?.link_amazon"
                               :href="p.cotizacion.link_amazon" target="_blank"
                               class="text-xs text-orange-500 hover:underline">Ver en Amazon →</a>
                        </td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-orange-600">
                            {{ fmt(p.cotizacion?.total_gtq) }}
                        </td>
                        <td class="px-4 py-3 text-right font-mono text-green-600 font-bold">
                            {{ fmt(p.monto_anticipo) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a v-if="p.comprobante_anticipo"
                               :href="`/storage/${p.comprobante_anticipo}`"
                               target="_blank"
                               class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-xs hover:bg-blue-200">
                                Ver
                            </a>
                            <span v-else class="text-gray-400 text-xs">—</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span :class="estadoClass[p.estado]"
                                  class="px-2 py-1 rounded-full text-xs font-semibold capitalize">
                                {{ p.estado.replace('_', ' ') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-gray-500">
                            {{ p.created_at }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button @click="abrirModal(p)"
                                    class="bg-orange-500 text-white px-3 py-1 rounded-lg text-xs hover:bg-orange-600">
                                Actualizar
                            </button>
                        </td>
                    </tr>
                    <tr v-if="!pedidos.data?.length">
                        <td colspan="8" class="text-center py-10 text-gray-400">
                            No hay pedidos registrados
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="pedidos.last_page > 1"
                 class="px-4 py-3 border-t flex gap-2 justify-end text-sm flex-wrap">
                <a v-for="p in pedidos.links" :key="p.label"
                   :href="p.url" v-html="p.label"
                   class="px-3 py-1 rounded border"
                   :class="p.active ? 'bg-orange-500 text-white' : 'text-gray-600 hover:bg-gray-50'" />
            </div>
        </div>

        <!-- Modal actualizar estado -->
        <div v-if="modalPedido"
             class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">

                <div class="bg-orange-500 text-white p-5 rounded-t-xl sticky top-0 z-10">
                    <h3 class="font-bold text-lg">📦 Actualizar Pedido</h3>
                    <p class="text-orange-100 text-sm mt-1">
                        {{ modalPedido.nombre }} {{ modalPedido.apellido }}
                    </p>
                </div>

                <div class="p-5 space-y-4">

                    <!-- Estado -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Nuevo estado *</label>
                        <select v-model="nuevoEstado"
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400">
                            <option value="pendiente">⏳ Pendiente</option>
                            <option value="confirmado">✅ Confirmado</option>
                            <option value="en_camino">🚚 En camino</option>
                            <option value="entregado">🎉 Entregado</option>
                            <option value="cancelado">❌ Cancelado</option>
                        </select>
                    </div>

                    <!-- Notas -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Notas internas</label>
                        <textarea v-model="notasEstado" rows="3"
                                  class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400"
                                  placeholder="Numero de tracking, observaciones..."></textarea>
                    </div>

                    <!-- COMPLEMENTO DE PAGO — solo si estado = entregado -->
                    <div v-if="nuevoEstado === 'entregado'"
                         class="p-4 bg-amber-50 border border-amber-300 rounded-xl space-y-3">

                        <p class="text-sm font-bold text-amber-800">
                            💰 Complemento de pago — requerido para marcar como entregado
                        </p>

                        <!-- Monto -->
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Monto complemento (Q) *</label>
                            <input v-model="montoComplemento" type="number" step="0.01" min="0"
                                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400"
                                   placeholder="Ej: 350.00" />
                        </div>

                        <!-- Referencia -->
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Referencia / No. de transferencia *</label>
                            <input v-model="referenciaComplemento" type="text"
                                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400"
                                   placeholder="Ej: TRF-20260306-001234" />
                        </div>

                        <!-- Imagen comprobante -->
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">
                                Imagen del comprobante *
                                <span v-if="modalPedido.comprobante_complemento"
                                      class="text-green-600 ml-1">(ya tiene uno, podes reemplazarlo)</span>
                            </label>
                            <input type="file" accept="image/*,.pdf"
                                   @change="e => comprobanteComplemento = e.target.files[0]"
                                   class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200" />
                        </div>

                        <!-- Error -->
                        <p v-if="errorComplemento" class="text-red-500 text-xs font-medium">
                            ⚠️ {{ errorComplemento }}
                        </p>
                    </div>

                </div>

                <div class="p-5 border-t flex gap-3 sticky bottom-0 bg-white">
                    <button @click="modalPedido = null"
                            class="flex-1 border border-gray-300 text-gray-600 py-2 rounded-lg text-sm hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button @click="actualizarEstado" :disabled="procesando"
                            class="flex-1 bg-orange-500 text-white py-2 rounded-lg text-sm font-bold hover:bg-orange-600 disabled:opacity-50">
                        {{ procesando ? 'Guardando...' : 'Actualizar' }}
                    </button>
                </div>
            </div>
        </div>

    </AdminLayout>
</template>
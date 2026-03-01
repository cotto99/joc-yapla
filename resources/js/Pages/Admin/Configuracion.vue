<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps({ configs: Object })

const form = useForm({
    tipo_cambio:       props.configs?.tipo_cambio?.valor       || '7.80',
    ganancia_pct:      props.configs?.ganancia_pct?.valor      || '15',
    flete_usd:         props.configs?.flete_usd?.valor         || '10',
    arancel_pct:       props.configs?.arancel_pct?.valor       || '12',
    comision_pct:      props.configs?.comision_pct?.valor      || '5',
    entrega_local_gtq: props.configs?.entrega_local_gtq?.valor || '50',
    anticipo_pct:      props.configs?.anticipo_pct?.valor      || '50',
    tiempo_estimado:   props.configs?.tiempo_estimado?.valor   || '15 a 21 días hábiles',
    whatsapp:          props.configs?.whatsapp?.valor          || '',
})

function guardar() {
    form.post(route('admin.configuracion.update'))
}
</script>

<template>
    <Head title="Configuración" />
    <AdminLayout>
        <template #header>
            <h1 class="text-xl font-bold text-gray-800">⚙️ Configuración</h1>
        </template>

        <div class="max-w-2xl">

            <!-- Tipo de cambio -->
            <div class="bg-white rounded-xl shadow p-6 mb-4">
                <h2 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                    💱 Tipo de Cambio
                </h2>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Tipo de cambio USD → GTQ (fallback si Banguat no responde)
                    </label>
                    <div class="relative w-48">
                        <span class="absolute left-3 top-2.5 text-gray-400 text-sm">Q</span>
                        <input v-model="form.tipo_cambio" type="number" step="0.01"
                               class="w-full border rounded-lg pl-7 pr-3 py-2 text-sm focus:ring-2 focus:ring-orange-400" />
                    </div>
                    <p class="text-xs text-gray-400 mt-1">
                        El sistema intenta obtener el tipo de cambio real del Banguat automáticamente
                    </p>
                </div>
            </div>

            <!-- Costos -->
            <div class="bg-white rounded-xl shadow p-6 mb-4">
                <h2 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                    💰 Costos y Comisiones
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">
                            % Ganancia sobre precio
                        </label>
                        <div class="relative">
                            <input v-model="form.ganancia_pct" type="number" step="0.1" min="0"
                                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 pr-8" />
                            <span class="absolute right-3 top-2.5 text-gray-400 text-sm">%</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">
                            Flete internacional (USD)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400 text-sm">$</span>
                            <input v-model="form.flete_usd" type="number" step="0.01" min="0"
                                   class="w-full border rounded-lg pl-7 pr-3 py-2 text-sm focus:ring-2 focus:ring-orange-400" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">
                            % Arancel de importación
                        </label>
                        <div class="relative">
                            <input v-model="form.arancel_pct" type="number" step="0.1" min="0"
                                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 pr-8" />
                            <span class="absolute right-3 top-2.5 text-gray-400 text-sm">%</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">
                            % Comisión del agente
                        </label>
                        <div class="relative">
                            <input v-model="form.comision_pct" type="number" step="0.1" min="0"
                                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 pr-8" />
                            <span class="absolute right-3 top-2.5 text-gray-400 text-sm">%</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">
                            Entrega local Guatemala (GTQ)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400 text-sm">Q</span>
                            <input v-model="form.entrega_local_gtq" type="number" step="0.01" min="0"
                                   class="w-full border rounded-lg pl-7 pr-3 py-2 text-sm focus:ring-2 focus:ring-orange-400" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">
                            % Anticipo requerido
                        </label>
                        <div class="relative">
                            <input v-model="form.anticipo_pct" type="number" step="1" min="1" max="100"
                                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 pr-8" />
                            <span class="absolute right-3 top-2.5 text-gray-400 text-sm">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Otros -->
            <div class="bg-white rounded-xl shadow p-6 mb-6">
                <h2 class="font-bold text-gray-700 mb-4">📋 Información General</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Tiempo estimado de entrega</label>
                        <input v-model="form.tiempo_estimado" type="text"
                               placeholder="Ej: 15 a 21 días hábiles"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400" />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">WhatsApp de contacto</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400 text-sm">+</span>
                            <input v-model="form.whatsapp" type="text"
                                   placeholder="50212345678"
                                   class="w-full border rounded-lg pl-7 pr-3 py-2 text-sm focus:ring-2 focus:ring-orange-400" />
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Sin espacios ni guiones, incluí el código de país</p>
                    </div>
                </div>
            </div>

            <!-- Preview del cálculo -->
            <div class="bg-orange-50 border border-orange-200 rounded-xl p-5 mb-6">
                <h3 class="font-bold text-orange-800 mb-3">🧮 Vista previa del cálculo (para $100 USD)</h3>
                <div class="text-sm space-y-1 text-orange-700">
                    <div class="flex justify-between">
                        <span>Precio base:</span>
                        <span>Q{{ (100 * form.tipo_cambio).toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Ganancia ({{ form.ganancia_pct }}%):</span>
                        <span>Q{{ (100 * form.tipo_cambio * form.ganancia_pct / 100).toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Flete (${{ form.flete_usd }}):</span>
                        <span>Q{{ (form.flete_usd * form.tipo_cambio).toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Arancel ({{ form.arancel_pct }}%):</span>
                        <span>Q{{ (100 * form.tipo_cambio * form.arancel_pct / 100).toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Comisión ({{ form.comision_pct }}%):</span>
                        <span>Q{{ (100 * form.tipo_cambio * form.comision_pct / 100).toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Entrega local:</span>
                        <span>Q{{ Number(form.entrega_local_gtq).toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between font-black text-orange-800 border-t border-orange-300 pt-2 mt-2 text-base">
                        <span>TOTAL:</span>
                        <span>Q{{
                            (
                                100 * form.tipo_cambio +
                                100 * form.tipo_cambio * form.ganancia_pct / 100 +
                                form.flete_usd * form.tipo_cambio +
                                100 * form.tipo_cambio * form.arancel_pct / 100 +
                                100 * form.tipo_cambio * form.comision_pct / 100 +
                                Number(form.entrega_local_gtq)
                            ).toFixed(2)
                        }}</span>
                    </div>
                </div>
            </div>

            <button @click="guardar" :disabled="form.processing"
                    class="w-full bg-orange-500 text-white py-3 rounded-xl font-bold text-base hover:bg-orange-600 disabled:opacity-50 shadow-lg">
                {{ form.processing ? '⏳ Guardando...' : '💾 Guardar Configuración' }}
            </button>

            <p v-if="form.recentlySuccessful"
               class="text-center text-green-600 font-medium mt-3">
                ✅ Configuración guardada exitosamente
            </p>
        </div>
    </AdminLayout>
</template>
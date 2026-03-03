<script setup>
import { Head } from '@inertiajs/vue3'

const props = defineProps({ pedido: Object })
</script>

<template>
    <Head title="¡Pedido recibido! — JOC-YAPLA" />
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-100 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl p-10 max-w-md w-full text-center">
            <div class="text-7xl mb-4">🎉</div>
            <h1 class="text-3xl font-black text-green-600 mb-2">¡Pedido recibido!</h1>
            <p class="text-gray-600 mb-6">
                Recibimos tu solicitud y comprobante de anticipo.
                Nos comunicaremos contigo a la brevedad para confirmar.
            </p>

            <!-- Código de pedido -->
            <div class="bg-orange-50 border-2 border-orange-300 rounded-xl p-5 mb-6">
                <p class="text-sm text-orange-600 font-bold uppercase tracking-wide mb-1">
                    Tu número de pedido
                </p>
                <p class="text-3xl font-black text-orange-700 font-mono">
                    {{ pedido.codigo }}
                </p>
                <p class="text-xs text-orange-500 mt-2">
                    Guardá este código para rastrear tu pedido
                </p>
            </div>

            <div class="bg-green-50 rounded-xl p-4 mb-6 text-sm text-green-700 text-left">
                <p class="font-bold mb-2">¿Qué sigue?</p>
                <div class="space-y-2">
                    <p>✅ Verificamos tu comprobante de pago</p>
                    <p>📞 Te confirmamos por WhatsApp</p>
                    <p>🛒 Compramos tu producto en Amazon</p>
                    <p>🚀 Te lo enviamos a Guatemala 🇬🇹</p>
                </div>
            </div>

            <div class="space-y-3">
                <a :href="`/tracking?codigo=${pedido.codigo}`"
                   class="block w-full bg-indigo-600 text-white py-3 rounded-xl font-bold hover:bg-indigo-700 transition">
                    📦 Rastrear mi pedido
                </a>
                <a href="/"
                   class="block w-full bg-orange-500 text-white py-3 rounded-xl font-bold hover:bg-orange-600 transition">
                    Hacer otra cotización
                </a>
            </div>
        </div>
    </div>
</template>
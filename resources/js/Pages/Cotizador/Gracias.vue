<script setup>
import { Head } from '@inertiajs/vue3'

const props = defineProps({ pedido: Object })
</script>

<template>
    <Head title="Pedido recibido! — JOC-YAPLA" />

    <div class="min-h-screen bg-gray-950 flex flex-col">

        <!-- HEADER -->
        <header class="bg-black border-b border-yellow-500/30 shadow-lg shadow-yellow-500/10">
            <div class="max-w-3xl mx-auto px-4 py-4 flex items-center justify-between">
                <img src="/images/logo-safari.png" alt="Safari" class="h-14 w-auto" />
                <a href="/" class="border border-yellow-500 text-yellow-400 hover:bg-yellow-500 hover:text-black px-4 py-2 rounded-lg text-sm font-bold transition">
                    Volver al inicio
                </a>
            </div>
        </header>

        <!-- Contenido centrado -->
        <div class="flex-1 flex items-center justify-center p-4">
            <div class="bg-gray-900 border border-yellow-500/20 rounded-2xl shadow-2xl shadow-yellow-500/5 p-10 max-w-md w-full text-center">

                <div class="text-7xl mb-4">🎉</div>
                <h1 class="text-3xl font-black text-yellow-400 mb-2">Pedido recibido!</h1>
                <p class="text-gray-400 mb-6">
                    Recibimos tu solicitud y comprobante de anticipo.
                    Nos comunicaremos contigo a la brevedad para confirmar.
                </p>

                <!-- Código de pedido -->
                <div class="bg-yellow-500/10 border-2 border-yellow-500/40 rounded-xl p-5 mb-6">
                    <p class="text-sm text-yellow-400 font-bold uppercase tracking-wide mb-1">
                        Tu numero de pedido
                    </p>
                    <p class="text-3xl font-black text-white font-mono">
                        {{ pedido.codigo }}
                    </p>
                    <p class="text-xs text-yellow-400/70 mt-2">
                        Guarda este codigo para rastrear tu pedido
                    </p>
                </div>

                <!-- Que sigue -->
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 mb-6 text-sm text-gray-300 text-left">
                    <p class="font-bold text-white mb-2">Que sigue?</p>
                    <div class="space-y-2">
                        <p>✅ Verificamos tu comprobante de pago</p>
                        <p>📞 Te confirmamos por WhatsApp</p>
                        <p>🛒 Compramos tu producto en Amazon</p>
                        <p>🚀 Te lo enviamos a Guatemala 🇬🇹</p>
                    </div>
                </div>

                <!-- Botones -->
                <div class="space-y-3">
                    <a :href="`/tracking?codigo=${pedido.codigo}`"
                       class="block w-full bg-yellow-500 hover:bg-yellow-400 text-black py-3 rounded-xl font-black transition shadow-lg shadow-yellow-500/30">
                        ⚡ Rastrear mi pedido
                    </a>
                    <a href="/"
                       class="block w-full border border-gray-600 text-gray-400 py-3 rounded-xl font-bold hover:bg-gray-800 transition">
                        Hacer otra cotizacion
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-black border-t border-yellow-500/20 text-gray-500 text-center py-6 text-xs">
            <img src="/images/logo-safari.png" alt="Safari" class="h-10 w-auto mx-auto mb-2" />
            <p>Importaciones desde Amazon a Guatemala</p>
        </footer>
    </div>
</template>
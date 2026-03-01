<script setup>
import { Head } from '@inertiajs/vue3'
</script>

<template>
    <Head title="¡Pedido recibido! — JOC-YAPLA" />
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-100 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl p-10 max-w-md w-full text-center">
            <div class="text-7xl mb-4">🎉</div>
            <h1 class="text-3xl font-black text-green-600 mb-2">¡Pedido recibido!</h1>
            <p class="text-gray-600 mb-6">
                Recibimos tu solicitud y comprobante de anticipo.
                Nos comunicaremos contigo a la brevedad para confirmar tu pedido.
            </p>
            <div class="bg-green-50 rounded-xl p-4 mb-6 text-sm text-green-700">
                <p class="font-bold mb-1">¿Qué sigue?</p>
                <p>1. Verificamos tu comprobante de pago</p>
                <p>2. Confirmamos tu pedido por WhatsApp</p>
                <p>3. Compramos tu producto en Amazon</p>
                <p>4. Te lo entregamos en Guatemala 🇬🇹</p>
            </div>
            <a href="/" class="block w-full bg-orange-500 text-white py-3 rounded-xl font-bold hover:bg-orange-600 transition">
                Hacer otra cotización
            </a>
        </div>
    </div>
</template>
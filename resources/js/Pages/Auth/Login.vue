<script setup>
import { Head, useForm } from '@inertiajs/vue3'

defineProps({ status: String })

const form = useForm({
    email:    '',
    password: '',
    remember: false,
})

function submit() {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <Head title="Safari — Iniciar Sesión" />

    <div class="min-h-screen bg-gray-950 flex items-center justify-center p-4">
        <div class="w-full max-w-sm">

            <!-- Logo -->
            <div class="text-center mb-8">
                <img src="/images/logo-safari.png" alt="Safari" class="h-28 w-auto mx-auto mb-2" />
            </div>

            <!-- Card -->
            <div class="bg-black border border-yellow-500/20 rounded-2xl p-8 shadow-2xl shadow-yellow-500/5">

                <h2 class="text-white font-black text-xl mb-1">Iniciar sesión</h2>
                <p class="text-gray-500 text-sm mb-6">Panel administrativo</p>

                <!-- Session Status -->
                <div v-if="status" class="mb-4 text-sm text-yellow-400">{{ status }}</div>

                <div class="space-y-4">
                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1.5 uppercase tracking-wide">
                            Email
                        </label>
                        <input v-model="form.email" type="email" required autofocus
                               class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 transition placeholder-gray-600"
                               placeholder="admin@ejemplo.com" />
                        <p v-if="form.errors.email" class="text-red-400 text-xs mt-1">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1.5 uppercase tracking-wide">
                            Contraseña
                        </label>
                        <input v-model="form.password" type="password" required
                               class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 transition placeholder-gray-600"
                               placeholder="••••••••" />
                        <p v-if="form.errors.password" class="text-red-400 text-xs mt-1">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Remember -->
                    <div class="flex items-center gap-2">
                        <input v-model="form.remember" type="checkbox" id="remember"
                               class="accent-yellow-400 w-4 h-4" />
                        <label for="remember" class="text-sm text-gray-400">Recordarme</label>
                    </div>

                    <!-- Submit -->
                    <button @click="submit" :disabled="form.processing"
                            class="w-full bg-yellow-400 hover:bg-yellow-300 text-black font-black py-3.5 rounded-xl transition disabled:opacity-50 text-sm tracking-wide shadow-lg shadow-yellow-400/20">
                        {{ form.processing ? 'Ingresando...' : 'Ingresar →' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
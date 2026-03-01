<script setup>
import { ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const sidebarOpen = ref(false)
const page = usePage()

const nav = [
    { name: 'Dashboard',     href: route('admin.dashboard'),      icon: '📊' },
    { name: 'Cotizaciones',  href: route('admin.cotizaciones'),   icon: '🔍' },
    { name: 'Pedidos',       href: route('admin.pedidos'),        icon: '📦' },
    { name: 'Configuración', href: route('admin.configuracion'),  icon: '⚙️' },
]

function isActive(href) {
    try {
        return page.url.startsWith(new URL(href).pathname)
    } catch {
        return false
    }
}
</script>

<template>
    <div class="min-h-screen bg-gray-100">

        <!-- TOPBAR MÓVIL -->
        <header class="lg:hidden bg-orange-500 text-white px-4 py-3 flex items-center justify-between sticky top-0 z-40 shadow-lg">
            <span class="text-lg font-bold">📦 JOC-YAPLA Admin</span>
            <div class="flex items-center gap-3">
                <Link :href="route('logout')" method="post" as="button"
                      class="text-xs text-orange-100 hover:text-white">Salir</Link>
                <button @click="sidebarOpen = !sidebarOpen" class="text-white text-2xl">
                    {{ sidebarOpen ? '✕' : '☰' }}
                </button>
            </div>
        </header>

        <!-- DRAWER MÓVIL -->
        <transition name="fade">
            <div v-if="sidebarOpen" class="lg:hidden fixed inset-0 z-30 flex">
                <div class="absolute inset-0 bg-black/50" @click="sidebarOpen = false"></div>
                <nav class="relative bg-orange-600 text-white w-72 h-full z-40 pt-4">
                    <div class="px-4 pb-4 border-b border-orange-500 mb-2">
                        <p class="text-xs text-orange-200">Admin:</p>
                        <p class="font-semibold">{{ page.props.auth.user.name }}</p>
                    </div>
                    <Link v-for="item in nav" :key="item.name"
                          :href="item.href"
                          @click="sidebarOpen = false"
                          class="flex items-center gap-4 px-5 py-4 hover:bg-orange-700 transition-colors"
                          :class="{ 'bg-orange-700 border-l-4 border-white': isActive(item.href) }">
                        <span class="text-2xl">{{ item.icon }}</span>
                        <span class="font-medium">{{ item.name }}</span>
                    </Link>
                </nav>
            </div>
        </transition>

        <!-- LAYOUT DESKTOP -->
        <div class="hidden lg:flex min-h-screen">
            <aside class="w-64 bg-orange-600 text-white flex flex-col min-h-screen sticky top-0">
                <div class="p-5 border-b border-orange-500">
                    <h1 class="text-lg font-black">📦 JOC-YAPLA</h1>
                    <p class="text-xs text-orange-200 mt-0.5">Panel Administrativo</p>
                </div>
                <nav class="flex-1 py-4">
                    <Link v-for="item in nav" :key="item.name"
                          :href="item.href"
                          class="flex items-center gap-3 px-4 py-3 hover:bg-orange-700 transition-colors"
                          :class="{ 'bg-orange-700 border-l-4 border-white': isActive(item.href) }">
                        <span class="text-xl">{{ item.icon }}</span>
                        <span class="text-sm font-medium">{{ item.name }}</span>
                    </Link>
                </nav>
                <div class="p-4 border-t border-orange-500">
                    <p class="text-xs text-orange-200">{{ page.props.auth.user.name }}</p>
                    <Link :href="route('logout')" method="post" as="button"
                          class="text-xs text-orange-300 hover:text-white mt-1">
                        Cerrar sesión →
                    </Link>
                </div>
            </aside>

            <div class="flex-1 flex flex-col">
                <header class="bg-white shadow-sm px-6 py-4 sticky top-0 z-10">
                    <slot name="header" />
                </header>
                <main class="flex-1 p-6"><slot /></main>
            </div>
        </div>

        <!-- CONTENIDO MÓVIL -->
        <main class="lg:hidden p-4 pb-6"><slot /></main>
    </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
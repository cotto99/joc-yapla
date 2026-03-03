<script setup>
import { ref, onMounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const sidebarOpen = ref(false)
const page = usePage()

/* ================= DARK MODE ================= */
const darkMode = ref(true)

onMounted(() => {
    const saved = localStorage.getItem('darkMode')
    darkMode.value = saved ? JSON.parse(saved) : true
    updateTheme()
})

function toggleDark() {
    darkMode.value = !darkMode.value
    localStorage.setItem('darkMode', JSON.stringify(darkMode.value))
    updateTheme()
}

function updateTheme() {
    if (darkMode.value) {
        document.documentElement.classList.add('dark')
    } else {
        document.documentElement.classList.remove('dark')
    }
}
/* ============================================ */

const nav = [
    { name: 'Dashboard',    href: route('admin.dashboard'),     icon: '📊' },
    { name: 'Cotizaciones', href: route('admin.cotizaciones'),  icon: '🔍' },
    { name: 'Pedidos',      href: route('admin.pedidos'),       icon: '📦' },
    { name: 'Configuración',href: route('admin.configuracion'), icon: '⚙️' },
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
<div class="min-h-screen bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors duration-300">

    <!-- TOPBAR MÓVIL -->
    <header class="lg:hidden bg-white dark:bg-black border-b border-gray-300 dark:border-yellow-500/30 px-4 py-3 flex items-center justify-between sticky top-0 z-40 shadow-lg">
        <div class="flex items-center gap-3">
            <img src="/images/logo-safari.png" alt="Safari" class="h-9 w-auto" />
        </div>

        <div class="flex items-center gap-4">

            <!-- BOTÓN DARK MODE -->
            <button @click="toggleDark" class="text-lg">
                {{ darkMode ? '☀️' : '🌙' }}
            </button>

            <Link :href="route('logout')" method="post" as="button"
                  class="text-xs text-gray-600 dark:text-gray-400 hover:text-yellow-400 transition">
                Salir
            </Link>

            <button @click="sidebarOpen = !sidebarOpen"
                    class="text-2xl leading-none text-gray-700 dark:text-yellow-400">
                {{ sidebarOpen ? '✕' : '☰' }}
            </button>
        </div>
    </header>

    <!-- DRAWER MÓVIL -->
    <transition name="fade">
        <div v-if="sidebarOpen" class="lg:hidden fixed inset-0 z-30 flex">
            <div class="absolute inset-0 bg-black/70" @click="sidebarOpen = false"></div>

            <nav class="relative bg-white dark:bg-black border-r border-gray-300 dark:border-yellow-500/20 w-72 h-full z-40 flex flex-col transition-colors">

                <div class="px-5 py-5 border-b border-gray-200 dark:border-yellow-500/20 flex items-center gap-3">
                    <img src="/images/logo-safari.png" alt="Safari" class="h-12 w-auto" />
                </div>

                <div class="flex-1 py-3">
                    <Link v-for="item in nav" :key="item.name"
                          :href="item.href"
                          @click="sidebarOpen = false"
                          class="flex items-center gap-4 px-5 py-3.5 transition-all border-l-4"
                          :class="isActive(item.href)
                              ? 'bg-yellow-400/10 border-yellow-400 text-yellow-500'
                              : 'border-transparent text-gray-600 dark:text-gray-300 hover:text-yellow-500 hover:bg-yellow-400/5'">
                        <span class="text-xl">{{ item.icon }}</span>
                        <span class="font-semibold text-sm">{{ item.name }}</span>
                    </Link>
                </div>

                <div class="p-4 border-t border-gray-200 dark:border-yellow-500/20">
                    <p class="text-xs text-gray-500">Admin:</p>
                    <p class="text-sm font-semibold">{{ page.props.auth.user.name }}</p>
                </div>
            </nav>
        </div>
    </transition>

    <!-- LAYOUT DESKTOP -->
    <div class="hidden lg:flex min-h-screen">

        <!-- SIDEBAR -->
        <aside class="w-64 bg-white dark:bg-black border-r border-gray-200 dark:border-yellow-500/20 flex flex-col min-h-screen sticky top-0 transition-colors">

            <div class="px-5 py-6 border-b border-gray-200 dark:border-yellow-500/20 flex items-center justify-center">
                <img src="/images/logo-safari.png" alt="Safari" class="h-20 w-auto" />
            </div>

            <nav class="flex-1 py-4">
                <Link v-for="item in nav" :key="item.name"
                      :href="item.href"
                      class="flex items-center gap-3 px-5 py-3.5 mx-2 rounded-xl mb-1 transition-all"
                      :class="isActive(item.href)
                          ? 'bg-yellow-400/15 text-yellow-500'
                          : 'text-gray-600 dark:text-gray-400 hover:text-yellow-500 hover:bg-yellow-400/5'">
                    <span class="text-xl">{{ item.icon }}</span>
                    <span class="text-sm font-semibold">{{ item.name }}</span>

                    <span v-if="isActive(item.href)"
                          class="ml-auto w-1.5 h-1.5 rounded-full bg-yellow-400"></span>
                </Link>
            </nav>

            <!-- BOTÓN DARK MODE -->
            <div class="px-4">
                <button @click="toggleDark"
                        class="w-full mb-3 text-xs py-2 rounded-lg transition
                               bg-gray-200 text-black
                               dark:bg-gray-800 dark:text-yellow-400">
                    {{ darkMode ? 'Modo Claro ☀️' : 'Modo Oscuro 🌙' }}
                </button>
            </div>

            <div class="p-4 border-t border-gray-200 dark:border-yellow-500/20">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-full bg-yellow-400/20 flex items-center justify-center">
                        <span class="text-yellow-500 text-xs font-black">
                            {{ page.props.auth.user.name.charAt(0).toUpperCase() }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Administrador</p>
                        <p class="text-sm font-semibold truncate max-w-[140px]">
                            {{ page.props.auth.user.name }}
                        </p>
                    </div>
                </div>

                <Link :href="route('logout')" method="post" as="button"
                      class="w-full text-center text-xs text-gray-500 hover:text-yellow-500 py-1.5 rounded-lg hover:bg-yellow-400/5 transition">
                    Cerrar sesión →
                </Link>
            </div>
        </aside>

        <!-- CONTENIDO -->
        <div class="flex-1 flex flex-col min-h-screen">

            <header class="bg-white dark:bg-black border-b border-gray-200 dark:border-yellow-500/20 px-6 py-4 sticky top-0 z-10 transition-colors">
                <slot name="header" />
            </header>

            <main class="flex-1 p-6">
                <slot />
            </main>
        </div>
    </div>

    <!-- CONTENIDO MÓVIL -->
    <main class="lg:hidden p-4 pb-8">
        <slot />
    </main>

</div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
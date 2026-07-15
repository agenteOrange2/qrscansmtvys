<script setup lang="ts">
import Lucide from '@/components/Base/Lucide';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { onBeforeUnmount, onMounted, ref } from 'vue';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);

const page = usePage();
const isLoggedIn = () => !!(page.props.auth as { user?: unknown } | undefined)?.user;

const mobileMenu = ref(false);
const scrolled = ref(false);
const showBackToTop = ref(false);

function onScroll(): void {
    scrolled.value = window.scrollY > 30;
    showBackToTop.value = window.scrollY > 400;
}

function scrollTop(): void {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

onMounted(() => {
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
});

onBeforeUnmount(() => window.removeEventListener('scroll', onScroll));

const features = [
    {
        icon: 'QrCode',
        title: 'Escaneo QR Instantáneo',
        text: 'Captura la información del gafete en segundos con nuestro escáner optimizado: cámara, linterna y detección automática.',
        action: 'Escanear ahora',
        color: 'text-blue-700 dark:text-blue-400',
        iconBox: 'bg-gradient-to-br from-blue-600 to-blue-800 shadow-blue-500/30 group-hover:shadow-blue-500/50',
        hover: 'hover:shadow-blue-500/10 hover:border-blue-500/20 group-hover:text-blue-600',
    },
    {
        icon: 'Users',
        title: 'Escaneo Grupal',
        text: 'Escanea equipos completos de la misma empresa —5, 8, 10 o más gafetes— y guárdalos todos juntos en una sola operación.',
        action: 'Capturar en grupo',
        color: 'text-purple-600 dark:text-purple-400',
        iconBox: 'bg-gradient-to-br from-purple-500 to-purple-600 shadow-purple-500/30 group-hover:shadow-purple-500/50',
        hover: 'hover:shadow-purple-500/10 hover:border-purple-500/20 group-hover:text-purple-600',
    },
    {
        icon: 'FileSpreadsheet',
        title: 'Exportación a Excel',
        text: 'Exporta todos tus contactos capturados a Excel con un solo clic, incluyendo marcas, comentarios y grupos.',
        action: 'Exportar datos',
        color: 'text-green-600 dark:text-green-400',
        iconBox: 'bg-gradient-to-br from-green-500 to-green-600 shadow-green-500/30 group-hover:shadow-green-500/50',
        hover: 'hover:shadow-green-500/10 hover:border-green-500/20 group-hover:text-green-600',
    },
    {
        icon: 'MapPin',
        title: 'Seguimiento por Estado',
        text: 'Registra y filtra contactos por estado de México para un análisis geográfico detallado de tus visitantes.',
        action: 'Ver ubicaciones',
        color: 'text-orange-600 dark:text-orange-400',
        iconBox: 'bg-gradient-to-br from-orange-500 to-orange-600 shadow-orange-500/30 group-hover:shadow-orange-500/50',
        hover: 'hover:shadow-orange-500/10 hover:border-orange-500/20 group-hover:text-orange-600',
    },
] as const;

const groupSteps = [
    { icon: 'ScanLine', title: 'Escanea sin pausas', text: 'La cámara sigue activa: pasa gafete tras gafete y cada contacto se suma a la lista.' },
    { icon: 'Building2', title: 'Misma empresa, un grupo', text: 'La empresa se detecta con el primer gafete y se aplica a todo el equipo automáticamente.' },
    { icon: 'Save', title: 'Un solo guardado', text: 'Todos los contactos, sus marcas de interés y comentarios se registran juntos en una sola operación.' },
] as const;
</script>

<template>
    <div class="min-h-screen scroll-smooth bg-white text-slate-900 antialiased dark:bg-slate-900 dark:text-white">
        <Head title="SMTVYS Scan QRCode">
            <link rel="icon" type="image/png" href="/img/smtvys-favicon.png" />
        </Head>

        <!-- ===== Navbar ===== -->
        <nav
            class="fixed inset-x-0 top-0 z-50 transition-all duration-300"
            :class="scrolled ? 'bg-white/90 shadow-md backdrop-blur dark:bg-slate-900/90' : 'bg-transparent'"
        >
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6">
                <a href="#home" class="flex items-center gap-2">
                    <img src="/img/logo_smtvys.png" alt="SMTVYS" class="h-6 sm:h-7" />
                </a>

                <!-- Links desktop -->
                <div class="hidden items-center gap-8 text-sm font-medium md:flex">
                    <a href="#home" class="transition hover:text-blue-700 dark:hover:text-blue-400">Inicio</a>
                    <a href="#features" class="transition hover:text-blue-700 dark:hover:text-blue-400">Características</a>
                    <a href="#grupal" class="transition hover:text-blue-700 dark:hover:text-blue-400">Escaneo Grupal</a>
                    <a href="#contact" class="transition hover:text-blue-700 dark:hover:text-blue-400">Contacto</a>
                </div>

                <div class="flex items-center gap-2">
                    <Link
                        v-if="isLoggedIn()"
                        :href="route('admin.dashboard')"
                        class="inline-flex h-9 items-center justify-center rounded-full bg-blue-700 px-5 text-xs font-semibold uppercase tracking-wider text-white shadow-lg shadow-blue-700/30 transition hover:bg-blue-800"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="inline-flex h-9 items-center justify-center rounded-full bg-blue-700 px-5 text-xs font-semibold uppercase tracking-wider text-white shadow-lg shadow-blue-700/30 transition hover:bg-blue-800"
                        >
                            Iniciar Sesión
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="hidden h-9 items-center justify-center rounded-full border border-blue-700 px-5 text-xs font-semibold uppercase tracking-wider text-blue-700 transition hover:bg-blue-700 hover:text-white sm:inline-flex dark:border-blue-400 dark:text-blue-400"
                        >
                            Registrar
                        </Link>
                    </template>

                    <!-- Hamburguesa -->
                    <button
                        type="button"
                        class="ml-1 rounded-md p-2 hover:bg-slate-100 md:hidden dark:hover:bg-slate-800"
                        aria-label="Menú"
                        @click="mobileMenu = !mobileMenu"
                    >
                        <Lucide :icon="mobileMenu ? 'X' : 'Menu'" class="h-5 w-5" />
                    </button>
                </div>
            </div>

            <!-- Menú móvil -->
            <Transition
                enter-active-class="transition duration-200"
                enter-from-class="opacity-0 -translate-y-2"
                leave-active-class="transition duration-150"
                leave-to-class="opacity-0 -translate-y-2"
            >
                <div v-if="mobileMenu" class="border-t border-slate-100 bg-white/95 px-4 py-3 backdrop-blur md:hidden dark:border-slate-800 dark:bg-slate-900/95">
                    <a href="#home" class="block rounded-md px-3 py-2 text-sm font-medium hover:bg-blue-50 hover:text-blue-700 dark:hover:bg-slate-800" @click="mobileMenu = false">Inicio</a>
                    <a href="#features" class="block rounded-md px-3 py-2 text-sm font-medium hover:bg-blue-50 hover:text-blue-700 dark:hover:bg-slate-800" @click="mobileMenu = false">Características</a>
                    <a href="#grupal" class="block rounded-md px-3 py-2 text-sm font-medium hover:bg-blue-50 hover:text-blue-700 dark:hover:bg-slate-800" @click="mobileMenu = false">Escaneo Grupal</a>
                    <a href="#contact" class="block rounded-md px-3 py-2 text-sm font-medium hover:bg-blue-50 hover:text-blue-700 dark:hover:bg-slate-800" @click="mobileMenu = false">Contacto</a>
                </div>
            </Transition>
        </nav>

        <!-- ===== Hero ===== -->
        <section id="home" class="relative overflow-hidden bg-slate-50/50 pb-24 pt-32 md:pb-32 md:pt-44 dark:bg-slate-800/20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6">
                <div class="grid grid-cols-1 items-center gap-12 md:grid-cols-2">
                    <div class="relative z-10 md:pr-6">
                        <h6 class="mb-3 text-sm font-bold uppercase tracking-wider text-blue-700 dark:text-blue-400">SMTVYS APP</h6>
                        <h1 class="mb-5 text-4xl font-bold leading-tight sm:text-5xl lg:text-6xl">
                            SMTVYS <span class="bg-gradient-to-r from-blue-700 to-blue-500 bg-clip-text text-transparent">Scan App</span>
                        </h1>
                        <p class="max-w-xl text-lg text-slate-400">
                            Plataforma enfocada en capturar contactos mediante códigos QR en eventos y exposiciones:
                            escanea gafetes individuales o <span class="font-semibold text-slate-600 dark:text-slate-300">equipos completos sin límite de personas</span>,
                            asócialos a tus marcas de interés y exporta todo a Excel.
                        </p>

                        <div class="mt-8 flex flex-wrap items-center gap-3">
                            <Link
                                :href="isLoggedIn() ? route('admin.scan') : route('login')"
                                class="inline-flex h-12 items-center justify-center gap-2 rounded-full bg-blue-700 px-7 font-semibold text-white shadow-xl shadow-blue-700/30 transition hover:-translate-y-0.5 hover:bg-blue-800"
                            >
                                <Lucide icon="ScanLine" class="h-5 w-5" />
                                Comenzar a escanear
                            </Link>
                            <a
                                href="#grupal"
                                class="inline-flex h-12 items-center justify-center gap-2 rounded-full border border-slate-200 px-7 font-semibold transition hover:border-blue-700 hover:text-blue-700 dark:border-slate-700 dark:hover:border-blue-400 dark:hover:text-blue-400"
                            >
                                <Lucide icon="Users" class="h-5 w-5" />
                                Escaneo grupal
                            </a>
                        </div>

                        <!-- Mini stats -->
                        <div class="mt-10 grid max-w-md grid-cols-3 gap-4 text-center">
                            <div>
                                <div class="text-2xl font-bold text-blue-700 dark:text-blue-400">&lt; 2s</div>
                                <div class="text-xs text-slate-400">por escaneo</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-blue-700 dark:text-blue-400">∞</div>
                                <div class="text-xs text-slate-400">gafetes por grupo</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-blue-700 dark:text-blue-400">32</div>
                                <div class="text-xs text-slate-400">estados de México</div>
                            </div>
                        </div>
                    </div>

                    <!-- Mockup -->
                    <div class="relative">
                        <div
                            class="absolute bottom-1/2 left-1/2 z-0 size-[320px] -translate-x-1/2 translate-y-1/2 rounded-full bg-gradient-to-tl from-blue-700 via-blue-700/70 to-blue-500/20 shadow-md shadow-red-500/10 sm:size-[420px] md:left-0 md:translate-x-0 md:size-[500px]"
                        ></div>
                        <span class="absolute right-6 top-0 z-0 size-16 animate-[spin_10s_linear_infinite] rounded-lg bg-blue-500/20"></span>
                        <span class="absolute -left-2 bottom-10 z-0 size-10 animate-[spin_14s_linear_infinite] rounded-lg bg-red-500/20"></span>
                        <img
                            src="/img/mockup_app.png"
                            alt="App SMTVYS"
                            class="relative z-10 mx-auto w-56 rotate-12 drop-shadow-2xl transition-transform duration-700 hover:rotate-6 sm:w-72 md:w-80"
                        />
                        <!-- Chip flotante: grupo guardado -->
                        <div class="absolute -bottom-2 left-1/2 z-20 flex -translate-x-1/2 items-center gap-2 rounded-full bg-white px-4 py-2 text-xs font-semibold shadow-xl md:left-auto md:right-2 md:translate-x-0 dark:bg-slate-800">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-green-500/15">
                                <Lucide icon="Check" class="h-3.5 w-3.5 text-green-600" />
                            </span>
                            Grupo guardado · 8 contactos
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== Características ===== -->
        <section id="features" class="relative bg-gradient-to-b from-transparent to-blue-50/30 py-16 md:py-24 dark:to-slate-800/20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6">
                <div class="pb-6 text-center">
                    <h6 class="mb-3 text-sm font-bold uppercase tracking-wider text-blue-700 dark:text-blue-400">Características</h6>
                    <h2 class="mb-6 text-2xl font-bold leading-normal md:text-3xl">Sistema Completo de Gestión de Contactos</h2>
                    <p class="mx-auto max-w-2xl text-slate-400">
                        Captura, organiza y gestiona información de contactos mediante códigos QR con una plataforma
                        intuitiva y poderosa diseñada para eventos y exposiciones.
                    </p>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="feature in features"
                        :key="feature.title"
                        class="group rounded-3xl border border-transparent p-6 transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl"
                        :class="feature.hover"
                    >
                        <div
                            class="flex size-16 items-center justify-center rounded-2xl text-white shadow-lg transition-all duration-500 group-hover:scale-110"
                            :class="feature.iconBox"
                        >
                            <Lucide :icon="feature.icon" class="h-7 w-7" />
                        </div>
                        <div class="mt-7">
                            <h3 class="mb-3 text-lg font-bold transition-colors">{{ feature.title }}</h3>
                            <p class="leading-relaxed text-slate-500 dark:text-slate-400">{{ feature.text }}</p>
                            <div class="mt-4 flex items-center gap-2 text-sm font-semibold transition-transform group-hover:translate-x-2" :class="feature.color">
                                <span>{{ feature.action }}</span>
                                <Lucide icon="ArrowRight" class="h-4 w-4" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Marcas / organización -->
                <div class="mt-16 grid grid-cols-1 items-center gap-6 md:mt-24 md:grid-cols-12">
                    <div class="md:col-span-6 lg:col-span-5">
                        <div class="rounded-2xl bg-red-500/5 px-6 pt-6 shadow shadow-red-500/20 dark:bg-red-500/10">
                            <img src="/img/mock_up_proximamente.png" alt="Mockup SMTVYS" class="mx-auto" />
                        </div>
                    </div>

                    <div class="md:col-span-6 lg:col-span-7">
                        <div class="lg:ml-10">
                            <h6 class="mb-3 text-sm font-bold uppercase tracking-wider text-blue-700 dark:text-blue-400">Personalizable</h6>
                            <h2 class="mb-6 text-2xl font-bold leading-normal md:text-3xl">
                                Trabaja más rápido con <br class="hidden sm:block" />
                                herramientas poderosas
                            </h2>
                            <p class="max-w-xl text-slate-400">
                                Organiza cada contacto por marcas de interés con comentarios personalizados, roles y
                                permisos por usuario, y estadísticas en tiempo real de tu equipo en la expo.
                            </p>

                            <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                                <div class="group relative flex overflow-hidden rounded-md bg-slate-50/50 p-6 shadow duration-500 hover:bg-red-500 dark:bg-slate-800/20 dark:shadow-gray-800 dark:hover:bg-red-500">
                                    <span class="text-blue-700 duration-500 group-hover:text-white">
                                        <Lucide icon="Shield" class="mt-2 size-8" />
                                    </span>
                                    <div class="ml-3 flex-1">
                                        <h5 class="text-lg font-semibold duration-500 group-hover:text-white">Mejora la seguridad</h5>
                                        <p class="mt-2 text-slate-400 duration-500 group-hover:text-white/60">
                                            Accesos con roles y permisos: cada usuario solo ve sus propios contactos capturados.
                                        </p>
                                    </div>
                                    <Lucide icon="Shield" class="absolute left-1 top-5 size-24 text-slate-900/[0.02] duration-500 group-hover:text-white/[0.1] dark:text-white/[0.03]" />
                                </div>

                                <div class="group relative flex overflow-hidden rounded-md bg-slate-50/50 p-6 shadow duration-500 hover:bg-red-500 dark:bg-slate-800/20 dark:shadow-gray-800 dark:hover:bg-red-500">
                                    <span class="text-blue-700 duration-500 group-hover:text-white">
                                        <Lucide icon="Aperture" class="mt-2 size-8" />
                                    </span>
                                    <div class="ml-3 flex-1">
                                        <h5 class="text-lg font-semibold duration-500 group-hover:text-white">Alto rendimiento</h5>
                                        <p class="mt-2 text-slate-400 duration-500 group-hover:text-white/60">
                                            Escáner con detección nativa del navegador: lecturas ultrarrápidas incluso con poca luz.
                                        </p>
                                    </div>
                                    <Lucide icon="Aperture" class="absolute left-1 top-5 size-24 text-slate-900/[0.02] duration-500 group-hover:text-white/[0.1] dark:text-white/[0.03]" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== Escaneo Grupal ===== -->
        <section id="grupal" class="relative overflow-hidden py-16 md:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6">
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-800 via-blue-700 to-blue-600 px-6 py-12 text-white shadow-2xl shadow-blue-700/30 md:px-12 md:py-16">
                    <span class="absolute -right-10 -top-10 size-48 rounded-full bg-white/10"></span>
                    <span class="absolute -bottom-16 -left-10 size-64 rounded-full bg-red-500/20"></span>

                    <div class="relative grid grid-cols-1 items-center gap-10 lg:grid-cols-2">
                        <div>
                            <h6 class="mb-3 text-sm font-bold uppercase tracking-wider text-blue-200">Nuevo</h6>
                            <h2 class="mb-4 text-2xl font-bold leading-normal md:text-4xl">
                                Escaneo grupal:<br />todo el equipo, una sola operación
                            </h2>
                            <p class="max-w-xl text-blue-100">
                                ¿Llegó un equipo completo de la misma empresa a tu stand? Actívalo en modo grupal y
                                registra a todos de una vez, sin repetir formularios ni perder tiempo entre visitantes.
                            </p>
                            <Link
                                :href="isLoggedIn() ? route('admin.scan') : route('login')"
                                class="mt-8 inline-flex h-12 items-center justify-center gap-2 rounded-full bg-white px-7 font-semibold text-blue-700 shadow-xl transition hover:-translate-y-0.5 hover:bg-blue-50"
                            >
                                <Lucide icon="Users" class="h-5 w-5" />
                                Probar escaneo grupal
                            </Link>
                        </div>

                        <div class="space-y-4">
                            <div
                                v-for="(step, index) in groupSteps"
                                :key="step.title"
                                class="flex items-start gap-4 rounded-2xl bg-white/10 p-5 backdrop-blur transition duration-300 hover:bg-white/15"
                            >
                                <div class="flex h-11 w-11 flex-none items-center justify-center rounded-xl bg-white/15 font-bold">
                                    {{ index + 1 }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 font-semibold">
                                        <Lucide :icon="step.icon" class="h-4 w-4 text-blue-200" />
                                        {{ step.title }}
                                    </div>
                                    <p class="mt-1 text-sm text-blue-100">{{ step.text }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== CTA / Contacto ===== -->
        <section id="contact" class="py-16 md:py-24">
            <div class="mx-auto max-w-3xl px-4 text-center sm:px-6">
                <h6 class="mb-3 text-sm font-bold uppercase tracking-wider text-blue-700 dark:text-blue-400">Comienza hoy</h6>
                <h2 class="mb-5 text-2xl font-bold md:text-3xl">¿Listo para tu próxima expo?</h2>
                <p class="mx-auto max-w-xl text-slate-400">
                    Inicia sesión y empieza a capturar contactos en segundos. Si necesitas una cuenta o soporte,
                    escríbenos y con gusto te ayudamos.
                </p>
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <Link
                        :href="isLoggedIn() ? route('admin.dashboard') : route('login')"
                        class="inline-flex h-12 items-center justify-center gap-2 rounded-full bg-blue-700 px-7 font-semibold text-white shadow-xl shadow-blue-700/30 transition hover:-translate-y-0.5 hover:bg-blue-800"
                    >
                        <Lucide icon="LogIn" class="h-5 w-5" />
                        {{ isLoggedIn() ? 'Ir al dashboard' : 'Iniciar sesión' }}
                    </Link>
                    <a
                        href="mailto:soporte@kuiraweb.com"
                        class="inline-flex h-12 items-center justify-center gap-2 rounded-full border border-slate-200 px-7 font-semibold transition hover:border-blue-700 hover:text-blue-700 dark:border-slate-700 dark:hover:border-blue-400 dark:hover:text-blue-400"
                    >
                        <Lucide icon="Mail" class="h-5 w-5" />
                        Contacto
                    </a>
                </div>
            </div>
        </section>

        <!-- ===== Footer ===== -->
        <footer class="bg-slate-800 py-8 dark:bg-gray-950">
            <div class="mx-auto max-w-7xl px-4 sm:px-6">
                <div class="grid grid-cols-1 items-center gap-8 md:grid-cols-12 md:gap-0">
                    <div class="text-center md:col-span-3 md:text-left">
                        <img src="/img/logo_smtvys.png" alt="SMTVYS" class="mx-auto h-6 brightness-0 invert md:mx-0" />
                    </div>

                    <div class="text-center md:col-span-5">
                        <p class="text-sm text-gray-400">
                            © {{ new Date().getFullYear() }} SMTVYS. Diseñado y desarrollado con
                            <span class="text-red-500">♥</span> por
                            <a href="https://kuiraweb.com" target="_blank" rel="noopener" class="text-gray-300 hover:text-white">Kuiraweb</a>.
                        </p>
                    </div>

                    <div class="md:col-span-4">
                        <ul class="flex justify-center gap-2 md:justify-end">
                            <li>
                                <a
                                    href="https://www.facebook.com"
                                    target="_blank"
                                    rel="noopener"
                                    class="inline-flex size-8 items-center justify-center rounded-md border border-gray-700 text-slate-300 transition hover:border-red-500 hover:bg-red-500 hover:text-white"
                                    title="Facebook"
                                >
                                    <Lucide icon="Facebook" class="h-4 w-4" />
                                </a>
                            </li>
                            <li>
                                <a
                                    href="https://www.instagram.com"
                                    target="_blank"
                                    rel="noopener"
                                    class="inline-flex size-8 items-center justify-center rounded-md border border-gray-700 text-slate-300 transition hover:border-red-500 hover:bg-red-500 hover:text-white"
                                    title="Instagram"
                                >
                                    <Lucide icon="Instagram" class="h-4 w-4" />
                                </a>
                            </li>
                            <li>
                                <a
                                    href="https://www.linkedin.com"
                                    target="_blank"
                                    rel="noopener"
                                    class="inline-flex size-8 items-center justify-center rounded-md border border-gray-700 text-slate-300 transition hover:border-red-500 hover:bg-red-500 hover:text-white"
                                    title="LinkedIn"
                                >
                                    <Lucide icon="Linkedin" class="h-4 w-4" />
                                </a>
                            </li>
                            <li>
                                <a
                                    href="mailto:soporte@kuiraweb.com"
                                    class="inline-flex size-8 items-center justify-center rounded-md border border-gray-700 text-slate-300 transition hover:border-red-500 hover:bg-red-500 hover:text-white"
                                    title="Correo"
                                >
                                    <Lucide icon="Mail" class="h-4 w-4" />
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Back to top -->
        <Transition
            enter-active-class="transition duration-200"
            enter-from-class="opacity-0 translate-y-3"
            leave-active-class="transition duration-200"
            leave-to-class="opacity-0 translate-y-3"
        >
            <button
                v-if="showBackToTop"
                type="button"
                class="fixed bottom-5 right-5 z-40 flex h-10 w-10 items-center justify-center rounded-full bg-red-500 text-white shadow-lg shadow-red-500/30 transition hover:bg-red-600"
                aria-label="Volver arriba"
                @click="scrollTop"
            >
                <Lucide icon="ArrowUp" class="h-5 w-5" />
            </button>
        </Transition>
    </div>
</template>

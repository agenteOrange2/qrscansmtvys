<script setup lang="ts">
import Button from '@/components/Base/Button';
import Lucide from '@/components/Base/Lucide';
import RazeLayout from '@/layouts/RazeLayout.vue';
import { Link } from '@inertiajs/vue3';

interface RecentScan {
    id: number;
    nombre: string;
    apellidos: string | null;
    empresa: string | null;
    scan_group_id: number | null;
    created_at: string;
    user?: { id: number; name: string; last_name: string | null } | null;
}

defineProps<{
    stats: {
        misEscaneos: number;
        totalSistema: number | null;
        escaneosHoy: number;
        grupos: number;
        empresas: number;
        marcas: number;
    };
    recentScans: RecentScan[];
    isAdmin: boolean;
}>();

function formatDate(value: string): string {
    return new Date(value).toLocaleString('es-MX', {
        day: '2-digit',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit',
    });
}
</script>

<template>
    <RazeLayout title="Dashboard">
        <div class="mt-2 space-y-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-lg font-medium">Panel de control</h1>
                    <p class="text-sm text-slate-500">Resumen de la captura de contactos por QR.</p>
                </div>
                <Button variant="primary" :as="Link" :href="route('admin.scan')">
                    <Lucide icon="ScanLine" class="mr-2 h-4 w-4" />
                    Escanear ahora
                </Button>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-6 sm:col-span-6 xl:col-span-3">
                    <div class="box p-5">
                        <div class="flex items-center gap-4">
                            <div class="rounded-full bg-primary/10 p-3">
                                <Lucide icon="ScanLine" class="h-6 w-6 text-primary" />
                            </div>
                            <div>
                                <div class="text-xs text-slate-500">Mis escaneos</div>
                                <div class="text-2xl font-medium">{{ stats.misEscaneos }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-6 sm:col-span-6 xl:col-span-3">
                    <div class="box p-5">
                        <div class="flex items-center gap-4">
                            <div class="rounded-full bg-success/10 p-3">
                                <Lucide icon="CalendarCheck" class="h-6 w-6 text-success" />
                            </div>
                            <div>
                                <div class="text-xs text-slate-500">Escaneos hoy</div>
                                <div class="text-2xl font-medium">{{ stats.escaneosHoy }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-6 sm:col-span-6 xl:col-span-3">
                    <div class="box p-5">
                        <div class="flex items-center gap-4">
                            <div class="rounded-full bg-warning/10 p-3">
                                <Lucide icon="Users" class="h-6 w-6 text-warning" />
                            </div>
                            <div>
                                <div class="text-xs text-slate-500">Grupos capturados</div>
                                <div class="text-2xl font-medium">{{ stats.grupos }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-6 sm:col-span-6 xl:col-span-3">
                    <div class="box p-5">
                        <div class="flex items-center gap-4">
                            <div class="rounded-full bg-pending/10 p-3">
                                <Lucide icon="Building2" class="h-6 w-6 text-pending" />
                            </div>
                            <div>
                                <div class="text-xs text-slate-500">Empresas distintas</div>
                                <div class="text-2xl font-medium">{{ stats.empresas }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-12 gap-6">
                <!-- Últimos escaneos -->
                <div class="col-span-12 xl:col-span-8">
                    <div class="box">
                        <div class="flex items-center border-b border-slate-200 px-5 py-4 dark:border-darkmode-400">
                            <div class="text-base font-medium">Últimos escaneos</div>
                            <Link
                                :href="route('admin.usuarios-capturados.index')"
                                class="ml-auto text-xs text-primary hover:underline"
                            >
                                Ver todos
                            </Link>
                        </div>
                        <div class="p-5">
                            <div v-if="recentScans.length === 0" class="py-8 text-center text-sm text-slate-400">
                                Aún no hay escaneos registrados.
                            </div>
                            <div
                                v-for="scan in recentScans"
                                :key="scan.id"
                                class="flex items-center gap-4 border-b border-slate-100 py-3 last:border-0 dark:border-darkmode-400/50"
                            >
                                <div class="flex h-10 w-10 flex-none items-center justify-center rounded-full bg-primary/10">
                                    <Lucide :icon="scan.scan_group_id ? 'Users' : 'User'" class="h-5 w-5 text-primary" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="truncate font-medium">{{ scan.nombre }} {{ scan.apellidos }}</div>
                                    <div class="truncate text-xs text-slate-500">
                                        {{ scan.empresa || 'Sin empresa' }}
                                        <template v-if="isAdmin && scan.user"> · por {{ scan.user.name }}</template>
                                    </div>
                                </div>
                                <span
                                    v-if="scan.scan_group_id"
                                    class="flex-none rounded-full bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary"
                                >
                                    Grupo #{{ scan.scan_group_id }}
                                </span>
                                <span class="flex-none text-xs text-slate-400">{{ formatDate(scan.created_at) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumen -->
                <div class="col-span-12 xl:col-span-4">
                    <div class="box">
                        <div class="border-b border-slate-200 px-5 py-4 dark:border-darkmode-400">
                            <div class="text-base font-medium">Resumen</div>
                        </div>
                        <div class="space-y-4 p-5">
                            <div v-if="isAdmin && stats.totalSistema !== null" class="flex items-center justify-between">
                                <span class="text-slate-500">Total del sistema</span>
                                <span class="font-medium">{{ stats.totalSistema }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Marcas registradas</span>
                                <span class="font-medium">{{ stats.marcas }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Grupos capturados</span>
                                <span class="font-medium">{{ stats.grupos }}</span>
                            </div>
                            <div class="border-t border-slate-100 pt-4 dark:border-darkmode-400">
                                <Button variant="outline-primary" class="w-full" :as="Link" :href="route('admin.usuarios-capturados.index')">
                                    <Lucide icon="Table" class="mr-2 h-4 w-4" />
                                    Ver usuarios capturados
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </RazeLayout>
</template>

<script setup lang="ts">
import Button from '@/components/Base/Button';
import { FormCheck, FormInput, FormSelect } from '@/components/Base/Form';
import { Dialog } from '@/components/Base/Headless';
import Lucide from '@/components/Base/Lucide';
import RazeLayout from '@/layouts/RazeLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

interface ScanRow {
    id: number;
    nombre: string;
    apellidos: string | null;
    puesto: string | null;
    empresa: string | null;
    estado: string | null;
    telefono: string | null;
    correo: string | null;
    scan_group_id: number | null;
    created_at: string;
    user?: { id: number; name: string; last_name: string | null } | null;
    group?: { id: number; empresa: string | null } | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

const props = defineProps<{
    scans: {
        data: ScanRow[];
        links: PaginationLink[];
        total: number;
        from: number | null;
        to: number | null;
    };
    filters: { search: string; per_page: number; sort: string; direction: string };
    stats: { misEscaneos: number; totalSistema: number | null; gruposHoy: number };
    isAdmin: boolean;
}>();

const search = ref(props.filters.search);
const perPage = ref(String(props.filters.per_page || 10));
const selected = ref<number[]>([]);
const confirmDialog = ref<'none' | 'bulk' | number>('none');

let searchTimeout: ReturnType<typeof setTimeout> | undefined;

function reload(extra: Record<string, string | number> = {}): void {
    router.get(
        route('admin.usuarios-capturados.index'),
        {
            search: search.value || undefined,
            per_page: Number(perPage.value),
            sort: props.filters.sort,
            direction: props.filters.direction,
            ...extra,
        },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => reload(), 350);
});

watch(perPage, () => reload());

function sortBy(field: string): void {
    const direction =
        props.filters.sort === field && props.filters.direction === 'asc' ? 'desc' : 'asc';
    reload({ sort: field, direction });
}

const allSelected = computed({
    get: () => props.scans.data.length > 0 && selected.value.length === props.scans.data.length,
    set: (value: boolean) => {
        selected.value = value ? props.scans.data.map((scan) => scan.id) : [];
    },
});

function deleteSelected(): void {
    const ids = confirmDialog.value === 'bulk' ? selected.value : [confirmDialog.value as number];

    router.delete(route('admin.usuarios-capturados.destroy-many'), {
        data: { ids },
        preserveScroll: true,
        onSuccess: () => {
            selected.value = [];
            confirmDialog.value = 'none';
        },
    });
}

function formatDate(value: string): string {
    return new Date(value).toLocaleString('es-MX', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function sortIcon(field: string): string {
    if (props.filters.sort !== field) return 'ChevronsUpDown';

    return props.filters.direction === 'asc' ? 'ChevronUp' : 'ChevronDown';
}
</script>

<template>
    <RazeLayout title="Usuarios Capturados">
        <div class="mt-2 space-y-6">
            <!-- Stats -->
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12 sm:col-span-4">
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
                <div v-if="isAdmin && stats.totalSistema !== null" class="col-span-12 sm:col-span-4">
                    <div class="box p-5">
                        <div class="flex items-center gap-4">
                            <div class="rounded-full bg-success/10 p-3">
                                <Lucide icon="Database" class="h-6 w-6 text-success" />
                            </div>
                            <div>
                                <div class="text-xs text-slate-500">Total del sistema</div>
                                <div class="text-2xl font-medium">{{ stats.totalSistema }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 sm:col-span-4">
                    <div class="box p-5">
                        <div class="flex items-center gap-4">
                            <div class="rounded-full bg-warning/10 p-3">
                                <Lucide icon="Users" class="h-6 w-6 text-warning" />
                            </div>
                            <div>
                                <div class="text-xs text-slate-500">Grupos capturados hoy</div>
                                <div class="text-2xl font-medium">{{ stats.gruposHoy }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="box">
                <div class="flex flex-col gap-3 border-b border-slate-200 p-5 dark:border-darkmode-400 sm:flex-row sm:items-center">
                    <div class="relative w-full sm:w-64">
                        <Lucide icon="Search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <FormInput v-model="search" type="text" placeholder="Buscar contacto…" class="pl-9" />
                    </div>
                    <FormSelect v-model="perPage" class="w-full sm:w-24">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </FormSelect>
                    <div class="flex gap-2 sm:ml-auto">
                        <Button
                            v-if="selected.length > 0"
                            variant="soft-danger"
                            @click="confirmDialog = 'bulk'"
                        >
                            <Lucide icon="Trash2" class="mr-2 h-4 w-4" />
                            Eliminar ({{ selected.length }})
                        </Button>
                        <Button variant="outline-success" :as="'a'" :href="route('admin.usuarios-capturados.export')">
                            <Lucide icon="FileSpreadsheet" class="mr-2 h-4 w-4" />
                            Exportar
                        </Button>
                        <Button variant="primary" :as="Link" :href="route('admin.scan')">
                            <Lucide icon="ScanLine" class="mr-2 h-4 w-4" />
                            Escanear
                        </Button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 text-xs uppercase text-slate-500 dark:border-darkmode-400">
                                <th class="w-10 px-5 py-3">
                                    <FormCheck.Input v-model="allSelected" type="checkbox" />
                                </th>
                                <th class="cursor-pointer px-3 py-3" @click="sortBy('nombre')">
                                    <span class="flex items-center gap-1">Nombre <Lucide :icon="sortIcon('nombre')" class="h-3.5 w-3.5" /></span>
                                </th>
                                <th class="hidden px-3 py-3 lg:table-cell">Contacto</th>
                                <th class="hidden cursor-pointer px-3 py-3 md:table-cell" @click="sortBy('empresa')">
                                    <span class="flex items-center gap-1">Empresa <Lucide :icon="sortIcon('empresa')" class="h-3.5 w-3.5" /></span>
                                </th>
                                <th class="hidden px-3 py-3 xl:table-cell">Grupo</th>
                                <th v-if="isAdmin" class="hidden px-3 py-3 xl:table-cell">Capturado por</th>
                                <th class="hidden cursor-pointer px-3 py-3 sm:table-cell" @click="sortBy('created_at')">
                                    <span class="flex items-center gap-1">Fecha <Lucide :icon="sortIcon('created_at')" class="h-3.5 w-3.5" /></span>
                                </th>
                                <th class="px-3 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="scans.data.length === 0">
                                <td :colspan="isAdmin ? 8 : 7" class="px-5 py-10 text-center text-slate-400">
                                    No se encontraron registros.
                                </td>
                            </tr>
                            <tr
                                v-for="scan in scans.data"
                                :key="scan.id"
                                class="border-b border-slate-100 last:border-0 hover:bg-slate-50 dark:border-darkmode-400/50 dark:hover:bg-darkmode-400/30"
                            >
                                <td class="px-5 py-3">
                                    <FormCheck.Input v-model="selected" type="checkbox" :value="scan.id" />
                                </td>
                                <td class="px-3 py-3">
                                    <div class="font-medium">{{ scan.nombre }} {{ scan.apellidos }}</div>
                                    <div class="text-xs text-slate-500 lg:hidden">{{ scan.correo }}</div>
                                </td>
                                <td class="hidden px-3 py-3 lg:table-cell">
                                    <div>{{ scan.correo || '—' }}</div>
                                    <div class="text-xs text-slate-500">{{ scan.telefono || '' }}</div>
                                </td>
                                <td class="hidden px-3 py-3 md:table-cell">
                                    <div>{{ scan.empresa || '—' }}</div>
                                    <div class="text-xs text-slate-500">{{ scan.puesto || '' }}</div>
                                </td>
                                <td class="hidden px-3 py-3 xl:table-cell">
                                    <span
                                        v-if="scan.scan_group_id"
                                        class="inline-flex items-center gap-1 rounded-full bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary"
                                    >
                                        <Lucide icon="Users" class="h-3 w-3" />
                                        Grupo #{{ scan.scan_group_id }}
                                    </span>
                                    <span v-else class="text-xs text-slate-400">Individual</span>
                                </td>
                                <td v-if="isAdmin" class="hidden px-3 py-3 text-xs text-slate-500 xl:table-cell">
                                    {{ scan.user ? `${scan.user.name} ${scan.user.last_name ?? ''}` : '—' }}
                                </td>
                                <td class="hidden px-3 py-3 text-xs text-slate-500 sm:table-cell">
                                    {{ formatDate(scan.created_at) }}
                                </td>
                                <td class="px-3 py-3">
                                    <div class="flex justify-end gap-1">
                                        <Link
                                            :href="route('admin.usuarios-capturados.edit', scan.id)"
                                            class="rounded p-1.5 text-slate-400 hover:bg-slate-100 hover:text-primary dark:hover:bg-darkmode-400"
                                        >
                                            <Lucide icon="Pencil" class="h-4 w-4" />
                                        </Link>
                                        <button
                                            type="button"
                                            class="rounded p-1.5 text-slate-400 hover:bg-slate-100 hover:text-danger dark:hover:bg-darkmode-400"
                                            @click="confirmDialog = scan.id"
                                        >
                                            <Lucide icon="Trash2" class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="flex flex-col items-center gap-3 p-5 sm:flex-row">
                    <div class="text-xs text-slate-500">
                        Mostrando {{ scans.from ?? 0 }}–{{ scans.to ?? 0 }} de {{ scans.total }} registros
                    </div>
                    <nav class="flex flex-wrap gap-1 sm:ml-auto">
                        <template v-for="(link, index) in scans.links" :key="index">
                            <component
                                :is="link.url ? Link : 'span'"
                                :href="link.url ?? undefined"
                                preserve-scroll
                                class="min-w-[2rem] rounded-md px-2.5 py-1.5 text-center text-xs"
                                :class="[
                                    link.active
                                        ? 'bg-primary font-semibold text-white'
                                        : link.url
                                          ? 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-darkmode-400'
                                          : 'text-slate-300 dark:text-slate-600',
                                ]"
                                v-html="link.label"
                            />
                        </template>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Confirmación de borrado -->
        <Dialog :open="confirmDialog !== 'none'" @close="confirmDialog = 'none'">
            <Dialog.Panel class="p-6 text-center">
                <Lucide icon="Trash2" class="mx-auto mb-3 h-12 w-12 text-danger" />
                <div class="text-lg font-medium">¿Eliminar registros?</div>
                <p class="mt-2 text-sm text-slate-500">
                    {{
                        confirmDialog === 'bulk'
                            ? `Se eliminarán ${selected.length} registro(s) de forma permanente.`
                            : 'Este registro se eliminará de forma permanente.'
                    }}
                </p>
                <div class="mt-5 flex justify-center gap-3">
                    <Button variant="outline-secondary" @click="confirmDialog = 'none'">Cancelar</Button>
                    <Button variant="danger" @click="deleteSelected">Eliminar</Button>
                </div>
            </Dialog.Panel>
        </Dialog>
    </RazeLayout>
</template>

<script setup lang="ts">
import Button from '@/components/Base/Button';
import { FormInput } from '@/components/Base/Form';
import { Dialog } from '@/components/Base/Headless';
import Lucide from '@/components/Base/Lucide';
import RazeLayout from '@/layouts/RazeLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

interface UserRow {
    id: number;
    name: string;
    last_name: string | null;
    email: string;
    phone: string | null;
    qr_scans_count: number;
    roles: Array<{ id: number; name: string }>;
    created_at: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

const props = defineProps<{
    users: {
        data: UserRow[];
        links: PaginationLink[];
        total: number;
    };
    filters: { search: string };
}>();

const search = ref(props.filters.search);
const deleteTarget = ref<UserRow | null>(null);

let searchTimeout: ReturnType<typeof setTimeout> | undefined;

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(
            route('admin.users.index'),
            { search: search.value || undefined },
            { preserveState: true, replace: true },
        );
    }, 350);
});

function destroy(): void {
    if (!deleteTarget.value) return;

    router.delete(route('admin.users.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => (deleteTarget.value = null),
    });
}
</script>

<template>
    <RazeLayout title="Usuarios">
        <div class="mt-2 space-y-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-lg font-medium">Usuarios del sistema</h1>
                    <p class="text-sm text-slate-500">Cuentas con acceso a la plataforma.</p>
                </div>
                <Button variant="primary" :as="Link" :href="route('admin.users.create')">
                    <Lucide icon="UserPlus" class="mr-2 h-4 w-4" />
                    Nuevo usuario
                </Button>
            </div>

            <div class="box">
                <div class="border-b border-slate-200 p-5 dark:border-darkmode-400">
                    <div class="relative w-full sm:w-64">
                        <Lucide icon="Search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <FormInput v-model="search" type="text" placeholder="Buscar usuario…" class="pl-9" />
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 text-xs uppercase text-slate-500 dark:border-darkmode-400">
                                <th class="px-5 py-3">Usuario</th>
                                <th class="hidden px-3 py-3 md:table-cell">Teléfono</th>
                                <th class="hidden px-3 py-3 sm:table-cell">Roles</th>
                                <th class="hidden px-3 py-3 lg:table-cell">Escaneos</th>
                                <th class="px-3 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="users.data.length === 0">
                                <td colspan="5" class="px-5 py-10 text-center text-slate-400">
                                    No se encontraron usuarios.
                                </td>
                            </tr>
                            <tr
                                v-for="user in users.data"
                                :key="user.id"
                                class="border-b border-slate-100 last:border-0 hover:bg-slate-50 dark:border-darkmode-400/50 dark:hover:bg-darkmode-400/30"
                            >
                                <td class="px-5 py-3">
                                    <div class="font-medium">{{ user.name }} {{ user.last_name }}</div>
                                    <div class="text-xs text-slate-500">{{ user.email }}</div>
                                </td>
                                <td class="hidden px-3 py-3 text-slate-500 md:table-cell">{{ user.phone || '—' }}</td>
                                <td class="hidden px-3 py-3 sm:table-cell">
                                    <div class="flex flex-wrap gap-1">
                                        <span
                                            v-for="role in user.roles"
                                            :key="role.id"
                                            class="rounded-full bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary"
                                        >
                                            {{ role.name }}
                                        </span>
                                        <span v-if="user.roles.length === 0" class="text-xs text-slate-400">Sin rol</span>
                                    </div>
                                </td>
                                <td class="hidden px-3 py-3 text-slate-500 lg:table-cell">{{ user.qr_scans_count }}</td>
                                <td class="px-3 py-3">
                                    <div class="flex justify-end gap-1">
                                        <Link
                                            :href="route('admin.users.edit', user.id)"
                                            class="rounded p-1.5 text-slate-400 hover:bg-slate-100 hover:text-primary dark:hover:bg-darkmode-400"
                                        >
                                            <Lucide icon="Pencil" class="h-4 w-4" />
                                        </Link>
                                        <button
                                            type="button"
                                            class="rounded p-1.5 text-slate-400 hover:bg-slate-100 hover:text-danger dark:hover:bg-darkmode-400"
                                            @click="deleteTarget = user"
                                        >
                                            <Lucide icon="Trash2" class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="users.links.length > 3" class="flex justify-end p-5">
                    <nav class="flex flex-wrap gap-1">
                        <template v-for="(link, index) in users.links" :key="index">
                            <component
                                :is="link.url ? Link : 'span'"
                                :href="link.url ?? undefined"
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

        <Dialog :open="deleteTarget !== null" @close="deleteTarget = null">
            <Dialog.Panel class="p-6 text-center">
                <Lucide icon="Trash2" class="mx-auto mb-3 h-12 w-12 text-danger" />
                <div class="text-lg font-medium">
                    ¿Eliminar a {{ deleteTarget?.name }} {{ deleteTarget?.last_name }}?
                </div>
                <p class="mt-2 text-sm text-slate-500">Esta acción no se puede deshacer.</p>
                <div class="mt-5 flex justify-center gap-3">
                    <Button variant="outline-secondary" @click="deleteTarget = null">Cancelar</Button>
                    <Button variant="danger" @click="destroy">Eliminar</Button>
                </div>
            </Dialog.Panel>
        </Dialog>
    </RazeLayout>
</template>

<script setup lang="ts">
import Button from '@/components/Base/Button';
import { Dialog } from '@/components/Base/Headless';
import Lucide from '@/components/Base/Lucide';
import RazeLayout from '@/layouts/RazeLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface RoleRow {
    id: number;
    name: string;
    users_count: number;
    permissions: Array<{ id: number; name: string }>;
}

defineProps<{
    roles: RoleRow[];
}>();

const deleteTarget = ref<RoleRow | null>(null);

function destroy(): void {
    if (!deleteTarget.value) return;

    router.delete(route('admin.roles.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => (deleteTarget.value = null),
    });
}
</script>

<template>
    <RazeLayout title="Roles">
        <div class="mt-2 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-medium">Roles</h1>
                    <p class="text-sm text-slate-500">Roles y permisos de acceso al sistema.</p>
                </div>
                <Button variant="primary" :as="Link" :href="route('admin.roles.create')">
                    <Lucide icon="Plus" class="mr-2 h-4 w-4" />
                    Nuevo rol
                </Button>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <div v-for="role in roles" :key="role.id" class="box flex flex-col p-5">
                    <div class="flex items-center gap-3">
                        <div class="rounded-full bg-primary/10 p-2.5">
                            <Lucide icon="ShieldCheck" class="h-5 w-5 text-primary" />
                        </div>
                        <div>
                            <div class="font-medium">{{ role.name }}</div>
                            <div class="text-xs text-slate-500">{{ role.users_count }} usuario(s)</div>
                        </div>
                        <div class="ml-auto flex gap-1">
                            <Link
                                :href="route('admin.roles.edit', role.id)"
                                class="rounded p-1.5 text-slate-400 hover:bg-slate-100 hover:text-primary dark:hover:bg-darkmode-400"
                            >
                                <Lucide icon="Pencil" class="h-4 w-4" />
                            </Link>
                            <button
                                type="button"
                                class="rounded p-1.5 text-slate-400 hover:bg-slate-100 hover:text-danger dark:hover:bg-darkmode-400"
                                @click="deleteTarget = role"
                            >
                                <Lucide icon="Trash2" class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-1.5 border-t border-slate-100 pt-3 dark:border-darkmode-400">
                        <span
                            v-for="permission in role.permissions"
                            :key="permission.id"
                            class="rounded-full bg-slate-100 px-2 py-0.5 text-xs dark:bg-darkmode-400"
                        >
                            {{ permission.name }}
                        </span>
                        <span v-if="role.permissions.length === 0" class="text-xs text-slate-400">Sin permisos</span>
                    </div>
                </div>
            </div>
        </div>

        <Dialog :open="deleteTarget !== null" @close="deleteTarget = null">
            <Dialog.Panel class="p-6 text-center">
                <Lucide icon="Trash2" class="mx-auto mb-3 h-12 w-12 text-danger" />
                <div class="text-lg font-medium">¿Eliminar el rol "{{ deleteTarget?.name }}"?</div>
                <p class="mt-2 text-sm text-slate-500">Los roles con usuarios asignados no pueden eliminarse.</p>
                <div class="mt-5 flex justify-center gap-3">
                    <Button variant="outline-secondary" @click="deleteTarget = null">Cancelar</Button>
                    <Button variant="danger" @click="destroy">Eliminar</Button>
                </div>
            </Dialog.Panel>
        </Dialog>
    </RazeLayout>
</template>

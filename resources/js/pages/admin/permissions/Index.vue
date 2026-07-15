<script setup lang="ts">
import Button from '@/components/Base/Button';
import { FormHelp, FormInput, FormLabel } from '@/components/Base/Form';
import { Dialog } from '@/components/Base/Headless';
import Lucide from '@/components/Base/Lucide';
import RazeLayout from '@/layouts/RazeLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

interface PermissionRow {
    id: number;
    name: string;
    roles_count: number;
}

defineProps<{
    permissions: PermissionRow[];
}>();

const editTarget = ref<PermissionRow | null>(null);
const deleteTarget = ref<PermissionRow | null>(null);
const showCreate = ref(false);

const form = useForm({ name: '' });

function openCreate(): void {
    form.reset();
    form.clearErrors();
    showCreate.value = true;
}

function openEdit(permission: PermissionRow): void {
    form.name = permission.name;
    form.clearErrors();
    editTarget.value = permission;
}

function submit(): void {
    if (editTarget.value) {
        form.put(route('admin.permissions.update', editTarget.value.id), {
            preserveScroll: true,
            onSuccess: () => (editTarget.value = null),
        });
    } else {
        form.post(route('admin.permissions.store'), {
            preserveScroll: true,
            onSuccess: () => (showCreate.value = false),
        });
    }
}

function destroy(): void {
    if (!deleteTarget.value) return;

    router.delete(route('admin.permissions.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => (deleteTarget.value = null),
    });
}
</script>

<template>
    <RazeLayout title="Permisos">
        <div class="mt-2 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-medium">Permisos</h1>
                    <p class="text-sm text-slate-500">Permisos disponibles para asignar a los roles.</p>
                </div>
                <Button variant="primary" @click="openCreate">
                    <Lucide icon="Plus" class="mr-2 h-4 w-4" />
                    Nuevo permiso
                </Button>
            </div>

            <div class="box overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs uppercase text-slate-500 dark:border-darkmode-400">
                            <th class="px-5 py-3">Permiso</th>
                            <th class="px-3 py-3">Roles que lo usan</th>
                            <th class="px-3 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="permission in permissions"
                            :key="permission.id"
                            class="border-b border-slate-100 last:border-0 hover:bg-slate-50 dark:border-darkmode-400/50 dark:hover:bg-darkmode-400/30"
                        >
                            <td class="px-5 py-3 font-medium">{{ permission.name }}</td>
                            <td class="px-3 py-3 text-slate-500">{{ permission.roles_count }}</td>
                            <td class="px-3 py-3">
                                <div class="flex justify-end gap-1">
                                    <button
                                        type="button"
                                        class="rounded p-1.5 text-slate-400 hover:bg-slate-100 hover:text-primary dark:hover:bg-darkmode-400"
                                        @click="openEdit(permission)"
                                    >
                                        <Lucide icon="Pencil" class="h-4 w-4" />
                                    </button>
                                    <button
                                        type="button"
                                        class="rounded p-1.5 text-slate-400 hover:bg-slate-100 hover:text-danger dark:hover:bg-darkmode-400"
                                        @click="deleteTarget = permission"
                                    >
                                        <Lucide icon="Trash2" class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Crear / editar -->
        <Dialog :open="showCreate || editTarget !== null" @close="showCreate = false; editTarget = null">
            <Dialog.Panel class="p-6">
                <div class="mb-4 text-lg font-medium">
                    {{ editTarget ? 'Editar permiso' : 'Nuevo permiso' }}
                </div>
                <form @submit.prevent="submit">
                    <FormLabel>Nombre del permiso *</FormLabel>
                    <FormInput v-model="form.name" type="text" required autofocus />
                    <FormHelp v-if="form.errors.name" class="text-danger">{{ form.errors.name }}</FormHelp>
                    <div class="mt-5 flex justify-end gap-3">
                        <Button type="button" variant="outline-secondary" @click="showCreate = false; editTarget = null">
                            Cancelar
                        </Button>
                        <Button type="submit" variant="primary" :disabled="form.processing">Guardar</Button>
                    </div>
                </form>
            </Dialog.Panel>
        </Dialog>

        <!-- Eliminar -->
        <Dialog :open="deleteTarget !== null" @close="deleteTarget = null">
            <Dialog.Panel class="p-6 text-center">
                <Lucide icon="Trash2" class="mx-auto mb-3 h-12 w-12 text-danger" />
                <div class="text-lg font-medium">¿Eliminar el permiso "{{ deleteTarget?.name }}"?</div>
                <div class="mt-5 flex justify-center gap-3">
                    <Button variant="outline-secondary" @click="deleteTarget = null">Cancelar</Button>
                    <Button variant="danger" @click="destroy">Eliminar</Button>
                </div>
            </Dialog.Panel>
        </Dialog>
    </RazeLayout>
</template>

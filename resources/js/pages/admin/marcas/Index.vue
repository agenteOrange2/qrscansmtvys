<script setup lang="ts">
import Button from '@/components/Base/Button';
import { Dialog } from '@/components/Base/Headless';
import Lucide from '@/components/Base/Lucide';
import RazeLayout from '@/layouts/RazeLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Marca {
    id: number;
    nombre: string;
    descripcion: string | null;
    imagen: string | null;
    qr_scans_count: number;
}

defineProps<{
    marcas: Marca[];
}>();

const deleteTarget = ref<Marca | null>(null);

function destroy(): void {
    if (!deleteTarget.value) return;

    router.delete(route('admin.marcas.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => (deleteTarget.value = null),
    });
}
</script>

<template>
    <RazeLayout title="Marcas">
        <div class="mt-2 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-medium">Marcas</h1>
                    <p class="text-sm text-slate-500">Catálogo de marcas de interés para los escaneos.</p>
                </div>
                <Button variant="primary" :as="Link" :href="route('admin.marcas.create')">
                    <Lucide icon="Plus" class="mr-2 h-4 w-4" />
                    Nueva marca
                </Button>
            </div>

            <div v-if="marcas.length === 0" class="box p-12 text-center text-slate-400">
                <Lucide icon="Tags" class="mx-auto mb-3 h-12 w-12" />
                No hay marcas registradas todavía.
            </div>

            <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <div v-for="marca in marcas" :key="marca.id" class="box flex flex-col p-5">
                    <div class="flex items-start gap-4">
                        <img
                            v-if="marca.imagen"
                            :src="`/storage/${marca.imagen}`"
                            :alt="marca.nombre"
                            class="h-14 w-14 rounded-lg object-cover"
                        />
                        <div
                            v-else
                            class="flex h-14 w-14 items-center justify-center rounded-lg bg-slate-100 dark:bg-darkmode-400"
                        >
                            <Lucide icon="Tag" class="h-6 w-6 text-slate-400" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="truncate font-medium">{{ marca.nombre }}</div>
                            <div class="mt-0.5 line-clamp-2 text-xs text-slate-500">
                                {{ marca.descripcion || 'Sin descripción' }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center border-t border-slate-100 pt-3 dark:border-darkmode-400">
                        <span class="text-xs text-slate-500">
                            {{ marca.qr_scans_count }} escaneo(s) asociados
                        </span>
                        <div class="ml-auto flex gap-1">
                            <Link
                                :href="route('admin.marcas.edit', marca.id)"
                                class="rounded p-1.5 text-slate-400 hover:bg-slate-100 hover:text-primary dark:hover:bg-darkmode-400"
                            >
                                <Lucide icon="Pencil" class="h-4 w-4" />
                            </Link>
                            <button
                                type="button"
                                class="rounded p-1.5 text-slate-400 hover:bg-slate-100 hover:text-danger dark:hover:bg-darkmode-400"
                                @click="deleteTarget = marca"
                            >
                                <Lucide icon="Trash2" class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Dialog :open="deleteTarget !== null" @close="deleteTarget = null">
            <Dialog.Panel class="p-6 text-center">
                <Lucide icon="Trash2" class="mx-auto mb-3 h-12 w-12 text-danger" />
                <div class="text-lg font-medium">¿Eliminar la marca "{{ deleteTarget?.nombre }}"?</div>
                <p class="mt-2 text-sm text-slate-500">
                    Se eliminará también su asociación con los escaneos existentes.
                </p>
                <div class="mt-5 flex justify-center gap-3">
                    <Button variant="outline-secondary" @click="deleteTarget = null">Cancelar</Button>
                    <Button variant="danger" @click="destroy">Eliminar</Button>
                </div>
            </Dialog.Panel>
        </Dialog>
    </RazeLayout>
</template>

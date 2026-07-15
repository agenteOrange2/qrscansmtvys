<script setup lang="ts">
import Button from '@/components/Base/Button';
import { FormCheck, FormHelp, FormInput, FormLabel, FormSelect, FormTextarea } from '@/components/Base/Form';
import Lucide from '@/components/Base/Lucide';
import RazeLayout from '@/layouts/RazeLayout.vue';
import { estadosMexico } from '@/lib/estados';
import { Link, useForm } from '@inertiajs/vue3';
import { reactive } from 'vue';

interface Marca {
    id: number;
    nombre: string;
    descripcion: string | null;
    imagen: string | null;
}

const props = defineProps<{
    scan: {
        id: number;
        nombre: string;
        apellidos: string | null;
        puesto: string | null;
        empresa: string | null;
        estado: string | null;
        telefono: string | null;
        rol: string | null;
        correo: string | null;
        campos_adicionales: string;
        scan_group_id: number | null;
        group?: { id: number; empresa: string | null } | null;
        marcas: Array<{ id: number; comentarios: string | null }>;
    };
    marcas: Marca[];
}>();

const selectedMarcas = reactive<Record<number, { checked: boolean; comentarios: string }>>(
    Object.fromEntries(
        props.marcas.map((marca) => {
            const current = props.scan.marcas.find((m) => m.id === marca.id);

            return [marca.id, { checked: !!current, comentarios: current?.comentarios ?? '' }];
        }),
    ),
);

const form = useForm({
    nombre: props.scan.nombre ?? '',
    apellidos: props.scan.apellidos ?? '',
    puesto: props.scan.puesto ?? '',
    empresa: props.scan.empresa ?? '',
    estado: props.scan.estado ?? '',
    telefono: props.scan.telefono ?? '',
    rol: props.scan.rol ?? '',
    correo: props.scan.correo ?? '',
    campos_adicionales: props.scan.campos_adicionales ?? '',
    marcas: [] as Array<{ id: number; comentarios: string | null }>,
});

function submit(): void {
    form.marcas = Object.entries(selectedMarcas)
        .filter(([, value]) => value.checked)
        .map(([id, value]) => ({ id: Number(id), comentarios: value.comentarios || null }));

    form.put(route('admin.usuarios-capturados.update', props.scan.id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <RazeLayout title="Editar contacto">
        <div class="mt-2 space-y-6">
            <div class="flex items-center gap-3">
                <Link
                    :href="route('admin.usuarios-capturados.index')"
                    class="rounded-full p-2 hover:bg-slate-100 dark:hover:bg-darkmode-400"
                >
                    <Lucide icon="ArrowLeft" class="h-5 w-5" />
                </Link>
                <div>
                    <h1 class="text-lg font-medium">Editar contacto #{{ scan.id }}</h1>
                    <p v-if="scan.scan_group_id" class="flex items-center gap-1 text-xs text-primary">
                        <Lucide icon="Users" class="h-3.5 w-3.5" />
                        Capturado en el grupo #{{ scan.scan_group_id }}
                        <span v-if="scan.group?.empresa">({{ scan.group.empresa }})</span>
                    </p>
                </div>
            </div>

            <form class="space-y-6" @submit.prevent="submit">
                <div class="box p-5">
                    <h2 class="mb-4 flex items-center font-medium">
                        <Lucide icon="UserCheck" class="mr-2 h-4 w-4 text-primary" />
                        Información del contacto
                    </h2>
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 sm:col-span-6">
                            <FormLabel>Nombre *</FormLabel>
                            <FormInput v-model="form.nombre" type="text" required />
                            <FormHelp v-if="form.errors.nombre" class="text-danger">{{ form.errors.nombre }}</FormHelp>
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <FormLabel>Apellidos</FormLabel>
                            <FormInput v-model="form.apellidos" type="text" />
                            <FormHelp v-if="form.errors.apellidos" class="text-danger">{{ form.errors.apellidos }}</FormHelp>
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <FormLabel>Correo electrónico</FormLabel>
                            <FormInput v-model="form.correo" type="email" />
                            <FormHelp v-if="form.errors.correo" class="text-danger">{{ form.errors.correo }}</FormHelp>
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <FormLabel>Teléfono</FormLabel>
                            <FormInput v-model="form.telefono" type="text" />
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <FormLabel>Empresa</FormLabel>
                            <FormInput v-model="form.empresa" type="text" />
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <FormLabel>Estado</FormLabel>
                            <FormSelect v-model="form.estado">
                                <option value="">Seleccionar estado…</option>
                                <option v-for="estado in estadosMexico" :key="estado" :value="estado">{{ estado }}</option>
                            </FormSelect>
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <FormLabel>Puesto</FormLabel>
                            <FormInput v-model="form.puesto" type="text" />
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <FormLabel>Rol en la expo</FormLabel>
                            <FormInput v-model="form.rol" type="text" />
                        </div>
                    </div>
                </div>

                <div class="box p-5">
                    <h2 class="mb-4 flex items-center font-medium">
                        <Lucide icon="Tags" class="mr-2 h-4 w-4 text-primary" />
                        Marcas de interés
                    </h2>
                    <div v-if="marcas.length === 0" class="text-sm text-slate-400">No hay marcas registradas.</div>
                    <div v-else class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div
                            v-for="marca in marcas"
                            :key="marca.id"
                            class="rounded-lg border p-3 transition"
                            :class="selectedMarcas[marca.id].checked ? 'border-primary bg-primary/5' : 'border-slate-200 dark:border-darkmode-400'"
                        >
                            <label class="flex cursor-pointer items-center gap-3">
                                <FormCheck.Input v-model="selectedMarcas[marca.id].checked" type="checkbox" />
                                <img
                                    v-if="marca.imagen"
                                    :src="`/storage/${marca.imagen}`"
                                    :alt="marca.nombre"
                                    class="h-8 w-8 rounded object-cover"
                                />
                                <span class="font-medium">{{ marca.nombre }}</span>
                            </label>
                            <FormTextarea
                                v-if="selectedMarcas[marca.id].checked"
                                v-model="selectedMarcas[marca.id].comentarios"
                                class="mt-2"
                                rows="2"
                                placeholder="Comentarios sobre esta marca…"
                            />
                        </div>
                    </div>
                </div>

                <div class="box p-5">
                    <h2 class="mb-4 flex items-center font-medium">
                        <Lucide icon="FilePlus2" class="mr-2 h-4 w-4 text-primary" />
                        Datos adicionales
                    </h2>
                    <FormTextarea
                        v-model="form.campos_adicionales"
                        rows="4"
                        placeholder="Un dato por línea…"
                    />
                    <FormHelp>Escribe un dato por línea; se guardarán como campos adicionales.</FormHelp>
                </div>

                <div class="flex justify-end gap-3">
                    <Button :as="Link" variant="outline-secondary" :href="route('admin.usuarios-capturados.index')">
                        Cancelar
                    </Button>
                    <Button type="submit" variant="primary" :disabled="form.processing">
                        <Lucide icon="Save" class="mr-2 h-4 w-4" />
                        {{ form.processing ? 'Guardando…' : 'Guardar cambios' }}
                    </Button>
                </div>
            </form>
        </div>
    </RazeLayout>
</template>

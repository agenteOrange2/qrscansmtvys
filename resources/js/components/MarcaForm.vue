<script setup lang="ts">
import Button from '@/components/Base/Button';
import { FormHelp, FormInput, FormLabel, FormTextarea } from '@/components/Base/Form';
import Lucide from '@/components/Base/Lucide';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    marca?: {
        id: number;
        nombre: string;
        descripcion: string | null;
        imagen: string | null;
    };
}>();

const form = useForm({
    nombre: props.marca?.nombre ?? '',
    descripcion: props.marca?.descripcion ?? '',
    imagen: null as File | null,
});

const preview = ref<string | null>(props.marca?.imagen ? `/storage/${props.marca.imagen}` : null);

function onFileChange(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    form.imagen = file;
    preview.value = file ? URL.createObjectURL(file) : preview.value;
}

function submit(): void {
    if (props.marca) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(
            route('admin.marcas.update', props.marca.id),
            { forceFormData: true },
        );
    } else {
        form.post(route('admin.marcas.store'), { forceFormData: true });
    }
}
</script>

<template>
    <form class="space-y-6" @submit.prevent="submit">
        <div class="box p-5">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12 sm:col-span-6">
                    <FormLabel>Nombre *</FormLabel>
                    <FormInput v-model="form.nombre" type="text" required />
                    <FormHelp v-if="form.errors.nombre" class="text-danger">{{ form.errors.nombre }}</FormHelp>
                </div>
                <div class="col-span-12">
                    <FormLabel>Descripción</FormLabel>
                    <FormTextarea v-model="form.descripcion" rows="3" />
                    <FormHelp v-if="form.errors.descripcion" class="text-danger">{{ form.errors.descripcion }}</FormHelp>
                </div>
                <div class="col-span-12">
                    <FormLabel>Imagen</FormLabel>
                    <div class="flex items-center gap-4">
                        <img v-if="preview" :src="preview" class="h-16 w-16 rounded-lg object-cover" alt="Vista previa" />
                        <input
                            type="file"
                            accept="image/*"
                            class="text-sm file:mr-3 file:rounded-md file:border-0 file:bg-primary/10 file:px-3 file:py-1.5 file:text-primary hover:file:bg-primary/20"
                            @change="onFileChange"
                        />
                    </div>
                    <FormHelp v-if="form.errors.imagen" class="text-danger">{{ form.errors.imagen }}</FormHelp>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <Button :as="Link" variant="outline-secondary" :href="route('admin.marcas.index')">Cancelar</Button>
            <Button type="submit" variant="primary" :disabled="form.processing">
                <Lucide icon="Save" class="mr-2 h-4 w-4" />
                {{ form.processing ? 'Guardando…' : marca ? 'Guardar cambios' : 'Crear marca' }}
            </Button>
        </div>
    </form>
</template>

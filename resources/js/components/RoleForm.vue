<script setup lang="ts">
import Button from '@/components/Base/Button';
import { FormCheck, FormHelp, FormInput, FormLabel } from '@/components/Base/Form';
import Lucide from '@/components/Base/Lucide';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    role?: {
        id: number;
        name: string;
        permissions: string[];
    };
    permissions: string[];
}>();

const form = useForm({
    name: props.role?.name ?? '',
    permissions: [...(props.role?.permissions ?? [])] as string[],
});

function submit(): void {
    if (props.role) {
        form.put(route('admin.roles.update', props.role.id), { preserveScroll: true });
    } else {
        form.post(route('admin.roles.store'), { preserveScroll: true });
    }
}
</script>

<template>
    <form class="space-y-6" @submit.prevent="submit">
        <div class="box p-5">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12 sm:col-span-6">
                    <FormLabel>Nombre del rol *</FormLabel>
                    <FormInput v-model="form.name" type="text" required />
                    <FormHelp v-if="form.errors.name" class="text-danger">{{ form.errors.name }}</FormHelp>
                </div>
                <div class="col-span-12">
                    <FormLabel>Permisos</FormLabel>
                    <div class="mt-1 flex flex-wrap gap-3">
                        <label
                            v-for="permission in permissions"
                            :key="permission"
                            class="flex cursor-pointer items-center gap-2 rounded-lg border px-4 py-2.5 transition"
                            :class="form.permissions.includes(permission) ? 'border-primary bg-primary/5' : 'border-slate-200 dark:border-darkmode-400'"
                        >
                            <FormCheck.Input v-model="form.permissions" type="checkbox" :value="permission" />
                            <span class="text-sm font-medium">{{ permission }}</span>
                        </label>
                    </div>
                    <FormHelp v-if="form.errors.permissions" class="text-danger">{{ form.errors.permissions }}</FormHelp>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <Button :as="Link" variant="outline-secondary" :href="route('admin.roles.index')">Cancelar</Button>
            <Button type="submit" variant="primary" :disabled="form.processing">
                <Lucide icon="Save" class="mr-2 h-4 w-4" />
                {{ form.processing ? 'Guardando…' : role ? 'Guardar cambios' : 'Crear rol' }}
            </Button>
        </div>
    </form>
</template>

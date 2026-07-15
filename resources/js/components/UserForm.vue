<script setup lang="ts">
import Button from '@/components/Base/Button';
import { FormCheck, FormHelp, FormInput, FormLabel } from '@/components/Base/Form';
import Lucide from '@/components/Base/Lucide';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    user?: {
        id: number;
        name: string;
        last_name: string | null;
        email: string;
        phone: string | null;
        position: string | null;
        company: string | null;
        roles: string[];
    };
    roles: string[];
}>();

const form = useForm({
    name: props.user?.name ?? '',
    last_name: props.user?.last_name ?? '',
    email: props.user?.email ?? '',
    phone: props.user?.phone ?? '',
    position: props.user?.position ?? '',
    company: props.user?.company ?? '',
    password: '',
    password_confirmation: '',
    roles: [...(props.user?.roles ?? [])] as string[],
});

function submit(): void {
    if (props.user) {
        form.put(route('admin.users.update', props.user.id), { preserveScroll: true });
    } else {
        form.post(route('admin.users.store'), { preserveScroll: true });
    }
}
</script>

<template>
    <form class="space-y-6" @submit.prevent="submit">
        <div class="box p-5">
            <h2 class="mb-4 flex items-center font-medium">
                <Lucide icon="User" class="mr-2 h-4 w-4 text-primary" />
                Datos del usuario
            </h2>
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12 sm:col-span-6">
                    <FormLabel>Nombre *</FormLabel>
                    <FormInput v-model="form.name" type="text" required />
                    <FormHelp v-if="form.errors.name" class="text-danger">{{ form.errors.name }}</FormHelp>
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <FormLabel>Apellidos</FormLabel>
                    <FormInput v-model="form.last_name" type="text" />
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <FormLabel>Correo electrónico *</FormLabel>
                    <FormInput v-model="form.email" type="email" required />
                    <FormHelp v-if="form.errors.email" class="text-danger">{{ form.errors.email }}</FormHelp>
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <FormLabel>Teléfono</FormLabel>
                    <FormInput v-model="form.phone" type="text" />
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <FormLabel>Puesto</FormLabel>
                    <FormInput v-model="form.position" type="text" />
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <FormLabel>Empresa</FormLabel>
                    <FormInput v-model="form.company" type="text" />
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <FormLabel>{{ user ? 'Nueva contraseña (opcional)' : 'Contraseña *' }}</FormLabel>
                    <FormInput v-model="form.password" type="password" :required="!user" autocomplete="new-password" />
                    <FormHelp v-if="form.errors.password" class="text-danger">{{ form.errors.password }}</FormHelp>
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <FormLabel>Confirmar contraseña</FormLabel>
                    <FormInput v-model="form.password_confirmation" type="password" :required="!user || form.password !== ''" autocomplete="new-password" />
                </div>
            </div>
        </div>

        <div class="box p-5">
            <h2 class="mb-4 flex items-center font-medium">
                <Lucide icon="ShieldCheck" class="mr-2 h-4 w-4 text-primary" />
                Roles
            </h2>
            <div class="flex flex-wrap gap-4">
                <label
                    v-for="role in roles"
                    :key="role"
                    class="flex cursor-pointer items-center gap-2 rounded-lg border px-4 py-2.5 transition"
                    :class="form.roles.includes(role) ? 'border-primary bg-primary/5' : 'border-slate-200 dark:border-darkmode-400'"
                >
                    <FormCheck.Input v-model="form.roles" type="checkbox" :value="role" />
                    <span class="text-sm font-medium">{{ role }}</span>
                </label>
            </div>
            <FormHelp v-if="form.errors.roles" class="text-danger">{{ form.errors.roles }}</FormHelp>
        </div>

        <div class="flex justify-end gap-3">
            <Button :as="Link" variant="outline-secondary" :href="route('admin.users.index')">Cancelar</Button>
            <Button type="submit" variant="primary" :disabled="form.processing">
                <Lucide icon="Save" class="mr-2 h-4 w-4" />
                {{ form.processing ? 'Guardando…' : user ? 'Guardar cambios' : 'Crear usuario' }}
            </Button>
        </div>
    </form>
</template>

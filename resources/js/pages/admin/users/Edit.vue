<script setup lang="ts">
import UserForm from '@/components/UserForm.vue';
import RazeLayout from '@/layouts/RazeLayout.vue';

defineProps<{
    user: {
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
</script>

<template>
    <RazeLayout title="Editar usuario">
        <div class="mt-2 space-y-6">
            <h1 class="text-lg font-medium">Editar usuario</h1>
            <UserForm :user="user" :roles="roles" />
        </div>
    </RazeLayout>
</template>

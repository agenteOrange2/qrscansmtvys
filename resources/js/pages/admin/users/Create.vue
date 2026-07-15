<script setup lang="ts">
import UserForm from '@/components/UserForm.vue';
import RazeLayout from '@/layouts/RazeLayout.vue';

defineProps<{
    roles: string[];
}>();
</script>

<template>
    <RazeLayout title="Nuevo usuario">
        <div class="mt-2 space-y-6">
            <h1 class="text-lg font-medium">Nuevo usuario</h1>
            <UserForm :roles="roles" />
        </div>
    </RazeLayout>
</template>

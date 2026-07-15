<script setup lang="ts">
import RoleForm from '@/components/RoleForm.vue';
import RazeLayout from '@/layouts/RazeLayout.vue';

defineProps<{
    role: {
        id: number;
        name: string;
        permissions: string[];
    };
    permissions: string[];
}>();
</script>

<template>
    <RazeLayout title="Editar rol">
        <div class="mt-2 space-y-6">
            <h1 class="text-lg font-medium">Editar rol</h1>
            <RoleForm :role="role" :permissions="permissions" />
        </div>
    </RazeLayout>
</template>

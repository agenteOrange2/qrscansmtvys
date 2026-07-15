<script setup lang="ts">
import RoleForm from '@/components/RoleForm.vue';
import RazeLayout from '@/layouts/RazeLayout.vue';

defineProps<{
    permissions: string[];
}>();
</script>

<template>
    <RazeLayout title="Nuevo rol">
        <div class="mt-2 space-y-6">
            <h1 class="text-lg font-medium">Nuevo rol</h1>
            <RoleForm :permissions="permissions" />
        </div>
    </RazeLayout>
</template>

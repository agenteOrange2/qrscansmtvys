<script setup lang="ts">
import MarcaForm from '@/components/MarcaForm.vue';
import RazeLayout from '@/layouts/RazeLayout.vue';
</script>

<template>
    <RazeLayout title="Nueva marca">
        <div class="mt-2 space-y-6">
            <h1 class="text-lg font-medium">Nueva marca</h1>
            <MarcaForm />
        </div>
    </RazeLayout>
</template>

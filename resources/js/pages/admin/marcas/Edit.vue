<script setup lang="ts">
import MarcaForm from '@/components/MarcaForm.vue';
import RazeLayout from '@/layouts/RazeLayout.vue';

defineProps<{
    marca: {
        id: number;
        nombre: string;
        descripcion: string | null;
        imagen: string | null;
    };
}>();
</script>

<template>
    <RazeLayout title="Editar marca">
        <div class="mt-2 space-y-6">
            <h1 class="text-lg font-medium">Editar marca</h1>
            <MarcaForm :marca="marca" />
        </div>
    </RazeLayout>
</template>

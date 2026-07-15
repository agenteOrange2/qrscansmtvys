<script setup lang="ts">
import { FormCheck, FormInput, FormLabel, FormSelect, FormTextarea } from '@/components/Base/Form';
import Button from '@/components/Base/Button';
import { Dialog } from '@/components/Base/Headless';
import Lucide from '@/components/Base/Lucide';
import QrScanner from '@/components/QrScanner.vue';
import RazeLayout from '@/layouts/RazeLayout.vue';
import { estadosMexico } from '@/lib/estados';
import { notify } from '@/lib/notify';
import { emptyContact, parseQrData, type ScannedContact } from '@/lib/qr';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, reactive, ref } from 'vue';

interface Marca {
    id: number;
    nombre: string;
    descripcion: string | null;
    imagen: string | null;
}

const props = defineProps<{
    marcas: Marca[];
}>();

const GROUP_TARGET = 5;
const GROUP_MAX = 10;

const mode = ref<'individual' | 'grupo'>('individual');
const paused = ref(false);
const saving = ref(false);

// --- Modo individual ---
const contact = ref<ScannedContact>(emptyContact());
const showForm = ref(false);
const extraInfo = ref('');

// --- Modo grupal ---
const queue = ref<ScannedContact[]>([]);
const expandedIndex = ref<number | null>(null);
const empresaGrupo = ref('');
const unificarEmpresa = ref(true);
const estadoGrupo = ref('');
const notasGrupo = ref('');

// --- Marcas de interés (compartidas por ambos modos) ---
const selectedMarcas = reactive<Record<number, { checked: boolean; comentarios: string }>>(
    Object.fromEntries(props.marcas.map((marca) => [marca.id, { checked: false, comentarios: '' }])),
);

// --- Diálogos ---
const duplicateDialog = reactive({ open: false, existingScanId: null as number | null });
const groupResultDialog = reactive({
    open: false,
    saved: 0,
    duplicates: [] as Array<{ correo: string; motivo: string }>,
});

const groupProgress = computed(() => Math.min((queue.value.length / GROUP_TARGET) * 100, 100));

function switchMode(newMode: 'individual' | 'grupo'): void {
    mode.value = newMode;
    paused.value = false;
    showForm.value = false;
}

function onDetect(raw: string): void {
    const parsed = parseQrData(raw);

    if (!parsed.nombre && !parsed.correo) {
        notify('El código QR no contiene datos de contacto reconocibles.', 'warning');

        return;
    }

    if (mode.value === 'individual') {
        contact.value = parsed;
        showForm.value = true;
        paused.value = true;

        return;
    }

    // Modo grupal: acumular y seguir escaneando
    if (queue.value.length >= GROUP_MAX) {
        notify(`Máximo ${GROUP_MAX} contactos por grupo. Guarda el grupo actual primero.`, 'warning');

        return;
    }

    const correo = parsed.correo.toLowerCase();

    if (correo && queue.value.some((item) => item.correo.toLowerCase() === correo)) {
        notify(`${parsed.nombre || parsed.correo} ya está en el grupo.`, 'warning');

        return;
    }

    queue.value.push(parsed);

    if (!empresaGrupo.value && parsed.empresa) {
        empresaGrupo.value = parsed.empresa;
    }

    notify(
        `${parsed.nombre || 'Contacto'} agregado (${queue.value.length}/${GROUP_TARGET})`,
        queue.value.length >= GROUP_TARGET ? 'info' : 'success',
        2500,
    );
}

function addManual(): void {
    if (mode.value === 'individual') {
        contact.value = emptyContact();
        showForm.value = true;
        paused.value = true;

        return;
    }

    if (queue.value.length >= GROUP_MAX) return;

    queue.value.push(emptyContact());
    expandedIndex.value = queue.value.length - 1;
}

function removeFromQueue(index: number): void {
    queue.value.splice(index, 1);

    if (expandedIndex.value === index) expandedIndex.value = null;
}

function buildMarcasPayload(): Array<{ id: number; comentarios: string | null }> {
    return Object.entries(selectedMarcas)
        .filter(([, value]) => value.checked)
        .map(([id, value]) => ({ id: Number(id), comentarios: value.comentarios || null }));
}

function resetMarcas(): void {
    for (const value of Object.values(selectedMarcas)) {
        value.checked = false;
        value.comentarios = '';
    }
}

function resetIndividual(): void {
    contact.value = emptyContact();
    extraInfo.value = '';
    showForm.value = false;
    resetMarcas();
    paused.value = false;
}

function resetGroup(): void {
    queue.value = [];
    expandedIndex.value = null;
    empresaGrupo.value = '';
    estadoGrupo.value = '';
    notasGrupo.value = '';
    resetMarcas();
    paused.value = false;
}

function firstValidationError(errors: unknown): string {
    if (errors && typeof errors === 'object') {
        const first = Object.values(errors as Record<string, string[]>)[0];

        if (Array.isArray(first) && first.length > 0) return first[0];
    }

    return 'Los datos enviados no son válidos.';
}

async function submitIndividual(): Promise<void> {
    if (!contact.value.nombre.trim()) {
        notify('El nombre es obligatorio.', 'warning');

        return;
    }

    saving.value = true;

    try {
        const camposAdicionales = [...contact.value.campos_adicionales];

        if (extraInfo.value.trim()) camposAdicionales.push(extraInfo.value.trim());

        await axios.post(route('admin.scan.store'), {
            ...contact.value,
            campos_adicionales: camposAdicionales,
            marcas: buildMarcasPayload(),
        });

        notify('Escaneo guardado exitosamente.');
        resetIndividual();
    } catch (error) {
        if (axios.isAxiosError(error) && error.response?.status === 409) {
            duplicateDialog.existingScanId = error.response.data.existingScanId ?? null;
            duplicateDialog.open = true;
        } else if (axios.isAxiosError(error) && error.response?.status === 422) {
            notify(firstValidationError(error.response.data.errors), 'error');
        } else {
            notify('Error al guardar el escaneo. Intenta de nuevo.', 'error');
        }
    } finally {
        saving.value = false;
    }
}

async function submitGroup(): Promise<void> {
    if (queue.value.length === 0) {
        notify('Escanea al menos un contacto antes de guardar el grupo.', 'warning');

        return;
    }

    if (queue.value.some((item) => !item.nombre.trim())) {
        notify('Todos los contactos del grupo deben tener nombre.', 'warning');

        return;
    }

    saving.value = true;

    try {
        const { data } = await axios.post(route('admin.scan.store-group'), {
            empresa: empresaGrupo.value || null,
            notas: notasGrupo.value || null,
            scans: queue.value.map((item) => ({
                ...item,
                empresa: unificarEmpresa.value ? empresaGrupo.value || item.empresa : item.empresa,
                estado: estadoGrupo.value || item.estado,
            })),
            marcas: buildMarcasPayload(),
        });

        notify(data.message);

        if (Array.isArray(data.duplicates) && data.duplicates.length > 0) {
            groupResultDialog.saved = data.saved ?? 0;
            groupResultDialog.duplicates = data.duplicates;
            groupResultDialog.open = true;
        }

        resetGroup();
    } catch (error) {
        if (axios.isAxiosError(error) && error.response?.status === 409) {
            groupResultDialog.saved = 0;
            groupResultDialog.duplicates = error.response.data.duplicates ?? [];
            groupResultDialog.open = true;
        } else if (axios.isAxiosError(error) && error.response?.status === 422) {
            notify(firstValidationError(error.response.data.errors), 'error');
        } else {
            notify('Error al guardar el grupo. Intenta de nuevo.', 'error');
        }
    } finally {
        saving.value = false;
    }
}

function scanAnother(): void {
    duplicateDialog.open = false;
    resetIndividual();
}
</script>

<template>
    <RazeLayout title="Escanear QR">
        <div class="mt-2 space-y-6">
            <!-- Encabezado + selector de modo -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-lg font-medium">Escáner de gafetes</h1>
                    <p class="text-sm text-slate-500">
                        {{
                            mode === 'individual'
                                ? 'Escanea un gafete y completa la información antes de guardar.'
                                : `Escanea hasta ${GROUP_MAX} gafetes de la misma empresa y guárdalos en una sola operación.`
                        }}
                    </p>
                </div>
                <div class="flex rounded-lg border border-slate-200 bg-white p-1 shadow-sm dark:border-darkmode-400 dark:bg-darkmode-600">
                    <button
                        type="button"
                        class="flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium transition"
                        :class="mode === 'individual' ? 'bg-primary text-white shadow' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                        @click="switchMode('individual')"
                    >
                        <Lucide icon="User" class="h-4 w-4" />
                        Individual
                    </button>
                    <button
                        type="button"
                        class="flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium transition"
                        :class="mode === 'grupo' ? 'bg-primary text-white shadow' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                        @click="switchMode('grupo')"
                    >
                        <Lucide icon="Users" class="h-4 w-4" />
                        Grupal
                        <span
                            v-if="queue.length > 0"
                            class="rounded-full bg-white/20 px-1.5 text-xs"
                            :class="mode !== 'grupo' && 'bg-primary/10 text-primary'"
                        >
                            {{ queue.length }}
                        </span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-12 gap-6">
                <!-- Columna: cámara -->
                <div class="col-span-12 xl:col-span-5">
                    <div class="box p-5">
                        <QrScanner :paused="paused" @detect="onDetect" />

                        <!-- Progreso del grupo -->
                        <div v-if="mode === 'grupo'" class="mx-auto mt-4 w-full max-w-md">
                            <div class="mb-1.5 flex items-center justify-between text-sm">
                                <span class="font-medium">Contactos del grupo</span>
                                <span :class="queue.length >= GROUP_TARGET ? 'font-semibold text-success' : 'text-slate-500'">
                                    {{ queue.length }} / {{ GROUP_TARGET }}
                                </span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-slate-200 dark:bg-darkmode-400">
                                <div
                                    class="h-full rounded-full bg-success transition-all duration-500"
                                    :style="{ width: `${groupProgress}%` }"
                                ></div>
                            </div>
                        </div>

                        <div class="mx-auto mt-4 flex w-full max-w-md gap-2">
                            <Button variant="outline-secondary" class="flex-1" @click="paused = !paused">
                                <Lucide :icon="paused ? 'Play' : 'Pause'" class="mr-2 h-4 w-4" />
                                {{ paused ? 'Reanudar' : 'Pausar' }}
                            </Button>
                            <Button variant="outline-primary" class="flex-1" @click="addManual">
                                <Lucide icon="PenLine" class="mr-2 h-4 w-4" />
                                Captura manual
                            </Button>
                        </div>
                    </div>

                    <!-- Lista del grupo -->
                    <div v-if="mode === 'grupo'" class="box mt-6 p-5">
                        <div class="mb-3 flex items-center justify-between">
                            <h2 class="font-medium">Contactos escaneados</h2>
                            <button
                                v-if="queue.length > 0"
                                type="button"
                                class="text-xs text-danger hover:underline"
                                @click="resetGroup"
                            >
                                Vaciar grupo
                            </button>
                        </div>

                        <div v-if="queue.length === 0" class="py-8 text-center text-sm text-slate-400">
                            <Lucide icon="ScanLine" class="mx-auto mb-2 h-8 w-8" />
                            Aún no hay contactos. Escanea el primer gafete.
                        </div>

                        <TransitionGroup v-else tag="ul" class="space-y-2" enter-active-class="transition duration-300" enter-from-class="opacity-0 -translate-y-2">
                            <li
                                v-for="(item, index) in queue"
                                :key="index"
                                class="rounded-lg border border-slate-200 dark:border-darkmode-400"
                            >
                                <div class="flex items-center gap-3 p-3">
                                    <div class="flex h-9 w-9 flex-none items-center justify-center rounded-full bg-primary/10 text-sm font-semibold text-primary">
                                        {{ index + 1 }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="truncate font-medium">
                                            {{ item.nombre || 'Sin nombre' }} {{ item.apellidos }}
                                        </div>
                                        <div class="truncate text-xs text-slate-500">
                                            {{ item.correo || 'sin correo' }} · {{ item.puesto || 'sin puesto' }}
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        class="p-1.5 text-slate-400 hover:text-primary"
                                        @click="expandedIndex = expandedIndex === index ? null : index"
                                    >
                                        <Lucide :icon="expandedIndex === index ? 'ChevronUp' : 'Pencil'" class="h-4 w-4" />
                                    </button>
                                    <button type="button" class="p-1.5 text-slate-400 hover:text-danger" @click="removeFromQueue(index)">
                                        <Lucide icon="Trash2" class="h-4 w-4" />
                                    </button>
                                </div>

                                <!-- Edición del contacto -->
                                <div v-if="expandedIndex === index" class="grid grid-cols-2 gap-3 border-t border-slate-200 p-3 dark:border-darkmode-400">
                                    <div>
                                        <FormLabel class="text-xs">Nombre *</FormLabel>
                                        <FormInput v-model="item.nombre" formInputSize="sm" type="text" />
                                    </div>
                                    <div>
                                        <FormLabel class="text-xs">Apellidos</FormLabel>
                                        <FormInput v-model="item.apellidos" formInputSize="sm" type="text" />
                                    </div>
                                    <div>
                                        <FormLabel class="text-xs">Correo</FormLabel>
                                        <FormInput v-model="item.correo" formInputSize="sm" type="email" />
                                    </div>
                                    <div>
                                        <FormLabel class="text-xs">Teléfono</FormLabel>
                                        <FormInput v-model="item.telefono" formInputSize="sm" type="text" />
                                    </div>
                                    <div>
                                        <FormLabel class="text-xs">Puesto</FormLabel>
                                        <FormInput v-model="item.puesto" formInputSize="sm" type="text" />
                                    </div>
                                    <div>
                                        <FormLabel class="text-xs">Rol en expo</FormLabel>
                                        <FormInput v-model="item.rol" formInputSize="sm" type="text" />
                                    </div>
                                </div>
                            </li>
                        </TransitionGroup>
                    </div>
                </div>

                <!-- Columna: formulario / datos compartidos -->
                <div class="col-span-12 xl:col-span-7">
                    <!-- MODO INDIVIDUAL -->
                    <template v-if="mode === 'individual'">
                        <div v-if="!showForm" class="box flex flex-col items-center justify-center p-12 text-center text-slate-400">
                            <Lucide icon="QrCode" class="mb-3 h-14 w-14" />
                            <p class="font-medium text-slate-500 dark:text-slate-400">Esperando escaneo…</p>
                            <p class="mt-1 text-sm">Apunta la cámara al código QR del gafete para capturar los datos.</p>
                        </div>

                        <form v-else class="space-y-6" @submit.prevent="submitIndividual">
                            <div class="box p-5">
                                <h2 class="mb-4 flex items-center font-medium">
                                    <Lucide icon="UserCheck" class="mr-2 h-4 w-4 text-primary" />
                                    Información del contacto
                                </h2>
                                <div class="grid grid-cols-12 gap-4">
                                    <div class="col-span-12 sm:col-span-6">
                                        <FormLabel>Nombre *</FormLabel>
                                        <FormInput v-model="contact.nombre" type="text" required />
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <FormLabel>Apellidos</FormLabel>
                                        <FormInput v-model="contact.apellidos" type="text" />
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <FormLabel>Correo electrónico</FormLabel>
                                        <FormInput v-model="contact.correo" type="email" />
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <FormLabel>Teléfono</FormLabel>
                                        <FormInput v-model="contact.telefono" type="text" />
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <FormLabel>Empresa</FormLabel>
                                        <FormInput v-model="contact.empresa" type="text" />
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <FormLabel>Estado</FormLabel>
                                        <FormSelect v-model="contact.estado">
                                            <option value="">Seleccionar estado…</option>
                                            <option v-for="estado in estadosMexico" :key="estado" :value="estado">{{ estado }}</option>
                                        </FormSelect>
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <FormLabel>Puesto</FormLabel>
                                        <FormInput v-model="contact.puesto" type="text" />
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <FormLabel>Rol en la expo</FormLabel>
                                        <FormInput v-model="contact.rol" type="text" />
                                    </div>
                                </div>
                            </div>

                            <!-- Marcas -->
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
                                            <FormCheck.Input
                                                v-model="selectedMarcas[marca.id].checked"
                                                type="checkbox"
                                            />
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

                            <!-- Datos adicionales -->
                            <div class="box p-5">
                                <h2 class="mb-4 flex items-center font-medium">
                                    <Lucide icon="FilePlus2" class="mr-2 h-4 w-4 text-primary" />
                                    Datos adicionales
                                </h2>
                                <div
                                    v-if="contact.campos_adicionales.length > 0"
                                    class="mb-3 flex flex-wrap gap-2"
                                >
                                    <span
                                        v-for="(campo, index) in contact.campos_adicionales"
                                        :key="index"
                                        class="rounded-full bg-slate-100 px-3 py-1 text-xs dark:bg-darkmode-400"
                                    >
                                        {{ campo }}
                                    </span>
                                </div>
                                <FormTextarea
                                    v-model="extraInfo"
                                    rows="3"
                                    placeholder="Notas o información extra sobre este contacto…"
                                />
                            </div>

                            <div class="flex justify-end gap-3">
                                <Button type="button" variant="outline-secondary" @click="resetIndividual">
                                    Cancelar
                                </Button>
                                <Button type="submit" variant="primary" :disabled="saving">
                                    <Lucide icon="Send" class="mr-2 h-4 w-4" />
                                    {{ saving ? 'Guardando…' : 'Enviar información' }}
                                </Button>
                            </div>
                        </form>
                    </template>

                    <!-- MODO GRUPAL -->
                    <template v-else>
                        <form class="space-y-6" @submit.prevent="submitGroup">
                            <div class="box p-5">
                                <h2 class="mb-4 flex items-center font-medium">
                                    <Lucide icon="Building2" class="mr-2 h-4 w-4 text-primary" />
                                    Datos compartidos del grupo
                                </h2>
                                <div class="grid grid-cols-12 gap-4">
                                    <div class="col-span-12 sm:col-span-6">
                                        <FormLabel>Empresa</FormLabel>
                                        <FormInput
                                            v-model="empresaGrupo"
                                            type="text"
                                            placeholder="Se llena con el primer gafete escaneado"
                                        />
                                        <label class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                                            <FormCheck.Input v-model="unificarEmpresa" type="checkbox" />
                                            Aplicar esta empresa a todos los contactos del grupo
                                        </label>
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <FormLabel>Estado (aplicado a todos)</FormLabel>
                                        <FormSelect v-model="estadoGrupo">
                                            <option value="">Seleccionar estado…</option>
                                            <option v-for="estado in estadosMexico" :key="estado" :value="estado">{{ estado }}</option>
                                        </FormSelect>
                                    </div>
                                    <div class="col-span-12">
                                        <FormLabel>Notas del grupo</FormLabel>
                                        <FormTextarea
                                            v-model="notasGrupo"
                                            rows="2"
                                            placeholder="Observaciones sobre esta visita o empresa…"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Marcas compartidas -->
                            <div class="box p-5">
                                <h2 class="mb-1 flex items-center font-medium">
                                    <Lucide icon="Tags" class="mr-2 h-4 w-4 text-primary" />
                                    Marcas de interés
                                </h2>
                                <p class="mb-4 text-xs text-slate-500">Se asociarán a todos los contactos del grupo.</p>
                                <div v-if="marcas.length === 0" class="text-sm text-slate-400">No hay marcas registradas.</div>
                                <div v-else class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div
                                        v-for="marca in marcas"
                                        :key="marca.id"
                                        class="rounded-lg border p-3 transition"
                                        :class="selectedMarcas[marca.id].checked ? 'border-primary bg-primary/5' : 'border-slate-200 dark:border-darkmode-400'"
                                    >
                                        <label class="flex cursor-pointer items-center gap-3">
                                            <FormCheck.Input
                                                v-model="selectedMarcas[marca.id].checked"
                                                type="checkbox"
                                            />
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

                            <div class="flex items-center justify-end gap-3">
                                <span v-if="queue.length > 0" class="mr-auto text-sm text-slate-500">
                                    {{ queue.length }} contacto(s) listos para guardar
                                </span>
                                <Button type="submit" variant="primary" :disabled="saving || queue.length === 0">
                                    <Lucide icon="Save" class="mr-2 h-4 w-4" />
                                    {{ saving ? 'Guardando…' : `Guardar grupo (${queue.length})` }}
                                </Button>
                            </div>
                        </form>
                    </template>
                </div>
            </div>
        </div>

        <!-- Diálogo: contacto duplicado (individual) -->
        <Dialog :open="duplicateDialog.open" @close="duplicateDialog.open = false">
            <Dialog.Panel class="p-6 text-center">
                <Lucide icon="UserX" class="mx-auto mb-3 h-14 w-14 text-warning" />
                <div class="text-lg font-medium">Contacto existente</div>
                <p class="mt-2 text-sm text-slate-500">
                    Este contacto ya fue registrado previamente en el sistema.
                </p>
                <div class="mt-5 flex justify-center gap-3">
                    <Button
                        v-if="duplicateDialog.existingScanId"
                        :as="Link"
                        variant="outline-primary"
                        :href="route('admin.usuarios-capturados.edit', duplicateDialog.existingScanId)"
                    >
                        Ver registro
                    </Button>
                    <Button variant="primary" @click="scanAnother">Escanear otro</Button>
                </div>
            </Dialog.Panel>
        </Dialog>

        <!-- Diálogo: resultado del grupo con duplicados -->
        <Dialog :open="groupResultDialog.open" @close="groupResultDialog.open = false">
            <Dialog.Panel class="p-6">
                <div class="text-center">
                    <Lucide
                        :icon="groupResultDialog.saved > 0 ? 'CheckCircle2' : 'AlertTriangle'"
                        class="mx-auto mb-3 h-14 w-14"
                        :class="groupResultDialog.saved > 0 ? 'text-success' : 'text-warning'"
                    />
                    <div class="text-lg font-medium">
                        {{
                            groupResultDialog.saved > 0
                                ? `${groupResultDialog.saved} contacto(s) guardados`
                                : 'No se guardó ningún contacto'
                        }}
                    </div>
                    <p class="mt-1 text-sm text-slate-500">Los siguientes contactos no se registraron:</p>
                </div>
                <ul class="mt-4 space-y-2">
                    <li
                        v-for="(duplicate, index) in groupResultDialog.duplicates"
                        :key="index"
                        class="flex items-center gap-2 rounded-lg bg-warning/10 px-3 py-2 text-sm"
                    >
                        <Lucide icon="AlertCircle" class="h-4 w-4 flex-none text-warning" />
                        <span class="truncate">{{ duplicate.correo || 'Sin correo' }}</span>
                        <span class="ml-auto flex-none text-xs text-slate-500">{{ duplicate.motivo }}</span>
                    </li>
                </ul>
                <div class="mt-5 text-center">
                    <Button variant="primary" @click="groupResultDialog.open = false">Entendido</Button>
                </div>
            </Dialog.Panel>
        </Dialog>
    </RazeLayout>
</template>

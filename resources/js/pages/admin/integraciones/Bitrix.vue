<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';
import Button from '@/components/Base/Button';
import {
    FormHelp,
    FormInput,
    FormLabel,
    FormSelect,
    FormSwitch,
} from '@/components/Base/Form';
import Lucide from '@/components/Base/Lucide';
import RazeLayout from '@/layouts/RazeLayout.vue';

interface Option {
    id: number | string;
    name: string;
}

interface FailedScan {
    id: number;
    nombre: string;
    apellidos: string | null;
    correo: string | null;
    bitrix_error: string | null;
    bitrix_attempts: number;
    updated_at: string;
}

const props = defineProps<{
    settings: {
        enabled: boolean;
        webhook: string;
        category_id: string;
        stage_id: string;
        responsible_id: string;
    };
    stats: {
        enviados: number;
        pendientes: number;
        fallidos: number;
        sinEnviar: number;
    };
    fallidos: FailedScan[];
}>();

const form = useForm({
    enabled: props.settings.enabled,
    webhook: props.settings.webhook ?? '',
    category_id: props.settings.category_id ?? '13',
    stage_id: props.settings.stage_id ?? '',
    responsible_id: props.settings.responsible_id ?? '',
});

function submit(): void {
    form.put(route('admin.integraciones.bitrix.update'), { preserveScroll: true });
}

function extractError(error: unknown): string {
    if (axios.isAxiosError(error)) {
        const data = error.response?.data as
            | { error?: string; message?: string }
            | undefined;

        return data?.error ?? data?.message ?? 'Error de conexión con el servidor.';
    }

    return 'Error inesperado.';
}

// ── Probar conexión ──
const testing = ref(false);
const testResult = ref<{ ok: boolean; message: string } | null>(null);

async function testConnection(): Promise<void> {
    testing.value = true;
    testResult.value = null;

    try {
        const { data } = await axios.post(route('admin.integraciones.bitrix.test'), {
            webhook: form.webhook,
        });
        testResult.value = {
            ok: true,
            message: `Conexión exitosa — webhook de ${data.user}.`,
        };
    } catch (error) {
        testResult.value = { ok: false, message: extractError(error) };
    } finally {
        testing.value = false;
    }
}

// ── Pipelines y etapas ──
const pipelines = ref<Option[] | null>(null);
const stages = ref<Option[] | null>(null);
const loadingPipelines = ref(false);
const loadingStages = ref(false);
const catalogError = ref<string | null>(null);

async function loadPipelines(): Promise<void> {
    loadingPipelines.value = true;
    catalogError.value = null;

    try {
        const { data } = await axios.post(route('admin.integraciones.bitrix.pipelines'), {
            webhook: form.webhook,
        });
        pipelines.value = data.categories;
    } catch (error) {
        catalogError.value = extractError(error);
    } finally {
        loadingPipelines.value = false;
    }
}

async function loadStages(): Promise<void> {
    loadingStages.value = true;
    catalogError.value = null;

    try {
        const { data } = await axios.post(route('admin.integraciones.bitrix.stages'), {
            webhook: form.webhook,
            category_id: form.category_id,
        });
        stages.value = data.stages;
    } catch (error) {
        catalogError.value = extractError(error);
    } finally {
        loadingStages.value = false;
    }
}

// ── Sincronizar pendientes ──
const syncing = ref(false);
const syncResult = ref<{ ok: boolean; message: string } | null>(null);

async function syncPending(): Promise<void> {
    syncing.value = true;
    syncResult.value = null;

    try {
        const { data } = await axios.post(route('admin.integraciones.bitrix.sync'));
        syncResult.value = {
            ok: true,
            message: `${data.queued} escaneo(s) puestos en cola para enviarse a Bitrix24.`,
        };
    } catch (error) {
        syncResult.value = { ok: false, message: extractError(error) };
    } finally {
        syncing.value = false;
    }
}

function formatDate(value: string): string {
    return new Date(value).toLocaleString();
}
</script>

<template>
    <RazeLayout title="Integración Bitrix24">
        <div class="mt-2 space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-lg font-medium">Integración Bitrix24 CRM</h1>
                <a
                    href="https://smtvys.bitrix24.com/crm/deal/kanban/category/13/"
                    target="_blank"
                    rel="noopener"
                    class="flex items-center text-sm text-primary hover:underline"
                >
                    <Lucide icon="ExternalLink" class="mr-1 h-4 w-4" />
                    Abrir kanban de deals
                </a>
            </div>

            <form class="space-y-6" @submit.prevent="submit">
                <!-- ── Estado ── -->
                <div class="box space-y-5 p-5">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h2 class="font-medium">Estado de la integración</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Al activarla, cada escaneo individual crea un <strong>deal</strong> en
                                Bitrix24 y cada grupo crea <strong>un solo deal</strong> con todos sus
                                contactos (deduplicados por correo/teléfono). El envío ocurre justo
                                después de guardar — no requiere procesos en segundo plano. Si alguien
                                ya registrado se vuelve a escanear, se envía un nuevo deal con su
                                información.
                            </p>
                        </div>
                        <FormSwitch>
                            <FormSwitch.Input
                                id="bitrix-enabled"
                                v-model="form.enabled"
                                type="checkbox"
                            />
                            <FormSwitch.Label htmlFor="bitrix-enabled">
                                {{ form.enabled ? 'Habilitada' : 'Deshabilitada' }}
                            </FormSwitch.Label>
                        </FormSwitch>
                    </div>
                    <FormHelp v-if="form.errors.enabled" class="text-danger">
                        {{ form.errors.enabled }}
                    </FormHelp>

                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                        <div class="rounded-lg border border-slate-200/60 p-4 text-center dark:border-darkmode-400">
                            <div class="text-2xl font-semibold text-success">{{ props.stats.enviados }}</div>
                            <div class="mt-1 text-xs text-slate-500">Enviados</div>
                        </div>
                        <div class="rounded-lg border border-slate-200/60 p-4 text-center dark:border-darkmode-400">
                            <div class="text-2xl font-semibold text-warning">{{ props.stats.pendientes }}</div>
                            <div class="mt-1 text-xs text-slate-500">En cola</div>
                        </div>
                        <div class="rounded-lg border border-slate-200/60 p-4 text-center dark:border-darkmode-400">
                            <div class="text-2xl font-semibold text-danger">{{ props.stats.fallidos }}</div>
                            <div class="mt-1 text-xs text-slate-500">Fallidos</div>
                        </div>
                        <div class="rounded-lg border border-slate-200/60 p-4 text-center dark:border-darkmode-400">
                            <div class="text-2xl font-semibold">{{ props.stats.sinEnviar }}</div>
                            <div class="mt-1 text-xs text-slate-500">Sin enviar</div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <Button
                            type="button"
                            variant="outline-primary"
                            :disabled="syncing || (props.stats.fallidos === 0 && props.stats.sinEnviar === 0)"
                            @click="syncPending"
                        >
                            <Lucide icon="RefreshCw" class="mr-2 h-4 w-4" />
                            {{ syncing ? 'Sincronizando…' : 'Enviar fallidos y sin enviar' }}
                        </Button>
                        <span
                            v-if="syncResult"
                            class="text-sm"
                            :class="syncResult.ok ? 'text-success' : 'text-danger'"
                        >
                            {{ syncResult.message }}
                        </span>
                    </div>
                </div>

                <!-- ── Conexión ── -->
                <div class="box space-y-4 p-5">
                    <h2 class="font-medium">Conexión</h2>
                    <div>
                        <FormLabel htmlFor="bitrix-webhook">Webhook entrante *</FormLabel>
                        <FormInput
                            id="bitrix-webhook"
                            v-model="form.webhook"
                            type="url"
                            placeholder="https://smtvys.bitrix24.com/rest/1/xxxxxxxx/"
                        />
                        <FormHelp v-if="form.errors.webhook" class="text-danger">
                            {{ form.errors.webhook }}
                        </FormHelp>
                        <FormHelp v-else>
                            Bitrix24 → Aplicaciones → Webhook entrante, con permiso
                            <code>CRM</code>. La URL termina en <code>/rest/&lt;usuario&gt;/&lt;token&gt;/</code>.
                        </FormHelp>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <Button
                            type="button"
                            variant="outline-secondary"
                            :disabled="testing || !form.webhook"
                            @click="testConnection"
                        >
                            <Lucide icon="PlugZap" class="mr-2 h-4 w-4" />
                            {{ testing ? 'Probando…' : 'Probar conexión' }}
                        </Button>
                        <span
                            v-if="testResult"
                            class="flex items-center text-sm"
                            :class="testResult.ok ? 'text-success' : 'text-danger'"
                        >
                            <Lucide
                                :icon="testResult.ok ? 'CheckCircle2' : 'XCircle'"
                                class="mr-1 h-4 w-4"
                            />
                            {{ testResult.message }}
                        </span>
                    </div>
                </div>

                <!-- ── Destino del deal ── -->
                <div class="box space-y-4 p-5">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="font-medium">Destino del deal</h2>
                        <div class="flex gap-2">
                            <Button
                                type="button"
                                variant="outline-secondary"
                                size="sm"
                                :disabled="loadingPipelines || !form.webhook"
                                @click="loadPipelines"
                            >
                                <Lucide icon="Download" class="mr-1.5 h-3.5 w-3.5" />
                                {{ loadingPipelines ? 'Cargando…' : 'Cargar pipelines' }}
                            </Button>
                            <Button
                                type="button"
                                variant="outline-secondary"
                                size="sm"
                                :disabled="loadingStages || !form.webhook || form.category_id === ''"
                                @click="loadStages"
                            >
                                <Lucide icon="Download" class="mr-1.5 h-3.5 w-3.5" />
                                {{ loadingStages ? 'Cargando…' : 'Cargar etapas' }}
                            </Button>
                        </div>
                    </div>
                    <FormHelp v-if="catalogError" class="text-danger">{{ catalogError }}</FormHelp>

                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 sm:col-span-4">
                            <FormLabel htmlFor="bitrix-category">Pipeline (CATEGORY_ID)</FormLabel>
                            <FormSelect v-if="pipelines" id="bitrix-category" v-model="form.category_id">
                                <option v-for="pipeline in pipelines" :key="pipeline.id" :value="String(pipeline.id)">
                                    {{ pipeline.name }} (ID {{ pipeline.id }})
                                </option>
                            </FormSelect>
                            <FormInput
                                v-else
                                id="bitrix-category"
                                v-model="form.category_id"
                                type="number"
                                min="0"
                                placeholder="13"
                            />
                            <FormHelp v-if="form.errors.category_id" class="text-danger">
                                {{ form.errors.category_id }}
                            </FormHelp>
                            <FormHelp v-else>
                                El kanban <code>/category/13/</code> corresponde al pipeline <code>13</code>.
                            </FormHelp>
                        </div>
                        <div class="col-span-12 sm:col-span-4">
                            <FormLabel htmlFor="bitrix-stage">Etapa inicial (opcional)</FormLabel>
                            <FormSelect v-if="stages" id="bitrix-stage" v-model="form.stage_id">
                                <option value="">— Primera etapa del pipeline —</option>
                                <option v-for="stage in stages" :key="stage.id" :value="String(stage.id)">
                                    {{ stage.name }} ({{ stage.id }})
                                </option>
                            </FormSelect>
                            <FormInput
                                v-else
                                id="bitrix-stage"
                                v-model="form.stage_id"
                                type="text"
                                placeholder="p. ej. C13:NEW"
                            />
                            <FormHelp v-if="form.errors.stage_id" class="text-danger">
                                {{ form.errors.stage_id }}
                            </FormHelp>
                            <FormHelp v-else>Vacío = primera etapa del pipeline.</FormHelp>
                        </div>
                        <div class="col-span-12 sm:col-span-4">
                            <FormLabel htmlFor="bitrix-responsible">Responsable (opcional)</FormLabel>
                            <FormInput
                                id="bitrix-responsible"
                                v-model="form.responsible_id"
                                type="number"
                                min="1"
                                placeholder="ID de usuario en Bitrix24"
                            />
                            <FormHelp v-if="form.errors.responsible_id" class="text-danger">
                                {{ form.errors.responsible_id }}
                            </FormHelp>
                            <FormHelp v-else>Vacío = responsable por defecto del portal.</FormHelp>
                        </div>
                    </div>
                </div>

                <!-- ── Guardar ── -->
                <div class="flex items-center justify-end gap-3">
                    <span v-if="form.recentlySuccessful" class="flex items-center text-sm text-success">
                        <Lucide icon="CheckCircle2" class="mr-1 h-4 w-4" />
                        Configuración guardada.
                    </span>
                    <Button type="submit" variant="primary" :disabled="form.processing">
                        <Lucide icon="Save" class="mr-2 h-4 w-4" />
                        {{ form.processing ? 'Guardando…' : 'Guardar configuración' }}
                    </Button>
                </div>
            </form>

            <!-- ── Últimos fallidos ── -->
            <div v-if="props.fallidos.length" class="box p-5">
                <h2 class="font-medium">Últimos envíos fallidos</h2>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-200/60 text-xs uppercase text-slate-500 dark:border-darkmode-400">
                                <th class="py-2 pr-4">ID</th>
                                <th class="py-2 pr-4">Nombre</th>
                                <th class="py-2 pr-4">Correo</th>
                                <th class="py-2 pr-4">Error</th>
                                <th class="py-2 pr-4">Intentos</th>
                                <th class="py-2">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="scan in props.fallidos"
                                :key="scan.id"
                                class="border-b border-slate-200/60 last:border-0 dark:border-darkmode-400"
                            >
                                <td class="py-2 pr-4">{{ scan.id }}</td>
                                <td class="py-2 pr-4">{{ scan.nombre }} {{ scan.apellidos ?? '' }}</td>
                                <td class="py-2 pr-4">{{ scan.correo ?? '—' }}</td>
                                <td class="max-w-md py-2 pr-4 text-danger">{{ scan.bitrix_error ?? '—' }}</td>
                                <td class="py-2 pr-4">{{ scan.bitrix_attempts }}</td>
                                <td class="py-2">{{ formatDate(scan.updated_at) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="mt-3 text-xs text-slate-500">
                    Usa «Enviar fallidos y sin enviar» para reintentarlos una vez corregida la configuración.
                </p>
            </div>
        </div>
    </RazeLayout>
</template>

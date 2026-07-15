<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Button from '@/components/Base/Button';
import { FormHelp, FormInput, FormLabel, FormTextarea } from '@/components/Base/Form';
import Lucide from '@/components/Base/Lucide';
import SettingsNav from '@/components/SettingsNav.vue';
import RazeLayout from '@/layouts/RazeLayout.vue';

interface BrandingSettings {
    app_name: string;
    login_title: string;
    login_subtitle: string;
    hero_title: string;
    hero_subtitle: string;
    logo_url: string | null;
    icon_url: string | null;
    favicon_url: string | null;
}

const props = defineProps<{
    brandingSettings: BrandingSettings;
}>();

const form = useForm({
    app_name: props.brandingSettings.app_name ?? '',
    login_title: props.brandingSettings.login_title ?? '',
    login_subtitle: props.brandingSettings.login_subtitle ?? '',
    hero_title: props.brandingSettings.hero_title ?? '',
    hero_subtitle: props.brandingSettings.hero_subtitle ?? '',
    logo: null as File | null,
    icon: null as File | null,
    favicon: null as File | null,
});

type AssetKey = 'logo' | 'icon' | 'favicon';

const previews = ref<Record<AssetKey, string | null>>({
    logo: props.brandingSettings.logo_url,
    icon: props.brandingSettings.icon_url,
    favicon: props.brandingSettings.favicon_url,
});

const assets: Array<{
    key: AssetKey;
    title: string;
    hint: string;
    accept: string;
}> = [
    {
        key: 'logo',
        title: 'Logo',
        hint: 'Se muestra en el login. PNG, JPG, SVG o WEBP (máx. 2 MB).',
        accept: 'image/png,image/jpeg,image/svg+xml,image/webp',
    },
    {
        key: 'icon',
        title: 'Ícono',
        hint: 'Ícono cuadrado del menú lateral y el login. PNG, JPG, SVG o WEBP (máx. 1 MB).',
        accept: 'image/png,image/jpeg,image/svg+xml,image/webp',
    },
    {
        key: 'favicon',
        title: 'Favicon',
        hint: 'Ícono de la pestaña del navegador. ICO, PNG o SVG (máx. 512 KB).',
        accept: '.ico,image/png,image/svg+xml,image/x-icon,image/vnd.microsoft.icon',
    },
];

function onFileChange(key: AssetKey, event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    form[key] = file;

    if (file) {
        previews.value[key] = URL.createObjectURL(file);
    }
}

function removeAsset(key: AssetKey): void {
    router.delete(route('admin.settings.branding.destroy-asset', key), {
        preserveScroll: true,
        onSuccess: () => {
            form[key] = null;
            previews.value[key] = null;
        },
    });
}

function submit(): void {
    form.transform((data) => ({ ...data, _method: 'put' })).post(
        route('admin.settings.branding.update'),
        {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => form.reset('logo', 'icon', 'favicon'),
        },
    );
}
</script>

<template>
    <Head title="Branding" />

    <RazeLayout>
        <div class="grid grid-cols-12 gap-y-10 gap-x-6">
            <div class="col-span-12">
                <div class="flex flex-col md:h-10 gap-y-3 md:items-center md:flex-row">
                    <div class="text-base font-medium">Configuración</div>
                </div>
            </div>

            <div class="col-span-12">
                <div class="flex flex-col lg:flex-row gap-6">
                    <SettingsNav current="branding" />

                    <!-- Content -->
                    <div class="flex-1 space-y-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Textos -->
                            <div class="box box--stacked p-5">
                                <div class="mb-5">
                                    <h3 class="text-base font-medium">Textos del sitio</h3>
                                    <p class="text-slate-500 text-sm mt-1">
                                        Personaliza el nombre de la aplicación y los textos de la
                                        pantalla de inicio de sesión. Deja un campo vacío para usar
                                        el texto predeterminado.
                                    </p>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <FormLabel for="app_name">Nombre de la aplicación</FormLabel>
                                        <FormInput
                                            id="app_name"
                                            v-model="form.app_name"
                                            type="text"
                                            placeholder="ScanQR SMTVYS"
                                        />
                                        <FormHelp v-if="form.errors.app_name" class="text-danger">
                                            {{ form.errors.app_name }}
                                        </FormHelp>
                                        <FormHelp v-else>
                                            Aparece en la pestaña del navegador y el menú lateral.
                                        </FormHelp>
                                    </div>

                                    <div class="grid grid-cols-12 gap-4">
                                        <div class="col-span-12 sm:col-span-6">
                                            <FormLabel for="login_title">Título del login</FormLabel>
                                            <FormInput
                                                id="login_title"
                                                v-model="form.login_title"
                                                type="text"
                                                placeholder="Bienvenido"
                                            />
                                            <FormHelp v-if="form.errors.login_title" class="text-danger">
                                                {{ form.errors.login_title }}
                                            </FormHelp>
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <FormLabel for="login_subtitle">Subtítulo del login</FormLabel>
                                            <FormInput
                                                id="login_subtitle"
                                                v-model="form.login_subtitle"
                                                type="text"
                                                placeholder="Ingresa tus credenciales para acceder"
                                            />
                                            <FormHelp v-if="form.errors.login_subtitle" class="text-danger">
                                                {{ form.errors.login_subtitle }}
                                            </FormHelp>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-12 gap-4">
                                        <div class="col-span-12 sm:col-span-6">
                                            <FormLabel for="hero_title">Título del panel lateral</FormLabel>
                                            <FormTextarea
                                                id="hero_title"
                                                v-model="form.hero_title"
                                                rows="2"
                                                placeholder="Escaneo QR&#10;para eventos"
                                            />
                                            <FormHelp v-if="form.errors.hero_title" class="text-danger">
                                                {{ form.errors.hero_title }}
                                            </FormHelp>
                                            <FormHelp v-else>
                                                Texto grande del lado derecho del login. Cada línea se
                                                muestra en un renglón.
                                            </FormHelp>
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <FormLabel for="hero_subtitle">Subtítulo del panel lateral</FormLabel>
                                            <FormTextarea
                                                id="hero_subtitle"
                                                v-model="form.hero_subtitle"
                                                rows="2"
                                                placeholder="Captura los contactos de tus eventos y envíalos directo a tu CRM."
                                            />
                                            <FormHelp v-if="form.errors.hero_subtitle" class="text-danger">
                                                {{ form.errors.hero_subtitle }}
                                            </FormHelp>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Imágenes -->
                            <div class="box box--stacked p-5">
                                <div class="mb-5">
                                    <h3 class="text-base font-medium">Logo, ícono y favicon</h3>
                                    <p class="text-slate-500 text-sm mt-1">
                                        Sube tus propias imágenes. Si quitas una, se usa la
                                        predeterminada del sistema.
                                    </p>
                                </div>

                                <div class="space-y-5">
                                    <div
                                        v-for="asset in assets"
                                        :key="asset.key"
                                        class="flex flex-col sm:flex-row sm:items-center gap-4 rounded-xl border border-slate-200/70 dark:border-darkmode-400 p-4"
                                    >
                                        <div
                                            class="flex h-16 w-16 shrink-0 items-center justify-center rounded-lg border border-slate-200/70 bg-slate-50 dark:border-darkmode-400 dark:bg-darkmode-600 overflow-hidden"
                                        >
                                            <img
                                                v-if="previews[asset.key]"
                                                :src="previews[asset.key]!"
                                                :alt="asset.title"
                                                class="max-h-14 max-w-14 object-contain"
                                            />
                                            <Lucide v-else icon="Image" class="w-6 h-6 text-slate-400" />
                                        </div>

                                        <div class="flex-1">
                                            <div class="font-medium text-sm">{{ asset.title }}</div>
                                            <p class="text-xs text-slate-500 mt-0.5">{{ asset.hint }}</p>
                                            <FormHelp v-if="form.errors[asset.key]" class="text-danger">
                                                {{ form.errors[asset.key] }}
                                            </FormHelp>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <label
                                                class="inline-flex cursor-pointer items-center rounded-lg border border-slate-300/70 dark:border-darkmode-400 px-3 py-1.5 text-xs font-medium hover:bg-slate-100 dark:hover:bg-darkmode-400 transition-colors"
                                            >
                                                <Lucide icon="Upload" class="w-3.5 h-3.5 mr-1.5" />
                                                {{ previews[asset.key] ? 'Cambiar' : 'Subir' }}
                                                <input
                                                    type="file"
                                                    class="hidden"
                                                    :accept="asset.accept"
                                                    @change="onFileChange(asset.key, $event)"
                                                />
                                            </label>
                                            <button
                                                v-if="previews[asset.key]"
                                                type="button"
                                                class="inline-flex items-center rounded-lg border border-danger/30 px-3 py-1.5 text-xs font-medium text-danger hover:bg-danger/10 transition-colors"
                                                @click="removeAsset(asset.key)"
                                            >
                                                <Lucide icon="Trash2" class="w-3.5 h-3.5 mr-1.5" />
                                                Quitar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3">
                                <Transition
                                    enter-active-class="transition ease-in-out"
                                    enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out"
                                    leave-to-class="opacity-0"
                                >
                                    <span
                                        v-show="form.recentlySuccessful"
                                        class="flex items-center text-sm text-success"
                                    >
                                        <Lucide icon="CheckCircle2" class="w-4 h-4 mr-1" />
                                        Branding guardado.
                                    </span>
                                </Transition>
                                <Button type="submit" variant="primary" :disabled="form.processing">
                                    <Lucide icon="Save" class="mr-2 h-4 w-4" />
                                    {{ form.processing ? 'Guardando…' : 'Guardar branding' }}
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </RazeLayout>
</template>

<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Lucide from '@/components/Base/Lucide';

interface NavEntry {
    key: string;
    title: string;
    icon: string;
    routeName: string;
    permission?: string;
}

const props = defineProps<{
    current: string;
}>();

const page = usePage();

const entries: NavEntry[] = [
    { key: 'profile', title: 'Perfil', icon: 'User', routeName: 'admin.settings.profile.edit' },
    { key: 'password', title: 'Contraseña', icon: 'Lock', routeName: 'admin.settings.password.edit' },
    { key: 'appearance', title: 'Apariencia', icon: 'Sun', routeName: 'admin.settings.appearance.edit' },
    { key: 'branding', title: 'Branding', icon: 'Palette', routeName: 'admin.settings.branding.edit', permission: 'branding' },
];

const visible = computed(() => {
    const auth = page.props.auth as { permissions?: string[] } | undefined;
    const permissions = auth?.permissions ?? [];

    return entries.filter(
        (entry) => !entry.permission || permissions.includes(entry.permission),
    );
});
</script>

<template>
    <div class="w-full lg:w-52 shrink-0">
        <div class="box box--stacked p-1.5">
            <nav class="flex flex-col">
                <Link
                    v-for="entry in visible"
                    :key="entry.key"
                    :href="route(entry.routeName)"
                    :class="[
                        'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors',
                        entry.key === props.current
                            ? 'bg-primary/10 text-primary'
                            : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-darkmode-400',
                    ]"
                >
                    <Lucide :icon="entry.icon" class="w-4 h-4 mr-2.5" />
                    {{ entry.title }}
                </Link>
            </nav>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch, nextTick, onMounted, onBeforeUnmount } from 'vue';
import Breadcrumb from '@/components/Base/Breadcrumb';
import { Menu } from '@/components/Base/Headless';
import Lucide from '@/components/Base/Lucide';
import { useAppearance } from '@/composables/useAppearance';
import { useCompactMenu } from '@/composables/useCompactMenu';
import { useMenu } from '@/composables/useMenu';
import { notify } from '@/lib/notify';
import type { Branding } from '@/types';

const props = defineProps<{
  title?: string;
}>();

const page = usePage();
const auth = computed(() => page.props.auth as any);
const branding = computed(() => page.props.branding as Branding | undefined);
const brandName = computed(() => branding.value?.appName ?? 'ScanQR SMTVYS');
const brandIcon = computed(() => branding.value?.iconUrl ?? branding.value?.logoUrl ?? null);

// Mensajes flash del backend → toast
watch(
  () => page.props.flash as { success?: string; error?: string } | undefined,
  (flash) => {
    if (flash?.success) notify(flash.success, 'success');
    if (flash?.error) notify(flash.error, 'error');
  },
  { immediate: true, deep: true },
);

const { menu } = useMenu();
const { appearance, updateAppearance } = useAppearance();

const toggleDarkMode = () => {
  updateAppearance(appearance.value === 'dark' ? 'light' : 'dark');
};
const { compactMenu, setCompactMenu } = useCompactMenu();

const compactMenuOnHover = ref(false);
const activeMobileMenu = ref(false);
const showSearch = ref(false);
const searchQuery = ref('');
const searchInputRef = ref<HTMLInputElement | null>(null);
const activeIndex = ref(0);

const userInitials = computed(() => {
  const name: string = auth.value?.user?.name ?? '';
  const initials = name
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map((word) => word[0].toUpperCase())
    .join('');

  return initials || 'U';
});

const isMac =
  typeof navigator !== 'undefined' &&
  /Mac|iP(hone|ad|od)/.test(navigator.platform || navigator.userAgent);
const shortcutLabel = isMac ? '⌘K' : 'Ctrl K';

interface SearchItem {
  icon: string;
  title: string;
  pageName: string;
  section: string;
}

const toSectionLabel = (divider: string) =>
  divider.charAt(0) + divider.slice(1).toLowerCase();

// Áreas buscables: el menú lateral (ya filtrado por permisos) + páginas de configuración
const searchItems = computed<SearchItem[]>(() => {
  const permissions: string[] = auth.value?.permissions ?? [];
  const items: SearchItem[] = [];
  let section = 'General';

  for (const item of menu.value) {
    if (typeof item === 'string') {
      section = toSectionLabel(item);
      continue;
    }
    // Las páginas de settings se listan aparte, con su grupo completo
    if (!item.pageName || item.pageName.startsWith('admin.settings.')) continue;
    items.push({ icon: item.icon, title: item.title, pageName: item.pageName, section });
  }

  const settings: Array<Omit<SearchItem, 'section'> & { permission?: string }> = [
    { icon: 'User', title: 'Perfil', pageName: 'admin.settings.profile.edit' },
    { icon: 'Lock', title: 'Contraseña', pageName: 'admin.settings.password.edit' },
    { icon: 'SunMoon', title: 'Apariencia', pageName: 'admin.settings.appearance.edit' },
    { icon: 'Palette', title: 'Branding', pageName: 'admin.settings.branding.edit', permission: 'branding' },
  ];

  for (const entry of settings) {
    if (entry.permission && !permissions.includes(entry.permission)) continue;
    items.push({ icon: entry.icon, title: entry.title, pageName: entry.pageName, section: 'Configuración' });
  }

  return items;
});

const normalize = (value: string) =>
  value
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '');

const filteredItems = computed(() => {
  const query = normalize(searchQuery.value.trim());
  if (!query) return searchItems.value;

  return searchItems.value.filter(
    (item) => normalize(item.title).includes(query) || normalize(item.section).includes(query),
  );
});

const groupedResults = computed(() => {
  const groups: Array<{ section: string; items: Array<SearchItem & { index: number }> }> = [];

  filteredItems.value.forEach((item, index) => {
    let group = groups.find((g) => g.section === item.section);
    if (!group) {
      group = { section: item.section, items: [] };
      groups.push(group);
    }
    group.items.push({ ...item, index });
  });

  return groups;
});

const openSearch = () => {
  showSearch.value = true;
};

const goToResult = (item: SearchItem) => {
  showSearch.value = false;
  router.visit(route(item.pageName));
};

const moveSelection = (delta: number) => {
  const total = filteredItems.value.length;
  if (!total) return;
  activeIndex.value = (activeIndex.value + delta + total) % total;
  nextTick(() => {
    document
      .querySelector('[data-search-active="true"]')
      ?.scrollIntoView({ block: 'nearest' });
  });
};

const selectActive = () => {
  const item = filteredItems.value[activeIndex.value];
  if (item) goToResult(item);
};

watch(searchQuery, () => {
  activeIndex.value = 0;
});

watch(showSearch, (open) => {
  if (open) {
    searchQuery.value = '';
    activeIndex.value = 0;
    nextTick(() => searchInputRef.value?.focus());
  }
});

const onGlobalKeydown = (event: KeyboardEvent) => {
  if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
    event.preventDefault();
    showSearch.value = !showSearch.value;
  }
};

onMounted(() => window.addEventListener('keydown', onGlobalKeydown));
onBeforeUnmount(() => window.removeEventListener('keydown', onGlobalKeydown));

const toggleCompactMenu = (event: MouseEvent) => {
  event.preventDefault();
  setCompactMenu(!compactMenu.value);
};

const isActive = (pageName?: string) => {
  if (!pageName) return false;
  try {
    const routeUrl = route(pageName);
    return page.url.startsWith(new URL(routeUrl).pathname);
  } catch {
    return false;
  }
};

const requestFullscreen = () => {
  const el = document.documentElement;
  if (el.requestFullscreen) {
    el.requestFullscreen();
  }
};
</script>

<template>
  <div :class="[
    'raze',
    'before:content-[\'\'] before:bg-linear-to-b before:from-slate-100 before:to-slate-50 dark:before:from-darkmode-800 dark:before:to-darkmode-800 before:h-screen before:w-full before:fixed before:top-0',
  ]">
    <Head v-if="props.title" :title="props.title" />
    <!-- BEGIN: Side Menu -->
    <div :class="[
      'xl:ml-0 shadow-xl transition-[margin] duration-300 xl:shadow-none fixed top-0 left-0 z-50 side-menu group',
      'after:content-[\'\'] after:fixed after:inset-0 after:bg-black/80 after:xl:hidden',
      { 'side-menu--collapsed': compactMenu },
      { 'side-menu--on-hover': compactMenuOnHover },
      { 'ml-0 after:block': activeMobileMenu },
      { '-ml-[275px] after:hidden': !activeMobileMenu },
    ]">
      <div :class="[
        'fixed ml-[275px] w-10 h-10 items-center justify-center xl:hidden z-50',
        { flex: activeMobileMenu },
        { hidden: !activeMobileMenu },
      ]">
        <a href="#" @click.prevent="activeMobileMenu = false" class="mt-5 ml-5">
          <Lucide icon="X" class="w-8 h-8 text-white" />
        </a>
      </div>
      <div :class="[
        'bg-linear-to-b from-theme-1 to-theme-2 z-20 relative w-[275px] duration-300 transition-[width] xl:rounded-[0_1.2rem_1.2rem_0/0_1.7rem_1.7rem_0] group-[.side-menu--collapsed]:xl:w-[91px] group-[.side-menu--collapsed.side-menu--on-hover]:xl:shadow-[6px_0_12px_-4px_#0000000f] group-[.side-menu--collapsed.side-menu--on-hover]:xl:w-[275px] overflow-hidden h-screen flex flex-col',
        'after:content-[\'\'] after:absolute after:inset-0 after:-mr-4 after:bg-texture-white after:bg-contain after:bg-fixed after:bg-[center_-20rem] after:bg-no-repeat',
      ]" @mouseover.prevent="compactMenuOnHover = true" @mouseleave.prevent="compactMenuOnHover = false">
        <!-- Logo -->
        <div
          class="flex-none hidden xl:flex items-center z-10 px-5 h-[65px] w-[275px] overflow-hidden relative duration-300 group-[.side-menu--collapsed]:xl:w-[91px] group-[.side-menu--collapsed.side-menu--on-hover]:xl:w-[275px]">
          <Link href="/"
            class="flex items-center transition-[margin] duration-300 group-[.side-menu--collapsed]:xl:ml-2 group-[.side-menu--collapsed.side-menu--on-hover]:xl:ml-0">
            <div
              class="flex items-center justify-center w-[34px] rounded-lg h-[34px] bg-white/8 transition-transform ease-in-out group-[.side-menu--collapsed.side-menu--on-hover]:xl:-rotate-180 overflow-hidden">
              <img v-if="brandIcon" :src="brandIcon" :alt="brandName" class="w-6 h-6 object-contain" />
              <Lucide v-else icon="QrCode" class="w-5 h-5 text-white" />
            </div>
            <div
              class="ml-3.5 group-[.side-menu--collapsed.side-menu--on-hover]:xl:opacity-100 group-[.side-menu--collapsed]:xl:opacity-0 transition-opacity font-medium text-white">
              {{ brandName }}
            </div>
          </Link>
          <a href="#" @click="toggleCompactMenu"
            class="group-[.side-menu--collapsed.side-menu--on-hover]:xl:opacity-100 group-[.side-menu--collapsed]:xl:rotate-180 group-[.side-menu--collapsed]:xl:opacity-0 transition-[opacity,transform] hidden 3xl:flex items-center justify-center w-[20px] h-[20px] ml-auto border rounded-full border-white/40 text-white hover:bg-white/5">
            <Lucide icon="ArrowLeft" class="w-3.5 h-3.5 stroke-[1.3]" />
          </a>
        </div>

        <!-- Menu Items -->
        <div class="w-full h-full z-20 px-5 overflow-y-auto overflow-x-hidden pb-3">
          <ul class="scrollable">
            <template v-for="(item, index) in menu" :key="index">
              <!-- Divider -->
              <li v-if="typeof item === 'string'" class="side-menu__divider">
                {{ item }}
              </li>
              <!-- Menu Item -->
              <li v-else>
                <Link :href="item.pageName ? route(item.pageName) : '#'" :class="[
                  'side-menu__link',
                  { 'side-menu__link--active': isActive(item.pageName) },
                ]">
                  <Lucide :icon="item.icon" class="side-menu__link__icon" />
                  <div class="side-menu__link__title">{{ item.title }}</div>
                  <div v-if="item.badge" class="side-menu__link__badge">
                    {{ item.badge }}
                  </div>
                </Link>
              </li>
            </template>
          </ul>
        </div>
      </div>

      <!-- Top Bar -->
      <div :class="[
        'fixed h-[65px] transition-[margin] duration-100 xl:ml-[275px] group-[.side-menu--collapsed]:xl:ml-[90px] mt-3.5 inset-x-0 top-0',
        'before:content-[\'\'] before:mx-5 before:absolute before:top-0 before:inset-x-0 before:-mt-[15px] before:h-[20px] before:backdrop-blur',
      ]">
        <div
          class="absolute inset-x-0 h-full mx-5 box dark:bg-darkmode-600 dark:border-darkmode-400 before:content-[''] before:z-[-1] before:inset-x-4 before:shadow-sm before:h-full before:bg-slate-50 dark:before:bg-darkmode-700 before:border before:border-slate-200 dark:before:border-darkmode-500 before:absolute before:rounded-lg before:mx-auto before:top-0 before:mt-3">
          <div class="flex items-center w-full h-full px-5">
            <!-- Mobile Menu Toggle + Search -->
            <div class="flex items-center gap-1 xl:hidden">
              <a href="#" @click.prevent="activeMobileMenu = true"
                class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-darkmode-400">
                <Lucide icon="AlignJustify" class="w-[18px] h-[18px]" />
              </a>
              <a href="#" class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-darkmode-400"
                @click.prevent="showSearch = true">
                <Lucide icon="Search" class="w-[18px] h-[18px]" />
              </a>
            </div>

            <!-- BEGIN: Breadcrumb -->
            <Breadcrumb class="flex-1 hidden xl:block">
              <Breadcrumb.Link to="/admin/dashboard">App</Breadcrumb.Link>
              <Breadcrumb.Link :to="page.url" :active="true">
                {{ props.title || (page.props.title as string) || 'Dashboard' }}
              </Breadcrumb.Link>
            </Breadcrumb>
            <!-- END: Breadcrumb -->

            <!-- BEGIN: Search -->
            <div class="relative justify-center flex-1 hidden xl:flex" @click.prevent="openSearch">
              <div
                class="bg-slate-50 dark:bg-darkmode-400 border dark:border-darkmode-400 w-[350px] flex items-center py-2 px-3.5 rounded-[0.5rem] text-slate-400 cursor-pointer hover:bg-slate-100 dark:hover:bg-darkmode-300 transition-colors">
                <Lucide icon="Search" class="w-[18px] h-[18px]" />
                <div class="ml-2.5 mr-auto">Búsqueda rápida...</div>
                <div class="text-xs border dark:border-darkmode-300 rounded px-1.5 py-0.5">{{ shortcutLabel }}</div>
              </div>
            </div>
            <!-- END: Search -->

            <!-- BEGIN: Notification & User Menu -->
            <div class="flex items-center flex-1">
              <div class="flex items-center gap-1 ml-auto">
                <a href="#" @click.prevent="toggleDarkMode"
                  class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-darkmode-400">
                  <Lucide :icon="appearance === 'dark' ? 'Sun' : 'Moon'" class="w-[18px] h-[18px]" />
                </a>
                <a href="#" @click.prevent="requestFullscreen"
                  class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-darkmode-400">
                  <Lucide icon="Expand" class="w-[18px] h-[18px]" />
                </a>
              </div>
              <Menu class="ml-5">
                <Menu.Button
                  class="overflow-hidden rounded-full w-[36px] h-[36px] border-[3px] border-slate-200/70 dark:border-darkmode-400 bg-linear-to-b from-theme-1 to-theme-2 flex items-center justify-center">
                  <span class="text-xs font-semibold text-white tracking-wide">{{ userInitials }}</span>
                </Menu.Button>
                <Menu.Items class="w-60 mt-1">
                  <Menu.Header class="flex items-center gap-3">
                    <div
                      class="flex-none w-9 h-9 rounded-full bg-linear-to-b from-theme-1 to-theme-2 flex items-center justify-center">
                      <span class="text-xs font-semibold text-white tracking-wide">{{ userInitials }}</span>
                    </div>
                    <div class="min-w-0">
                      <div class="font-medium truncate">{{ auth.user?.name }}</div>
                      <div class="text-xs font-normal text-slate-500 truncate">{{ auth.user?.email }}</div>
                    </div>
                  </Menu.Header>
                  <Menu.Divider />
                  <Menu.Item>
                    <Link :href="route('admin.settings.profile.edit')" class="flex items-center w-full">
                      <Lucide icon="User" class="w-4 h-4 mr-2" />
                      Perfil
                    </Link>
                  </Menu.Item>
                  <Menu.Item>
                    <Link :href="route('admin.settings.appearance.edit')" class="flex items-center w-full">
                      <Lucide icon="Settings" class="w-4 h-4 mr-2" />
                      Configuración
                    </Link>
                  </Menu.Item>
                  <Menu.Divider />
                  <Menu.Item>
                    <Link :href="route('logout')" method="post" as="button"
                      class="flex items-center w-full text-left text-danger">
                      <Lucide icon="Power" class="w-4 h-4 mr-2" />
                      Cerrar Sesión
                    </Link>
                  </Menu.Item>
                </Menu.Items>
              </Menu>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- END: Side Menu -->

    <!-- BEGIN: Quick Search -->
    <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0"
      enter-to-class="opacity-100" leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100" leave-to-class="opacity-0">
      <div v-if="showSearch" class="fixed inset-0 z-[60] flex items-start justify-center pt-[15vh]"
        @click.self="showSearch = false" @keydown.escape="showSearch = false">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showSearch = false"></div>
        <div class="relative w-[90%] max-w-[600px] bg-white dark:bg-darkmode-600 rounded-xl shadow-2xl z-10">
          <div class="flex items-center px-5 py-4 border-b dark:border-darkmode-400">
            <Lucide icon="Search" class="w-5 h-5 text-slate-400 mr-3" />
            <input ref="searchInputRef" v-model="searchQuery" type="text"
              class="flex-1 bg-transparent border-0 outline-none text-base placeholder:text-slate-400 dark:text-slate-200 focus:ring-0 p-0"
              placeholder="Buscar páginas, configuración..." @keydown.escape="showSearch = false"
              @keydown.down.prevent="moveSelection(1)" @keydown.up.prevent="moveSelection(-1)"
              @keydown.enter.prevent="selectActive" />
            <div class="text-xs text-slate-400 border dark:border-darkmode-400 rounded px-1.5 py-0.5 ml-3">ESC</div>
          </div>
          <div class="p-3 max-h-[50vh] overflow-y-auto">
            <template v-for="group in groupedResults" :key="group.section">
              <div class="px-2 py-1.5 text-xs font-medium text-slate-400 uppercase">{{ group.section }}</div>
              <a v-for="item in group.items" :key="item.pageName" href="#"
                :data-search-active="item.index === activeIndex" :class="[
                  'flex items-center px-3 py-2.5 rounded-lg cursor-pointer',
                  item.index === activeIndex
                    ? 'bg-primary/10 text-primary'
                    : 'hover:bg-slate-100 dark:hover:bg-darkmode-400',
                ]" @click.prevent="goToResult(item)" @mousemove="activeIndex = item.index">
                <Lucide :icon="item.icon" :class="[
                  'w-4 h-4 mr-3',
                  item.index === activeIndex ? 'text-primary' : 'text-slate-500',
                ]" />
                <span :class="item.index === activeIndex ? 'font-medium' : 'dark:text-slate-300'">
                  {{ item.title }}
                </span>
                <Lucide v-if="item.index === activeIndex" icon="CornerDownLeft"
                  class="w-3.5 h-3.5 ml-auto text-primary/70" />
              </a>
            </template>
            <div v-if="!filteredItems.length" class="px-3 py-8 text-center text-slate-400">
              <Lucide icon="SearchX" class="w-8 h-8 mx-auto mb-2 stroke-[1.2]" />
              No se encontraron resultados para «{{ searchQuery }}»
            </div>
          </div>
          <div
            class="flex items-center gap-4 px-5 py-3 border-t dark:border-darkmode-400 text-xs text-slate-400">
            <span class="flex items-center gap-1.5">
              <kbd class="border dark:border-darkmode-400 rounded px-1 py-0.5">↑</kbd>
              <kbd class="border dark:border-darkmode-400 rounded px-1 py-0.5">↓</kbd>
              navegar
            </span>
            <span class="flex items-center gap-1.5">
              <kbd class="border dark:border-darkmode-400 rounded px-1 py-0.5">↵</kbd>
              abrir
            </span>
            <span class="flex items-center gap-1.5 ml-auto">
              <kbd class="border dark:border-darkmode-400 rounded px-1 py-0.5">{{ shortcutLabel }}</kbd>
              abrir/cerrar
            </span>
          </div>
        </div>
      </div>
    </Transition>
    <!-- END: Quick Search -->

    <!-- BEGIN: Content -->
    <div :class="[
      'transition-[margin,width] duration-100 px-5 pt-[56px] pb-16 relative z-20',
      { 'xl:ml-[275px]': !compactMenu },
      { 'xl:ml-[91px]': compactMenu },
    ]">
      <div class="container mt-[65px]">
        <slot />
      </div>
    </div>
    <!-- END: Content -->
  </div>
</template>

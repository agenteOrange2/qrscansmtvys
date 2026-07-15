import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export interface MenuItem {
  icon: string;
  title: string;
  pageName?: string;
  subMenu?: MenuItem[];
  ignore?: boolean;
  badge?: string;
  permission?: string;
}

const items: Array<MenuItem | string> = [
  {
    icon: 'Home',
    pageName: 'admin.dashboard',
    title: 'Dashboard',
    permission: 'dashboard',
  },
  'ESCANEO',
  {
    icon: 'ScanLine',
    pageName: 'admin.scan',
    title: 'Escanear QR',
    permission: 'scan',
  },
  {
    icon: 'Users',
    pageName: 'admin.usuarios-capturados.index',
    title: 'Usuarios Capturados',
    permission: 'captura',
  },
  {
    icon: 'Tags',
    pageName: 'admin.marcas.index',
    title: 'Marcas',
    permission: 'scan',
  },
  'INTEGRACIONES',
  {
    icon: 'Plug',
    pageName: 'admin.integraciones.bitrix.edit',
    title: 'Bitrix24 CRM',
    permission: 'integraciones',
  },
  'ADMINISTRACIÓN',
  {
    icon: 'UserCog',
    pageName: 'admin.users.index',
    title: 'Usuarios',
    permission: 'users',
  },
  {
    icon: 'ShieldCheck',
    pageName: 'admin.roles.index',
    title: 'Roles',
    permission: 'roles',
  },
  {
    icon: 'KeyRound',
    pageName: 'admin.permissions.index',
    title: 'Permisos',
    permission: 'roles',
  },
  'CUENTA',
  {
    icon: 'Settings',
    pageName: 'admin.settings.profile.edit',
    title: 'Configuración',
  },
];

export function useMenu() {
  const page = usePage();

  const menu = computed<Array<MenuItem | string>>(() => {
    const auth = page.props.auth as { permissions?: string[] } | undefined;
    const permissions = auth?.permissions ?? [];

    const visible = items.filter(
      (item) =>
        typeof item === 'string' ||
        !item.permission ||
        permissions.includes(item.permission),
    );

    // Ocultar divisores que quedaron sin elementos debajo
    return visible.filter((item, index) => {
      if (typeof item !== 'string') return true;
      const next = visible[index + 1];

      return next !== undefined && typeof next !== 'string';
    });
  });

  return {
    menu,
  };
}

# Graph Report - qrscansmtvys  (2026-07-15)

## Corpus Check
- 579 files · ~4,415,876 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2425 nodes · 3233 edges · 300 communities (272 shown, 28 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 41 edges (avg confidence: 0.76)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `48c4dda6`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Scan.vue
- devDependencies
- FormInput.vue
- SideMenu.vue
- AGENTS.md
- cn
- Button.vue
- utils.ts
- scripts
- index.ts
- index.ts
- index.ts
- Alert.vue
- index.ts
- Dialog.vue
- Button.vue
- index.ts
- Plantilla Laravel 12 + Inertia + Vue
- compilerOptions
- Button.vue
- User.php
- Table.vue
- RazeLayout.vue
- ProfileController.php
- index.ts
- index.ts
- ResetPassword.vue
- FormRequest
- Inertia Vue Development
- Inertia Vue Development
- Sidebar.vue
- QrScan
- useAppearance.ts
- Highlight.vue
- Bitrix.vue
- Marca
- TwoFactor.vue
- SidebarProvider.vue
- Bitrix24Service
- index.ts
- UserQrScanExport
- dependencies
- index.ts
- Slideover.vue
- Litepicker.vue
- TomSelect.vue
- Layout.vue
- SendScanToBitrix.php
- inertia.php
- aliases
- Pest Testing 3
- Pest Testing 3
- ThemeSwitcher.vue
- Role
- UserController.php
- Controller
- Tailwind CSS Development
- Tailwind CSS Development
- TwoFactorSetupModal.vue
- Permission
- InputOTPSlot.vue
- TooltipContent.vue
- Index.vue
- HandleAppearance.php
- optionalDependencies
- scripts
- BitrixSettingController.php
- DashboardController.php
- AppHeader.vue
- Notification.vue
- TinySlider.vue
- global.d.ts
- require
- Wayfinder Development
- Wayfinder Development
- Dropzone.vue
- UserInfo.vue
- require-dev
- ActivitiesPanel.vue
- AppSidebar.vue
- LeafletMapLoader.vue
- Tippy.vue
- Index.vue
- Welcome.vue
- Chart.vue
- BalloonBlockEditor.vue
- BalloonEditor.vue
- ClassicEditor.vue
- DocumentEditor.vue
- InlineEditor.vue
- Collapsible.vue
- SheetContent.vue
- Profile.vue
- composer.json
- Link.vue
- Bar.vue
- TippyContent.vue
- MarcaForm.vue
- Alert.vue
- index.ts
- Edit.vue
- config
- UserFactory
- SidebarMenuSkeleton.vue
- Index.vue
- TwoFactorChallenge.vue
- QrScan.php
- Draggable.vue
- index.ts
- ckeditor.ts
- Checkbox.vue
- index.ts
- DashboardOverview1.vue
- psr-4
- LoadingIcon.vue
- Badge.vue
- DropdownMenuCheckboxItem.vue
- DropdownMenuContent.vue
- DropdownMenuRadioItem.vue
- DropdownMenuSubContent.vue
- InputOTP.vue
- ProfileValidationRules.php
- DropdownMenu.vue
- DropdownMenuItem.vue
- DropdownMenuLabel.vue
- DropdownMenuRadioGroup.vue
- DropdownMenuSub.vue
- DropdownMenuSubTrigger.vue
- Input.vue
- TestCase.php
- autoload-dev
- extra
- keywords
- laravel-boost
- laravel-boost
- clsx
- dayjs
- @inertiajs/vue3
- @left4code/tw-starter
- lodash
- pinia
- reka-ui
- tailwind-merge
- tailwindcss
- tippy.js
- tw-animate-css
- vue
- vue-input-otp
- TextLink.vue
- vue-shims.d.ts
- ziggy.js
- create-vhost.sh
- init-project.sh
- resetIndividual
- DropdownMenuSeparator.vue
- eslint-plugin-import
- @laravel/vite-plugin-wayfinder
- postcss-import
- postcss-nesting
- prettier-plugin-tailwindcss
- @tailwindcss/forms
- @tailwindcss/vite
- @types/lodash
- @types/toastify-js
- typescript
- vite
- @vitejs/plugin-vue
- @vue/eslint-config-typescript

## God Nodes (most connected - your core abstractions)
1. `cn()` - 91 edges
2. `QrScan` - 30 edges
3. `Controller` - 23 edges
4. `User` - 21 edges
5. `Marca` - 20 edges
6. `ScanGroup` - 19 edges
7. `compilerOptions` - 19 edges
8. `Setting` - 18 edges
9. `Bitrix24Service` - 18 edges
10. `UserQrScanExport` - 16 edges

## Surprising Connections (you probably didn't know these)
- `enableBitrix()` --calls--> `Setting`  [INFERRED]
  tests/Feature/BitrixIntegrationTest.php → app/Models/Setting.php
- `BitrixSettingController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Admin/BitrixSettingController.php → app/Http/Controllers/Controller.php
- `MarcaController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Admin/MarcaController.php → app/Http/Controllers/Controller.php
- `PermissionController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Admin/PermissionController.php → app/Http/Controllers/Controller.php
- `QrScanController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Admin/QrScanController.php → app/Http/Controllers/Controller.php

## Import Cycles
- 1-file cycle: `resources/js/components/Base/Dropzone/Dropzone.vue -> resources/js/components/Base/Dropzone/Dropzone.vue`
- 1-file cycle: `resources/js/components/Base/Dropzone/dropzone.ts -> resources/js/components/Base/Dropzone/dropzone.ts`
- 1-file cycle: `resources/js/components/Base/Litepicker/Litepicker.vue -> resources/js/components/Base/Litepicker/Litepicker.vue`
- 1-file cycle: `resources/js/components/Base/Litepicker/litepicker.ts -> resources/js/components/Base/Litepicker/litepicker.ts`
- 1-file cycle: `resources/js/components/Base/Notification/Notification.vue -> resources/js/components/Base/Notification/Notification.vue`
- 1-file cycle: `resources/js/components/Base/Notification/notification.ts -> resources/js/components/Base/Notification/notification.ts`
- 3-file cycle: `resources/js/components/ui/sidebar/SidebarMenuButton.vue -> resources/js/components/ui/sidebar/SidebarMenuButtonChild.vue -> resources/js/components/ui/sidebar/index.ts -> resources/js/components/ui/sidebar/SidebarMenuButton.vue`

## Communities (300 total, 28 thin omitted)

### Community 0 - "Scan.vue"
Cohesion: 0.05
Nodes (50): estadosMexico, colors, notify(), NotifyType, emptyContact(), parseQrData(), parseVCard(), ScannedContact (+42 more)

### Community 1 - "devDependencies"
Cohesion: 0.11
Nodes (19): concurrently, eslint-config-prettier, @eslint/js, eslint-plugin-vue, devDependencies, concurrently, eslint-config-prettier, @eslint/js (+11 more)

### Community 2 - "FormInput.vue"
Cohesion: 0.05
Nodes (40): attrs, computedClass, ProvideFormInline, attrs, computedClass, emit, formInline, FormInputEmit (+32 more)

### Community 3 - "SideMenu.vue"
Cohesion: 0.06
Nodes (35): BreadcrumbProps, { light }, ProvideBeradcrumb, slots, BreadcrumbComponent, breadcrumb, computedClass, LinkProps (+27 more)

### Community 4 - "AGENTS.md"
Cohesion: 0.04
Nodes (45): APIs & Eloquent Resources, Application Structure & Architecture, Artisan, Authentication & Authorization, Available Search Syntax, Comments, Configuration, Constructors (+37 more)

### Community 5 - "cn"
Cohesion: 0.08
Nodes (15): props, delegatedProps, props, props, props, props, props, props (+7 more)

### Community 6 - "Button.vue"
Cohesion: 0.05
Nodes (36): {
  as = "button",
  size,
  variant,
  elevated,
  rounded,
}, attrs, ButtonProps, computedClass, danger, dark, Elevated, facebook (+28 more)

### Community 7 - "utils.ts"
Cohesion: 0.07
Nodes (23): Props, SidebarMenuButtonVariants, props, props, props, props, props, props (+15 more)

### Community 8 - "scripts"
Cohesion: 0.05
Nodes (40): scripts, ci:check, dev, dev:ssr, lint, lint:check, post-autoload-dump, post-create-project-cmd (+32 more)

### Community 9 - "index.ts"
Cohesion: 0.12
Nodes (13): { as = "div" }, attrs, computedClass, DividerProps, { as = "div" }, attrs, computedClass, HeaderProps (+5 more)

### Community 10 - "index.ts"
Cohesion: 0.05
Nodes (25): emits, forwarded, props, delegatedProps, emits, forwarded, props, props (+17 more)

### Community 11 - "index.ts"
Cohesion: 0.06
Nodes (27): navigationMenuTriggerStyle, delegatedProps, emits, forwarded, props, delegatedProps, emits, forwarded (+19 more)

### Community 12 - "Alert.vue"
Cohesion: 0.06
Nodes (32): AlertProps, {
  as = "div",
  dismissible,
  variant,
  ...props
}, attrs, computedClass, danger, dark, outlineDanger, outlineDark (+24 more)

### Community 13 - "index.ts"
Cohesion: 0.06
Nodes (23): emits, forwarded, props, props, delegatedProps, emits, forwarded, props (+15 more)

### Community 14 - "Dialog.vue"
Cohesion: 0.07
Nodes (27): { as = "div" }, attrs, computedClass, DescriptionProps, attrs, computedClass, DialogProps, emit (+19 more)

### Community 15 - "Button.vue"
Cohesion: 0.09
Nodes (21): { as = "button" }, attrs, ButtonProps, computedClass, disclosure, group, attrs, computedClass (+13 more)

### Community 16 - "index.ts"
Cohesion: 0.11
Nodes (15): attrs, computedClass, FormCheckComponent, attrs, computedClass, attrs, computedClass, FormSwitchComponent (+7 more)

### Community 17 - "Plantilla Laravel 12 + Inertia + Vue"
Cohesion: 0.06
Nodes (31): 1. Clonar el repositorio, 2. Limpiar archivos del proyecto anterior, 3. Instalar dependencias, 4. Configurar entorno, 5. Migraciones y seeders, 6. Compilar assets, 7. Iniciar servidor de desarrollo, Actualizar dependencias (+23 more)

### Community 18 - "compilerOptions"
Cohesion: 0.06
Nodes (30): DOM, DOM.Iterable, ESNext, ./resources/js/*, resources/js/**/*.d.ts, resources/js/**/*.ts, resources/js/**/*.tsx, resources/js/**/*.vue (+22 more)

### Community 19 - "Button.vue"
Cohesion: 0.08
Nodes (22): { as = "a" }, attrs, ButtonProps, computedClass, list, tab, GroupProps, TabComponent (+14 more)

### Community 20 - "User.php"
Cohesion: 0.05
Nodes (32): emailRules(), nameRules(), profileRules(), PermissionController, RedirectResponse, Request, Response, RedirectResponse (+24 more)

### Community 21 - "Table.vue"
Cohesion: 0.09
Nodes (21): TableComponent, attrs, computedClass, {
  dark = false,
  bordered = false,
  hover = false,
  striped = false,
  sm = false,
}, ProvideTable, TableProps, attrs, computedClass (+13 more)

### Community 22 - "RazeLayout.vue"
Cohesion: 0.07
Nodes (20): form, props, compactMenu, useCompactMenu(), items, MenuItem, useMenu(), activeMobileMenu (+12 more)

### Community 23 - "ProfileController.php"
Cohesion: 0.14
Nodes (5): MarcaRequest, StoreQrScanRequest, StoreUserRequest, UpdateUserRequest, FormRequest

### Community 24 - "index.ts"
Cohesion: 0.12
Nodes (13): attrs, computedClass, Icon, LucideProps, props, entries, NavEntry, page (+5 more)

### Community 25 - "index.ts"
Cohesion: 0.11
Nodes (11): DialogComponent, RecentScan, deleteTarget, Marca, deleteTarget, RoleRow, deleteTarget, PaginationLink (+3 more)

### Community 26 - "ResetPassword.vue"
Cohesion: 0.12
Nodes (8): passwordInput, delegatedProps, props, props, form, form, form, props

### Community 27 - "FormRequest"
Cohesion: 0.13
Nodes (12): beep(), cameraReady, cameras, constraints, emit, errorMessage, flash, onDetect() (+4 more)

### Community 28 - "Inertia Vue Development"
Cohesion: 0.09
Nodes (22): Basic Link Component, Basic Usage, Client-Side Navigation, Common Pitfalls, Deferred Props, Documentation, Form Component (Recommended), Form Component Reset Props (+14 more)

### Community 29 - "Inertia Vue Development"
Cohesion: 0.09
Nodes (22): Basic Link Component, Basic Usage, Client-Side Navigation, Common Pitfalls, Deferred Props, Documentation, Form Component (Recommended), Form Component Reset Props (+14 more)

### Community 30 - "Sidebar.vue"
Cohesion: 0.10
Nodes (14): emits, forwarded, props, props, delegatedProps, props, props, props (+6 more)

### Community 31 - "QrScan"
Cohesion: 0.17
Nodes (8): JsonResponse, RedirectResponse, Request, Response, QrScanController, UpdateQrScanRequest, QrScan, BinaryFileResponse

### Community 32 - "useAppearance.ts"
Cohesion: 0.15
Nodes (14): { appearance, updateAppearance }, tabs, appearance, getStoredAppearance(), handleSystemThemeChange(), initializeTheme(), mediaQuery(), prefersDark() (+6 more)

### Community 33 - "Highlight.vue"
Cohesion: 0.10
Nodes (16): attrs, buttonComputedClass, codePreviewComputedClass, copySource, copySourceEl, copyText, highlightComputedClass, HighlightProps (+8 more)

### Community 34 - "Bitrix.vue"
Cohesion: 0.11
Nodes (18): catalogError, extractError(), FailedScan, form, loadingPipelines, loadingStages, loadPipelines(), loadStages() (+10 more)

### Community 35 - "Marca"
Cohesion: 0.29
Nodes (5): MarcaController, RedirectResponse, Response, Marca, BelongsToMany

### Community 36 - "TwoFactor.vue"
Cohesion: 0.15
Nodes (10): isRecoveryCodesVisible, recoveryCodeSectionRef, { recoveryCodesList, fetchRecoveryCodes, errors }, errors, hasSetupData, manualSetupKey, qrCodeSvg, recoveryCodesList (+2 more)

### Community 37 - "SidebarProvider.vue"
Cohesion: 0.12
Nodes (15): emits, isMobile, open, openMobile, props, setOpen(), setOpenMobile(), state (+7 more)

### Community 39 - "index.ts"
Cohesion: 0.20
Nodes (8): className, Props, Props, Props, Props, Props, Props, BreadcrumbItem

### Community 40 - "UserQrScanExport"
Cohesion: 0.18
Nodes (10): Collection, UserQrScanExport, Exportable, FromCollection, WithColumnFormatting, WithCustomStartCell, WithHeadings, WithMapping (+2 more)

### Community 41 - "dependencies"
Cohesion: 0.04
Nodes (47): axios, chart.js, class-variance-authority, clsx, dayjs, @headlessui/vue, @inertiajs/vue3, laravel-vite-plugin (+39 more)

### Community 42 - "index.ts"
Cohesion: 0.12
Nodes (12): { as = "div" }, attrs, computedClass, DescriptionProps, { as = "div" }, attrs, computedClass, FooterProps (+4 more)

### Community 43 - "Slideover.vue"
Cohesion: 0.13
Nodes (15): { as = "div" }, attrs, computedClass, PanelProps, slideover, attrs, computedClass, emit (+7 more)

### Community 44 - "Litepicker.vue"
Cohesion: 0.19
Nodes (14): emit, getDateFormat(), init(), LitepickerConfig, LitepickerElement, LitepickerEmit, LitepickerProps, litepickerRef (+6 more)

### Community 45 - "TomSelect.vue"
Cohesion: 0.21
Nodes (13): getOptions(), init(), setValue(), updateValue(), computedOptions, emit, props, ProvideTomSelect (+5 more)

### Community 46 - "Layout.vue"
Cohesion: 0.19
Nodes (10): footerNavItems, mainNavItems, { isCurrentUrl }, currentUrlReactive, page, useCurrentUrl(), UseCurrentUrlReturn, { isCurrentOrParentUrl } (+2 more)

### Community 47 - "SendScanToBitrix.php"
Cohesion: 0.20
Nodes (10): Collection, Throwable, SendScanGroupToBitrix, Throwable, SendScanToBitrix, Dispatchable, InteractsWithQueue, Queueable (+2 more)

### Community 48 - "inertia.php"
Cohesion: 0.20
Nodes (3): AppServiceProvider, FortifyServiceProvider, ServiceProvider

### Community 49 - "aliases"
Cohesion: 0.12
Nodes (15): aliases, components, composables, lib, ui, utils, iconLibrary, $schema (+7 more)

### Community 50 - "Pest Testing 3"
Cohesion: 0.12
Nodes (15): Architecture Testing, Assertions, Basic Test Structure, Basic Usage, Common Pitfalls, Creating Tests, Datasets, Documentation (+7 more)

### Community 51 - "Pest Testing 3"
Cohesion: 0.12
Nodes (15): Architecture Testing, Assertions, Basic Test Structure, Basic Usage, Common Pitfalls, Creating Tests, Datasets, Documentation (+7 more)

### Community 52 - "ThemeSwitcher.vue"
Cohesion: 0.13
Nodes (9): colorSchemeStore, imageAssets, setColorSchemeClass(), switchColorScheme(), themeStore, themeSwitcherSlideover, route, themeComponent (+1 more)

### Community 53 - "Role"
Cohesion: 0.27
Nodes (5): RedirectResponse, Request, Response, ProfileController, ProfileDeleteRequest

### Community 55 - "Controller"
Cohesion: 0.20
Nodes (7): DashboardController, Request, Response, Controller, PasswordController, RedirectResponse, Response

### Community 56 - "Tailwind CSS Development"
Cohesion: 0.13
Nodes (14): Basic Usage, Common Patterns, Common Pitfalls, CSS-First Configuration, Dark Mode, Documentation, Flexbox Layout, Grid Layout (+6 more)

### Community 57 - "Tailwind CSS Development"
Cohesion: 0.13
Nodes (14): Basic Usage, Common Patterns, Common Pitfalls, CSS-First Configuration, Dark Mode, Documentation, Flexbox Layout, Grid Layout (+6 more)

### Community 58 - "TwoFactorSetupModal.vue"
Cohesion: 0.13
Nodes (11): Props, uniqueErrors, code, { copy, copied }, isOpen, modalConfig, pinInputContainerRef, Props (+3 more)

### Community 59 - "Permission"
Cohesion: 0.36
Nodes (4): CreateNewUser, ProfileUpdateRequest, CreatesNewUsers, ProfileValidationRules

### Community 60 - "InputOTPSlot.vue"
Cohesion: 0.11
Nodes (14): delegatedProps, emits, forwarded, props, delegatedProps, forwarded, props, forwarded (+6 more)

### Community 61 - "TooltipContent.vue"
Cohesion: 0.14
Nodes (9): emits, forwarded, props, delegatedProps, emits, forwarded, props, props (+1 more)

### Community 62 - "Index.vue"
Cohesion: 0.14
Nodes (10): MainProps, props, DialogComponent, { as = "div", placement = "bottom-end" }, attrs, computedClass, PanelProps, SlideoverComponent (+2 more)

### Community 63 - "HandleAppearance.php"
Cohesion: 0.12
Nodes (12): Response, TwoFactorAuthenticationController, HandleAppearance, Request, Response, HandleInertiaRequests, Request, TwoFactorAuthenticationRequest (+4 more)

### Community 64 - "optionalDependencies"
Cohesion: 0.15
Nodes (13): lightningcss-linux-x64-gnu, lightningcss-win32-x64-msvc, optionalDependencies, lightningcss-linux-x64-gnu, lightningcss-win32-x64-msvc, @rollup/rollup-linux-x64-gnu, @rollup/rollup-win32-x64-msvc, @tailwindcss/oxide-linux-x64-gnu (+5 more)

### Community 65 - "scripts"
Cohesion: 0.15
Nodes (12): private, $schema, scripts, build, build:ssr, dev, format, format:check (+4 more)

### Community 66 - "BitrixSettingController.php"
Cohesion: 0.33
Nodes (5): BitrixSettingController, JsonResponse, RedirectResponse, Request, Response

### Community 67 - "DashboardController.php"
Cohesion: 0.20
Nodes (4): BelongsTo, BelongsToMany, fakePng(), UploadedFile

### Community 68 - "AppHeader.vue"
Cohesion: 0.18
Nodes (8): auth, { isCurrentUrl, whenCurrentUrl }, mainNavItems, page, Props, rightNavItems, Props, page

### Community 69 - "Notification.vue"
Cohesion: 0.27
Nodes (8): init(), NotificationElement, NotificationProps, props, ProvideNotification, reInit(), toastifyRef, vNotificationDirective

### Community 70 - "TinySlider.vue"
Cohesion: 0.24
Nodes (8): init(), reInit(), props, ProvideTinySlider, sliderRef, TinySliderElement, TinySliderProps, vSliderDirective

### Community 71 - "global.d.ts"
Cohesion: 0.20
Nodes (10): Auth, TwoFactorConfigContent, User, ComponentCustomProperties, ImportMeta, ImportMetaEnv, InertiaConfig, @inertiajs/core (+2 more)

### Community 72 - "require"
Cohesion: 0.18
Nodes (11): require, inertiajs/inertia-laravel, laravel/fortify, laravel/framework, laravel/tinker, laravel/wayfinder, maatwebsite/excel, php (+3 more)

### Community 73 - "Wayfinder Development"
Cohesion: 0.18
Nodes (10): Common Methods, Common Pitfalls, Documentation, Generate Routes, Import Patterns, Quick Reference, Verification, Wayfinder Development (+2 more)

### Community 74 - "Wayfinder Development"
Cohesion: 0.18
Nodes (10): Common Methods, Common Pitfalls, Documentation, Generate Routes, Import Patterns, Quick Reference, Verification, Wayfinder Development (+2 more)

### Community 75 - "Dropzone.vue"
Cohesion: 0.25
Nodes (7): DropzoneElement, DropzoneProps, fileUploadRef, init(), props, ProvideDropzone, vFileUploadDirective

### Community 76 - "UserInfo.vue"
Cohesion: 0.13
Nodes (11): { isMobile, state }, page, user, props, { getInitials }, Props, showAvatar, Props (+3 more)

### Community 77 - "require-dev"
Cohesion: 0.20
Nodes (10): require-dev, fakerphp/faker, laravel/boost, laravel/pail, laravel/pint, laravel/sail, mockery/mockery, nunomaduro/collision (+2 more)

### Community 78 - "ActivitiesPanel.vue"
Cohesion: 0.33
Nodes (3): FileIconProps, Variant, { variant, type, src }

### Community 79 - "AppSidebar.vue"
Cohesion: 0.13
Nodes (7): props, props, props, props, props, props, props

### Community 80 - "LeafletMapLoader.vue"
Cohesion: 0.29
Nodes (7): initializeMap(), LeafletElement, MapConfig, Init, LeafletMapLoaderProps, mapRef, props

### Community 81 - "Tippy.vue"
Cohesion: 0.22
Nodes (5): props, ProvideTippy, TippyProps, tippyRef, vTippyDirective

### Community 82 - "Index.vue"
Cohesion: 0.20
Nodes (5): deleteTarget, editTarget, form, PermissionRow, showCreate

### Community 83 - "Welcome.vue"
Cohesion: 0.20
Nodes (6): features, groupSteps, mobileMenu, page, scrolled, showBackToTop

### Community 84 - "Chart.vue"
Cohesion: 0.31
Nodes (6): ChartElement, ChartProps, chartRef, init(), props, ProvideChart

### Community 85 - "BalloonBlockEditor.vue"
Cohesion: 0.22
Nodes (7): cacheData, CkeditorEmit, CkeditorProps, editorRef, emit, props, vEditorDirective

### Community 86 - "BalloonEditor.vue"
Cohesion: 0.22
Nodes (7): cacheData, CkeditorEmit, CkeditorProps, editorRef, emit, props, vEditorDirective

### Community 87 - "ClassicEditor.vue"
Cohesion: 0.22
Nodes (7): cacheData, CkeditorEmit, CkeditorProps, editorRef, emit, props, vEditorDirective

### Community 88 - "DocumentEditor.vue"
Cohesion: 0.22
Nodes (7): cacheData, CkeditorEmit, CkeditorProps, editorRef, emit, props, vEditorDirective

### Community 89 - "InlineEditor.vue"
Cohesion: 0.22
Nodes (7): cacheData, CkeditorEmit, CkeditorProps, editorRef, emit, props, vEditorDirective

### Community 90 - "Collapsible.vue"
Cohesion: 0.22
Nodes (5): emits, forwarded, props, props, props

### Community 91 - "SheetContent.vue"
Cohesion: 0.22
Nodes (7): delegatedProps, emits, forwarded, props, SheetContentProps, delegatedProps, props

### Community 92 - "Profile.vue"
Cohesion: 0.22
Nodes (6): deleteForm, page, profileForm, Props, showDeleteModal, user

### Community 93 - "composer.json"
Cohesion: 0.25
Nodes (7): description, license, minimum-stability, name, prefer-stable, $schema, type

### Community 94 - "Link.vue"
Cohesion: 0.25
Nodes (5): PaginationComponent, { as = "a", active = false }, attrs, computedClass, LinkProps

### Community 95 - "Bar.vue"
Cohesion: 0.25
Nodes (5): attrs, computedClass, ProgressComponent, attrs, computedClass

### Community 96 - "TippyContent.vue"
Cohesion: 0.25
Nodes (4): props, ProvideTippy, TippyContentProps, tippyRef

### Community 97 - "MarcaForm.vue"
Cohesion: 0.25
Nodes (3): form, preview, props

### Community 98 - "Alert.vue"
Cohesion: 0.29
Nodes (4): props, props, props, AlertVariants

### Community 99 - "index.ts"
Cohesion: 0.32
Nodes (3): Props, ButtonVariants, form

### Community 100 - "Edit.vue"
Cohesion: 0.14
Nodes (10): { appearance, updateAppearance }, brandImage, branding, form, heroSubtitle, heroTitle, loginSubtitle, loginTitle (+2 more)

### Community 101 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 102 - "UserFactory"
Cohesion: 0.43
Nodes (3): UserFactory, Factory, static

### Community 103 - "SidebarMenuSkeleton.vue"
Cohesion: 0.29
Nodes (4): props, width, props, SkeletonProps

### Community 104 - "Index.vue"
Cohesion: 0.31
Nodes (4): BelongsTo, HasMany, ScanGroup, Model

### Community 105 - "TwoFactorChallenge.vue"
Cohesion: 0.29
Nodes (4): authConfigContent, code, form, showRecoveryInput

### Community 106 - "QrScan.php"
Cohesion: 0.20
Nodes (6): AssetKey, assets, BrandingSettings, form, previews, props

### Community 107 - "Draggable.vue"
Cohesion: 0.33
Nodes (3): DraggableProps, draggableRef, props

### Community 108 - "index.ts"
Cohesion: 0.33
Nodes (5): ProvideBalloonBlockEditor, ProvideBalloonEditor, ProvideClassicEditor, ProvideDocumentEditor, ProvideInlineEditor

### Community 109 - "ckeditor.ts"
Cohesion: 0.33
Nodes (4): CkeditorElement, CkeditorEmit, CkeditorProps, init()

### Community 110 - "Checkbox.vue"
Cohesion: 0.33
Nodes (4): delegatedProps, emits, forwarded, props

### Community 111 - "index.ts"
Cohesion: 0.33
Nodes (3): props, forwardedProps, props

### Community 112 - "DashboardOverview1.vue"
Cohesion: 0.17
Nodes (7): BrandingController, RedirectResponse, Request, Response, Setting, self, enableBitrix()

### Community 113 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 114 - "LoadingIcon.vue"
Cohesion: 0.40
Nodes (3): darkMode, iconColor, props

### Community 115 - "Badge.vue"
Cohesion: 0.18
Nodes (8): Props, delegatedProps, props, BadgeVariants, breadcrumbs, { hasSetupData, clearTwoFactorAuthData }, Props, showSetupModal

### Community 117 - "DropdownMenuContent.vue"
Cohesion: 0.25
Nodes (7): attrs, computedClass, emit, InputEmit, InputProps, localValue, props

### Community 118 - "DropdownMenuRadioItem.vue"
Cohesion: 0.33
Nodes (4): form, Marca, props, selectedMarcas

### Community 119 - "DropdownMenuSubContent.vue"
Cohesion: 0.33
Nodes (5): activities, auth, page, recentOrders, stats

### Community 121 - "ProfileValidationRules.php"
Cohesion: 0.36
Nodes (4): ResetUserPassword, PasswordUpdateRequest, PasswordValidationRules, ResetsUserPasswords

### Community 122 - "DropdownMenu.vue"
Cohesion: 0.40
Nodes (4): { as = "div" }, attrs, computedClass, PanelProps

### Community 123 - "DropdownMenuItem.vue"
Cohesion: 0.33
Nodes (3): delegatedProps, props, props

### Community 124 - "DropdownMenuLabel.vue"
Cohesion: 0.50
Nodes (3): delegatedProps, forwardedProps, props

### Community 125 - "DropdownMenuRadioGroup.vue"
Cohesion: 0.40
Nodes (4): { as = "div" }, attrs, ButtonProps, computedClass

### Community 126 - "DropdownMenuSub.vue"
Cohesion: 0.40
Nodes (4): { as = "div" }, attrs, ButtonProps, computedClass

### Community 127 - "DropdownMenuSubTrigger.vue"
Cohesion: 0.40
Nodes (4): { as = "div" }, attrs, computedClass, FooterProps

### Community 128 - "Input.vue"
Cohesion: 0.40
Nodes (4): { as = "a" }, attrs, computedClass, ItemProps

### Community 130 - "autoload-dev"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

### Community 131 - "extra"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

### Community 132 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 149 - "clsx"
Cohesion: 0.40
Nodes (4): { as = "div" }, attrs, computedClass, MenuProps

### Community 151 - "dayjs"
Cohesion: 0.40
Nodes (4): { as = "div" }, attrs, computedClass, PopoverProps

### Community 152 - "@inertiajs/vue3"
Cohesion: 0.40
Nodes (4): delegatedProps, emits, forwarded, props

### Community 153 - "@left4code/tw-starter"
Cohesion: 0.40
Nodes (4): delegatedProps, emits, forwarded, props

### Community 154 - "lodash"
Cohesion: 0.40
Nodes (4): delegatedProps, emits, forwarded, props

### Community 155 - "pinia"
Cohesion: 0.40
Nodes (4): delegatedProps, emits, forwarded, props

### Community 157 - "tailwind-merge"
Cohesion: 0.50
Nodes (3): emits, forwarded, props

### Community 158 - "tailwindcss"
Cohesion: 0.50
Nodes (3): delegatedProps, forwardedProps, props

### Community 159 - "tippy.js"
Cohesion: 0.50
Nodes (3): delegatedProps, forwardedProps, props

### Community 160 - "tw-animate-css"
Cohesion: 0.50
Nodes (3): emits, forwarded, props

### Community 161 - "vue"
Cohesion: 0.50
Nodes (3): emits, forwarded, props

### Community 162 - "vue-input-otp"
Cohesion: 0.50
Nodes (3): emits, modelValue, props

## Knowledge Gaps
- **1187 isolated node(s):** `php`, `E:\Xampp\php\php.exe`, `$schema`, `style`, `config` (+1182 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **28 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `cn()` connect `cn` to `utils.ts`, `index.ts`, `index.ts`, `index.ts`, `@inertiajs/vue3`, `@left4code/tw-starter`, `ResetPassword.vue`, `lodash`, `pinia`, `tailwindcss`, `tippy.js`, `Sidebar.vue`, `vue-input-otp`, `SidebarProvider.vue`, `InputOTPSlot.vue`, `TooltipContent.vue`, `AppSidebar.vue`, `SheetContent.vue`, `Alert.vue`, `index.ts`, `SidebarMenuSkeleton.vue`, `Checkbox.vue`, `Badge.vue`, `DropdownMenuItem.vue`, `DropdownMenuLabel.vue`?**
  _High betweenness centrality (0.020) - this node is a cross-community bridge._
- **Why does `FormCheckComponent` connect `index.ts` to `Scan.vue`, `Edit.vue`, `DropdownMenuCheckboxItem.vue`, `DropdownMenuRadioItem.vue`, `RazeLayout.vue`?**
  _High betweenness centrality (0.017) - this node is a cross-community bridge._
- **Why does `Controller` connect `Controller` to `BitrixSettingController.php`, `Marca`, `DashboardOverview1.vue`, `User.php`, `Role`, `HandleAppearance.php`, `QrScan`?**
  _High betweenness centrality (0.014) - this node is a cross-community bridge._
- **Are the 5 inferred relationships involving `QrScan` (e.g. with `.collection()` and `.edit()`) actually correct?**
  _`QrScan` has 5 INFERRED edges - model-reasoned connections that need verification._
- **What connects `php`, `E:\Xampp\php\php.exe`, `$schema` to the rest of the system?**
  _1187 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Scan.vue` be split into smaller, more focused modules?**
  _Cohesion score 0.05028248587570622 - nodes in this community are weakly interconnected._
- **Should `devDependencies` be split into smaller, more focused modules?**
  _Cohesion score 0.10526315789473684 - nodes in this community are weakly interconnected._
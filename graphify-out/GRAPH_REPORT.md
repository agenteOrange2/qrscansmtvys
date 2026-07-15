# Graph Report - qrscansmtvys  (2026-07-15)

## Corpus Check
- 579 files · ~4,420,216 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2439 nodes · 3248 edges · 284 communities (258 shown, 26 thin omitted)
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
- vue-input-otp
- TextLink.vue
- vue-shims.d.ts
- ziggy.js
- create-vhost.sh
- init-project.sh

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
- `DashboardController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Admin/DashboardController.php → app/Http/Controllers/Controller.php
- `MarcaController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Admin/MarcaController.php → app/Http/Controllers/Controller.php
- `PermissionController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Admin/PermissionController.php → app/Http/Controllers/Controller.php

## Import Cycles
- 1-file cycle: `resources/js/components/Base/Dropzone/Dropzone.vue -> resources/js/components/Base/Dropzone/Dropzone.vue`
- 1-file cycle: `resources/js/components/Base/Dropzone/dropzone.ts -> resources/js/components/Base/Dropzone/dropzone.ts`
- 1-file cycle: `resources/js/components/Base/Litepicker/Litepicker.vue -> resources/js/components/Base/Litepicker/Litepicker.vue`
- 1-file cycle: `resources/js/components/Base/Litepicker/litepicker.ts -> resources/js/components/Base/Litepicker/litepicker.ts`
- 1-file cycle: `resources/js/components/Base/Notification/Notification.vue -> resources/js/components/Base/Notification/Notification.vue`
- 1-file cycle: `resources/js/components/Base/Notification/notification.ts -> resources/js/components/Base/Notification/notification.ts`
- 3-file cycle: `resources/js/components/ui/sidebar/SidebarMenuButton.vue -> resources/js/components/ui/sidebar/SidebarMenuButtonChild.vue -> resources/js/components/ui/sidebar/index.ts -> resources/js/components/ui/sidebar/SidebarMenuButton.vue`

## Communities (284 total, 26 thin omitted)

### Community 0 - "Scan.vue"
Cohesion: 0.09
Nodes (19): estadosMexico, contact, duplicateDialog, empresaGrupo, estadoGrupo, expandedIndex, extraInfo, groupResultDialog (+11 more)

### Community 1 - "devDependencies"
Cohesion: 0.04
Nodes (49): concurrently, eslint, eslint-config-prettier, eslint-import-resolver-typescript, @eslint/js, eslint-plugin-import, eslint-plugin-vue, @laravel/vite-plugin-wayfinder (+41 more)

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
Cohesion: 0.06
Nodes (19): Props, delegatedProps, props, props, props, props, props, props (+11 more)

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
Cohesion: 0.06
Nodes (29): props, SidebarMenuButtonVariants, props, props, props, props, props, props (+21 more)

### Community 8 - "scripts"
Cohesion: 0.05
Nodes (40): scripts, ci:check, dev, dev:ssr, lint, lint:check, post-autoload-dump, post-create-project-cmd (+32 more)

### Community 9 - "index.ts"
Cohesion: 0.04
Nodes (34): MainProps, props, FileIconProps, Variant, { variant, type, src }, { as = "div" }, attrs, ButtonProps (+26 more)

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
Cohesion: 0.07
Nodes (25): { as = "button" }, attrs, ButtonProps, computedClass, disclosure, group, attrs, computedClass (+17 more)

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
Cohesion: 0.11
Nodes (12): HasMany, User, Authenticatable, DatabaseSeeder, RolesAndPermissionsSeeder, HasFactory, HasMedia, HasRoles (+4 more)

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
Cohesion: 0.06
Nodes (27): compactMenu, useCompactMenu(), items, MenuItem, useMenu(), activeIndex, activeMobileMenu, { appearance, updateAppearance } (+19 more)

### Community 23 - "ProfileController.php"
Cohesion: 0.19
Nodes (6): RedirectResponse, Request, Response, UserController, StoreUserRequest, UpdateUserRequest

### Community 24 - "index.ts"
Cohesion: 0.10
Nodes (13): attrs, computedClass, Icon, LucideProps, props, MainProps, props, form (+5 more)

### Community 25 - "index.ts"
Cohesion: 0.08
Nodes (18): DialogComponent, { as = "div" }, attrs, ButtonProps, computedClass, DialogComponent, { as = "div", placement = "bottom-end" }, attrs (+10 more)

### Community 26 - "ResetPassword.vue"
Cohesion: 0.08
Nodes (15): passwordInput, Props, ButtonVariants, delegatedProps, props, props, form, form (+7 more)

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
Cohesion: 0.16
Nodes (8): JsonResponse, RedirectResponse, Request, Response, QrScanController, StoreQrScanRequest, QrScan, BinaryFileResponse

### Community 32 - "useAppearance.ts"
Cohesion: 0.08
Nodes (24): { appearance, updateAppearance }, tabs, appearance, getStoredAppearance(), handleSystemThemeChange(), initializeTheme(), mediaQuery(), prefersDark() (+16 more)

### Community 33 - "Highlight.vue"
Cohesion: 0.10
Nodes (16): attrs, buttonComputedClass, codePreviewComputedClass, copySource, copySourceEl, copyText, highlightComputedClass, HighlightProps (+8 more)

### Community 34 - "Bitrix.vue"
Cohesion: 0.11
Nodes (18): catalogError, extractError(), FailedScan, form, loadingPipelines, loadingStages, loadPipelines(), loadStages() (+10 more)

### Community 35 - "Marca"
Cohesion: 0.20
Nodes (7): MarcaController, RedirectResponse, Response, MarcaRequest, Marca, BelongsToMany, Model

### Community 36 - "TwoFactor.vue"
Cohesion: 0.19
Nodes (14): colors, notify(), NotifyType, resendToBitrix(), buildMarcasPayload(), firstValidationError(), onDetect(), rescanExisting() (+6 more)

### Community 37 - "SidebarProvider.vue"
Cohesion: 0.24
Nodes (9): emits, isMobile, open, openMobile, props, setOpen(), setOpenMobile(), state (+1 more)

### Community 38 - "Bitrix24Service"
Cohesion: 0.20
Nodes (3): Setting, Bitrix24Service, self

### Community 39 - "index.ts"
Cohesion: 0.20
Nodes (8): className, Props, Props, Props, Props, Props, Props, BreadcrumbItem

### Community 40 - "UserQrScanExport"
Cohesion: 0.18
Nodes (10): Collection, UserQrScanExport, Exportable, FromCollection, WithColumnFormatting, WithCustomStartCell, WithHeadings, WithMapping (+2 more)

### Community 41 - "dependencies"
Cohesion: 0.12
Nodes (17): axios, chart.js, @headlessui/vue, laravel-vite-plugin, lucide-vue-next, dependencies, axios, chart.js (+9 more)

### Community 42 - "index.ts"
Cohesion: 0.29
Nodes (5): SlideoverComponent, { as = "div" }, attrs, computedClass, TitleProps

### Community 43 - "Slideover.vue"
Cohesion: 0.22
Nodes (9): attrs, computedClass, emit, handleClose(), open, props, Size, SlideoverProps (+1 more)

### Community 44 - "Litepicker.vue"
Cohesion: 0.11
Nodes (21): attrs, computedClass, emit, InputEmit, InputProps, localValue, props, emit (+13 more)

### Community 45 - "TomSelect.vue"
Cohesion: 0.21
Nodes (13): getOptions(), init(), setValue(), updateValue(), computedOptions, emit, props, ProvideTomSelect (+5 more)

### Community 46 - "Layout.vue"
Cohesion: 0.16
Nodes (11): footerNavItems, mainNavItems, Props, { isCurrentUrl }, currentUrlReactive, page, useCurrentUrl(), UseCurrentUrlReturn (+3 more)

### Community 47 - "SendScanToBitrix.php"
Cohesion: 0.20
Nodes (9): Throwable, Throwable, SendScanToBitrix, Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ShouldQueue (+1 more)

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
Cohesion: 0.25
Nodes (6): colorSchemeStore, imageAssets, setColorSchemeClass(), switchColorScheme(), themeStore, themeSwitcherSlideover

### Community 53 - "Role"
Cohesion: 0.14
Nodes (11): allSelected, confirmDialog, PaginationLink, perPage, props, reload(), resendingId, ScanRow (+3 more)

### Community 54 - "UserController.php"
Cohesion: 0.11
Nodes (6): StoreScanGroupRequest, UpdateQrScanRequest, TwoFactorAuthenticationRequest, FormRequest, InteractsWithTwoFactorState, form

### Community 55 - "Controller"
Cohesion: 0.14
Nodes (11): Controller, PasswordController, RedirectResponse, Response, RedirectResponse, Request, Response, ProfileController (+3 more)

### Community 56 - "Tailwind CSS Development"
Cohesion: 0.13
Nodes (14): Basic Usage, Common Patterns, Common Pitfalls, CSS-First Configuration, Dark Mode, Documentation, Flexbox Layout, Grid Layout (+6 more)

### Community 57 - "Tailwind CSS Development"
Cohesion: 0.13
Nodes (14): Basic Usage, Common Patterns, Common Pitfalls, CSS-First Configuration, Dark Mode, Documentation, Flexbox Layout, Grid Layout (+6 more)

### Community 58 - "TwoFactorSetupModal.vue"
Cohesion: 0.07
Nodes (25): Props, uniqueErrors, isRecoveryCodesVisible, recoveryCodeSectionRef, { recoveryCodesList, fetchRecoveryCodes, errors }, code, { copy, copied }, isOpen (+17 more)

### Community 59 - "Permission"
Cohesion: 0.15
Nodes (9): CreateNewUser, ResetUserPassword, PasswordUpdateRequest, ProfileDeleteRequest, ProfileUpdateRequest, CreatesNewUsers, PasswordValidationRules, ProfileValidationRules (+1 more)

### Community 60 - "InputOTPSlot.vue"
Cohesion: 0.11
Nodes (14): delegatedProps, emits, forwarded, props, delegatedProps, forwarded, props, forwarded (+6 more)

### Community 61 - "TooltipContent.vue"
Cohesion: 0.14
Nodes (9): emits, forwarded, props, delegatedProps, emits, forwarded, props, props (+1 more)

### Community 62 - "Index.vue"
Cohesion: 0.39
Nodes (4): PermissionController, RedirectResponse, Request, Response

### Community 63 - "HandleAppearance.php"
Cohesion: 0.24
Nodes (7): HandleAppearance, Request, Response, HandleInertiaRequests, Request, Closure, Middleware

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
Nodes (5): DashboardController, Request, Response, BelongsTo, BelongsToMany

### Community 68 - "AppHeader.vue"
Cohesion: 0.14
Nodes (9): auth, { isCurrentUrl, whenCurrentUrl }, mainNavItems, page, Props, rightNavItems, Props, props (+1 more)

### Community 69 - "Notification.vue"
Cohesion: 0.27
Nodes (8): init(), NotificationElement, NotificationProps, props, ProvideNotification, reInit(), toastifyRef, vNotificationDirective

### Community 70 - "TinySlider.vue"
Cohesion: 0.24
Nodes (8): init(), reInit(), props, ProvideTinySlider, sliderRef, TinySliderElement, TinySliderProps, vSliderDirective

### Community 71 - "global.d.ts"
Cohesion: 0.22
Nodes (9): Auth, TwoFactorConfigContent, ComponentCustomProperties, ImportMeta, ImportMetaEnv, InertiaConfig, @inertiajs/core, vite/client (+1 more)

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
Cohesion: 0.16
Nodes (11): { isMobile, state }, page, user, { getInitials }, Props, showAvatar, Props, getInitials() (+3 more)

### Community 77 - "require-dev"
Cohesion: 0.20
Nodes (10): require-dev, fakerphp/faker, laravel/boost, laravel/pail, laravel/pint, laravel/sail, mockery/mockery, nunomaduro/collision (+2 more)

### Community 78 - "ActivitiesPanel.vue"
Cohesion: 0.29
Nodes (6): { as = "div" }, attrs, computedClass, PanelProps, slideover, ProvideSlideover

### Community 79 - "AppSidebar.vue"
Cohesion: 0.29
Nodes (3): route, themeComponent, themeStore

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
Cohesion: 0.43
Nodes (6): emptyContact(), parseQrData(), parseVCard(), ScannedContact, vCardValue(), addManual()

### Community 100 - "Edit.vue"
Cohesion: 0.29
Nodes (5): deleteTarget, PaginationLink, props, search, UserRow

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
Cohesion: 0.23
Nodes (5): Collection, SendScanGroupToBitrix, BelongsTo, HasMany, ScanGroup

### Community 106 - "QrScan.php"
Cohesion: 0.11
Nodes (13): entries, NavEntry, page, props, visible, { appearance, updateAppearance }, themes, AssetKey (+5 more)

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
Cohesion: 0.04
Nodes (36): emits, forwarded, props, delegatedProps, emits, forwarded, props, props (+28 more)

### Community 112 - "DashboardOverview1.vue"
Cohesion: 0.36
Nodes (4): BrandingController, RedirectResponse, Request, Response

### Community 113 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 114 - "LoadingIcon.vue"
Cohesion: 0.40
Nodes (3): darkMode, iconColor, props

### Community 115 - "Badge.vue"
Cohesion: 0.50
Nodes (3): delegatedProps, props, BadgeVariants

### Community 118 - "DropdownMenuRadioItem.vue"
Cohesion: 0.40
Nodes (4): { as = "div" }, attrs, computedClass, DescriptionProps

### Community 119 - "DropdownMenuSubContent.vue"
Cohesion: 0.33
Nodes (5): activities, auth, page, recentOrders, stats

### Community 120 - "InputOTP.vue"
Cohesion: 0.40
Nodes (4): { as = "div" }, attrs, computedClass, FooterProps

### Community 121 - "ProfileValidationRules.php"
Cohesion: 0.83
Nodes (3): emailRules(), nameRules(), profileRules()

### Community 123 - "DropdownMenuItem.vue"
Cohesion: 0.33
Nodes (3): delegatedProps, props, props

### Community 130 - "autoload-dev"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

### Community 131 - "extra"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

### Community 132 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 152 - "@inertiajs/vue3"
Cohesion: 0.40
Nodes (4): delegatedProps, emits, forwarded, props

### Community 156 - "reka-ui"
Cohesion: 0.17
Nodes (10): RedirectResponse, Response, RoleController, RoleRequest, down(), up(), down(), up() (+2 more)

### Community 162 - "vue-input-otp"
Cohesion: 0.50
Nodes (3): emits, modelValue, props

## Knowledge Gaps
- **1194 isolated node(s):** `php`, `E:\Xampp\php\php.exe`, `$schema`, `style`, `config` (+1189 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **26 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `cn()` connect `utils.ts` to `Alert.vue`, `vue-input-otp`, `cn`, `SidebarProvider.vue`, `SidebarMenuSkeleton.vue`, `index.ts`, `index.ts`, `SheetContent.vue`, `index.ts`, `Checkbox.vue`, `index.ts`, `Badge.vue`, `@inertiajs/vue3`, `ResetPassword.vue`, `DropdownMenuItem.vue`, `InputOTPSlot.vue`, `TooltipContent.vue`, `Sidebar.vue`?**
  _High betweenness centrality (0.020) - this node is a cross-community bridge._
- **Why does `FormCheckComponent` connect `index.ts` to `Scan.vue`, `useAppearance.ts`, `TwoFactorChallenge.vue`, `DropdownMenuCheckboxItem.vue`, `Role`, `index.ts`?**
  _High betweenness centrality (0.017) - this node is a cross-community bridge._
- **Why does `Controller` connect `Controller` to `BitrixSettingController.php`, `Marca`, `DashboardController.php`, `DashboardOverview1.vue`, `ProfileController.php`, `reka-ui`, `Index.vue`, `QrScan`?**
  _High betweenness centrality (0.014) - this node is a cross-community bridge._
- **Are the 5 inferred relationships involving `QrScan` (e.g. with `.collection()` and `.edit()`) actually correct?**
  _`QrScan` has 5 INFERRED edges - model-reasoned connections that need verification._
- **What connects `php`, `E:\Xampp\php\php.exe`, `$schema` to the rest of the system?**
  _1194 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Scan.vue` be split into smaller, more focused modules?**
  _Cohesion score 0.09090909090909091 - nodes in this community are weakly interconnected._
- **Should `devDependencies` be split into smaller, more focused modules?**
  _Cohesion score 0.04081632653061224 - nodes in this community are weakly interconnected._
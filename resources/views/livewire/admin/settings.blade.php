<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight text-white">Settings</h1>
        <p class="text-slate-400 mt-1">Configure branding, monetization, and the complete dynamic body-color &amp; theme system.</p>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-950/20 border border-emerald-900/30 text-emerald-400 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-rose-950/20 border border-rose-900/30 text-rose-400 rounded-xl text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="saveSettings" class="space-y-8">
        <!-- Site Branding -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl shadow-xl space-y-6">
            <h2 class="text-lg font-bold text-white border-b border-slate-800 pb-3">Website Branding</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Site Name</label>
                    <input wire:model="siteName" type="text"
                        class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-backend-primary focus:border-transparent transition-all"
                        placeholder="My Tech Website" required>
                    @error('siteName') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">SEO Default Meta Description</label>
                    <input wire:model="metaDescription" type="text"
                        class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-backend-primary focus:border-transparent transition-all"
                        placeholder="The ultimate news platform for developers.">
                    @error('metaDescription') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Logo Image</label>
                <input type="file" wire:model="logo" class="mt-1.5 block text-slate-400 text-xs">
                <div class="mt-3">
                    @if ($logo)
                        <span class="text-[10px] text-slate-500 block mb-1">New Logo Preview:</span>
                        <img src="{{ $logo->temporaryUrl() }}" class="h-12 w-auto bg-slate-950 p-2 border border-slate-800 rounded-lg">
                    @elseif ($existingLogo)
                        <span class="text-[10px] text-slate-500 block mb-1">Active Logo:</span>
                        <img src="{{ $existingLogo }}" class="h-12 w-auto bg-slate-950 p-2 border border-slate-800 rounded-lg">
                    @endif
                </div>
                @error('logo') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Multi-Language -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl shadow-xl space-y-6">
            <h2 class="text-lg font-bold text-white border-b border-slate-800 pb-3">Multi-Language &amp; Localization</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Default Site Language</label>
                    <select wire:model="defaultLocale"
                        class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-backend-primary focus:border-transparent transition-all">
                        @foreach($supportedLocales as $locale)
                            <option value="{{ $locale }}">{{ strtoupper($locale) }}</option>
                        @endforeach
                    </select>
                    @error('defaultLocale') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Add Language Code</label>
                    <div class="flex mt-1.5">
                        <input wire:model="newLocale" type="text"
                            class="block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-l-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-backend-primary focus:border-transparent transition-all"
                            placeholder="e.g. es, fr, de">
                        <button type="button" wire:click="addLocale"
                            class="px-4 bg-backend-primary hover:bg-backend-primary-hover text-white rounded-r-lg text-sm font-semibold transition-colors">
                            Add
                        </button>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Active Languages</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($supportedLocales as $locale)
                        <span class="inline-flex items-center px-3 py-1 bg-slate-950 text-indigo-400 border border-indigo-950 rounded-lg text-xs font-semibold">
                            {{ strtoupper($locale) }}
                            @if($locale === $defaultLocale)
                                <span class="ml-1 text-[10px] text-indigo-500 font-bold">(Default)</span>
                            @else
                                <button type="button" wire:click="removeLocale('{{ $locale }}')" class="ml-2 text-slate-500 hover:text-rose-500 font-bold">
                                    &times;
                                </button>
                            @endif
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- AdSense -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl shadow-xl space-y-6">
            <h2 class="text-lg font-bold text-white border-b border-slate-800 pb-3">Google AdSense Integration</h2>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">AdSense Publisher Client ID</label>
                <input wire:model="adsenseClientId" type="text"
                    class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-backend-primary focus:border-transparent transition-all"
                    placeholder="ca-pub-XXXXXXXXXXXXXXXX">
                <p class="text-[10px] text-slate-500 mt-1">Leave empty to disable all banner displays.</p>
                @error('adsenseClientId') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Top Banner Ad Slot ID</label>
                    <input wire:model="adsenseTopSlot" type="text"
                        class="mt-1.5 block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-backend-primary focus:border-transparent transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Sidebar Ad Slot ID</label>
                    <input wire:model="adsenseSidebarSlot" type="text"
                        class="mt-1.5 block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-backend-primary focus:border-transparent transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">In-Article Ad Slot ID</label>
                    <input wire:model="adsenseArticleSlot" type="text"
                        class="mt-1.5 block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-backend-primary focus:border-transparent transition-all">
                </div>
            </div>
        </div>

        <!-- Theme Preset Selector -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl shadow-xl space-y-6">
            <h2 class="text-lg font-bold text-white border-b border-slate-800 pb-3">Theme Presets (Instant Load)</h2>
            <p class="text-xs text-slate-400">Apply a complete design system preset before tweaking individual colors. Selecting will populate the color pickers below; click Save to commit changes.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($presets as $key => $preset)
                    <div wire:click="$set('selectedPreset', '{{ $key }}')"
                         class="group relative cursor-pointer rounded-xl border-2 border-slate-800 hover:border-backend-primary transition-all overflow-hidden bg-slate-950/60 p-1">
                        <div class="rounded-lg p-3 flex flex-col space-y-2"
                             style="background: linear-gradient(135deg, {{ $preset['values']['theme_primary'] ?? '#4f46e5' }}22, {{ $preset['values']['theme_accent'] ?? '#ec4899' }}22);">
                            <div class="flex items-center gap-2">
                                <div class="h-4 w-4 rounded-full" style="background: {{ $preset['values']['theme_primary'] ?? '#4f46e5' }};"></div>
                                <div class="h-4 w-4 rounded-full" style="background: {{ $preset['values']['theme_accent'] ?? '#ec4899' }};"></div>
                                <div class="h-4 w-4 rounded-full" style="background: {{ $preset['values']['theme_body_bg'] ?? '#020617' }}; border: 1px solid rgba(255,255,255,0.1);"></div>
                            </div>
                            <div class="h-2 rounded-full" style="background: {{ $preset['values']['theme_header_bg'] ?? '#ffffff' }};"></div>
                            <div class="h-8 rounded" style="background: {{ $preset['values']['theme_card_bg'] ?? '#111827' }}; border: 1px solid {{ $preset['values']['theme_border_color'] ?? '#334155' }};"></div>
                            <div class="h-2 rounded-full" style="background: {{ $preset['values']['theme_footer_bg'] ?? '#020617' }};"></div>
                        </div>
                        <div class="p-3 pt-2">
                            <div class="text-xs font-bold text-white group-hover:text-backend-primary transition-colors">{{ $preset['name'] }}</div>
                            <div class="text-[10px] text-slate-500 mt-0.5">{{ $key }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Body Colors (dynamic frontpage) -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl shadow-xl space-y-6">
            <h2 class="text-lg font-bold text-white border-b border-slate-800 pb-3">Dynamic Body &amp; Accent Colors</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $colorFields = [
                        ['prop' => 'themePrimary', 'label' => 'Accent / Primary Color', 'var' => '--theme-primary'],
                        ['prop' => 'themePrimaryHover', 'label' => 'Primary Hover', 'var' => '--theme-primary-hover'],
                        ['prop' => 'themeSecondary', 'label' => 'Secondary Accent', 'var' => '--theme-secondary'],
                        ['prop' => 'themeAccent', 'label' => 'Highlight Accent', 'var' => '--theme-accent'],
                        ['prop' => 'themeBodyBg', 'label' => 'Body Background', 'var' => '--theme-body-bg'],
                        ['prop' => 'themeBodyBgAlt', 'label' => 'Body Alt Background', 'var' => '--theme-body-bg-alt'],
                        ['prop' => 'themeBodyText', 'label' => 'Body Text Color', 'var' => '--theme-body-text'],
                        ['prop' => 'themeBodyHeadingColor', 'label' => 'Body Heading Color', 'var' => '--theme-body-heading-color'],
                        ['prop' => 'themeBodyLinkColor', 'label' => 'Body Link Color', 'var' => '--theme-body-link-color'],
                        ['prop' => 'themeBodyLinkHover', 'label' => 'Body Link Hover', 'var' => '--theme-body-link-hover'],
                        ['prop' => 'themeSurfaceBg', 'label' => 'Surface BG', 'var' => '--theme-surface-bg'],
                        ['prop' => 'themeCardBg', 'label' => 'Card Background', 'var' => '--theme-card-bg'],
                        ['prop' => 'themeSectionBg', 'label' => 'Section Background', 'var' => '--theme-section-bg'],
                        ['prop' => 'themeHeaderBg', 'label' => 'Header Background', 'var' => '--theme-header-bg'],
                        ['prop' => 'themeHeaderText', 'label' => 'Header Text Color', 'var' => '--theme-header-text'],
                        ['prop' => 'themeFooterBg', 'label' => 'Footer Background', 'var' => '--theme-footer-bg'],
                        ['prop' => 'themeFooterText', 'label' => 'Footer Text Color', 'var' => '--theme-footer-text'],
                        ['prop' => 'themeSidebarBg', 'label' => 'Sidebar Background', 'var' => '--theme-sidebar-bg'],
                        ['prop' => 'themeSidebarActive', 'label' => 'Sidebar Active', 'var' => '--theme-sidebar-active'],
                        ['prop' => 'themeNavColor', 'label' => 'Navigation Text', 'var' => '--theme-nav-color'],
                        ['prop' => 'themeNavHover', 'label' => 'Nav Hover Color', 'var' => '--theme-nav-hover'],
                        ['prop' => 'themeBorderColor', 'label' => 'Divider / Border', 'var' => '--theme-border-color'],
                    ];
                @endphp
                @foreach($colorFields as $f)
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">{{ $f['label'] }}</label>
                        <div class="flex items-center space-x-2 mt-1.5">
                            <input wire:model.live="{{ $f['prop'] }}" type="color"
                                class="h-10 w-12 bg-slate-950 border border-slate-800 rounded-lg p-1 cursor-pointer"
                                style="color-scheme: dark;">
                            <input wire:model.live="{{ $f['prop'] }}" type="text"
                                class="block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white uppercase text-xs focus:outline-none focus:ring-2 focus:ring-backend-primary focus:border-transparent transition-all font-mono">
                        </div>
                        @error($f['prop']) <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Dark Mode -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl shadow-xl space-y-6">
            <h2 class="text-lg font-bold text-white border-b border-slate-800 pb-3">Dark Mode &amp; Override Palette</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Dark Mode Strategy</label>
                    <select wire:model="themeDarkMode"
                        class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-backend-primary">
                        <option value="auto">Auto (System)</option>
                        <option value="light">Always Light</option>
                        <option value="dark">Always Dark</option>
                    </select>
                </div>
                @php
                    $darkFields = [
                        ['prop' => 'themeDarkBodyBg', 'label' => 'Dark Body BG'],
                        ['prop' => 'themeDarkBodyText', 'label' => 'Dark Body Text'],
                        ['prop' => 'themeDarkSurfaceBg', 'label' => 'Dark Surface BG'],
                        ['prop' => 'themeDarkCardBg', 'label' => 'Dark Card BG'],
                    ];
                @endphp
                @foreach($darkFields as $f)
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">{{ $f['label'] }}</label>
                        <div class="flex items-center space-x-2 mt-1.5">
                            <input wire:model.live="{{ $f['prop'] }}" type="color"
                                class="h-10 w-12 bg-slate-950 border border-slate-800 rounded-lg p-1 cursor-pointer" style="color-scheme: dark;">
                            <input wire:model.live="{{ $f['prop'] }}" type="text"
                                class="block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white uppercase text-xs focus:outline-none focus:ring-2 focus:ring-backend-primary font-mono">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Typography & Radius -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl shadow-xl space-y-6">
            <h2 class="text-lg font-bold text-white border-b border-slate-800 pb-3">Typography, Radius &amp; Card Styling</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Heading Font Stack</label>
                    <input wire:model="themeFontHeading" type="text"
                        class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white text-xs focus:outline-none focus:ring-2 focus:ring-backend-primary font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Body Font Stack</label>
                    <input wire:model="themeFontBody" type="text"
                        class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white text-xs focus:outline-none focus:ring-2 focus:ring-backend-primary font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Base Font Size</label>
                    <input wire:model="themeFontSizeBase" type="text"
                        class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white text-xs focus:outline-none focus:ring-2 focus:ring-backend-primary font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Line Height Base</label>
                    <input wire:model="themeLineHeightBase" type="text"
                        class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white text-xs focus:outline-none focus:ring-2 focus:ring-backend-primary font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Button Radius</label>
                    <input wire:model="themeButtonRadius" type="text"
                        class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white text-xs focus:outline-none focus:ring-2 focus:ring-backend-primary font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Card Radius</label>
                    <input wire:model="themeCardRadius" type="text"
                        class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white text-xs focus:outline-none focus:ring-2 focus:ring-backend-primary font-mono">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Card Shadow (default)</label>
                    <input wire:model="themeCardShadow" type="text"
                        class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white text-xs focus:outline-none focus:ring-2 focus:ring-backend-primary font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Card Hover Shadow</label>
                    <input wire:model="themeCardHoverShadow" type="text"
                        class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white text-xs focus:outline-none focus:ring-2 focus:ring-backend-primary font-mono">
                </div>
            </div>
        </div>

        <!-- Forms -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl shadow-xl space-y-6">
            <h2 class="text-lg font-bold text-white border-b border-slate-800 pb-3">Form / Input Styling</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $formFields = [
                        ['prop' => 'themeFormInputBg', 'label' => 'Input Background'],
                        ['prop' => 'themeFormInputBorder', 'label' => 'Input Border'],
                        ['prop' => 'themeFormPlaceholder', 'label' => 'Placeholder Color'],
                        ['prop' => 'themeFormFocusBorder', 'label' => 'Focus Border (ring)'],
                        ['prop' => 'themeFormLabel', 'label' => 'Label Color'],
                        ['prop' => 'themeFormRadius', 'label' => 'Form Radius'],
                    ];
                @endphp
                @foreach($formFields as $f)
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">{{ $f['label'] }}</label>
                        <div class="flex items-center space-x-2 mt-1.5">
                            @if(in_array($f['prop'], ['themeFormRadius']))
                                <input wire:model.live="{{ $f['prop'] }}" type="text"
                                    class="block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white uppercase text-xs focus:outline-none focus:ring-2 focus:ring-backend-primary font-mono">
                            @else
                                <input wire:model.live="{{ $f['prop'] }}" type="color"
                                    class="h-10 w-12 bg-slate-950 border border-slate-800 rounded-lg p-1 cursor-pointer" style="color-scheme: dark;">
                                <input wire:model.live="{{ $f['prop'] }}" type="text"
                                    class="block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white uppercase text-xs focus:outline-none focus:ring-2 focus:ring-backend-primary font-mono">
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Admin Backend Theme -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl shadow-xl space-y-6">
            <h2 class="text-lg font-bold text-white border-b border-slate-800 pb-3">Admin / Control Center Theme</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $adminFields = [
                        ['prop' => 'themeBackendPrimary', 'label' => 'Backend Primary'],
                        ['prop' => 'themeBackendPrimaryHover', 'label' => 'Backend Primary Hover'],
                        ['prop' => 'themeAdminBodyBg', 'label' => 'Admin Body BG'],
                        ['prop' => 'themeAdminBodyText', 'label' => 'Admin Body Text'],
                        ['prop' => 'themeAdminSidebarBg', 'label' => 'Admin Sidebar BG'],
                        ['prop' => 'themeAdminSidebarText', 'label' => 'Admin Sidebar Text'],
                        ['prop' => 'themeAdminSidebarActive', 'label' => 'Sidebar Active'],
                        ['prop' => 'themeAdminCardsBg', 'label' => 'Admin Cards BG'],
                        ['prop' => 'themeAdminFormsBg', 'label' => 'Admin Forms BG'],
                    ];
                @endphp
                @foreach($adminFields as $f)
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">{{ $f['label'] }}</label>
                        <div class="flex items-center space-x-2 mt-1.5">
                            <input wire:model.live="{{ $f['prop'] }}" type="color"
                                class="h-10 w-12 bg-slate-950 border border-slate-800 rounded-lg p-1 cursor-pointer" style="color-scheme: dark;">
                            <input wire:model.live="{{ $f['prop'] }}" type="text"
                                class="block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white uppercase text-xs focus:outline-none focus:ring-2 focus:ring-backend-primary font-mono">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Live Preview Panel -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl shadow-xl space-y-4">
            <h2 class="text-lg font-bold text-white border-b border-slate-800 pb-3">Live Theme Preview</h2>
            <div class="rounded-lg overflow-hidden border border-slate-800 shadow-2xl">
                <div class="px-4 py-2 text-[10px] uppercase tracking-wider font-bold text-white flex items-center gap-2"
                     style="background: {{ $themeHeaderBg }}; color: {{ $themeHeaderText }};">
                    <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                    <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    <span class="ml-2 opacity-70">preview.{{ strtolower(explode(' ', $siteName ?: 'site')[0]) }}.com</span>
                </div>
                <div class="p-5 space-y-3"
                     style="background: {{ $themeBodyBg }}; color: {{ $themeBodyText }};">
                    <div class="flex items-center justify-between mb-2">
                        <div class="font-bold text-lg" style="color: {{ $themeBodyHeadingColor }}; font-family: {{ $themeFontHeading }};">
                            {{ $siteName ?: 'Site Title' }}
                        </div>
                        <div class="flex gap-3 text-xs font-semibold" style="color: {{ $themeNavColor }};">
                            <span style="color: {{ $themePrimary }};">Home</span>
                            <span>News</span>
                            <span>Tools</span>
                            <span>About</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2 p-3 rounded"
                             style="background: {{ $themeCardBg }}; border: 1px solid {{ $themeBorderColor }}; border-radius: {{ $themeCardRadius }}; box-shadow: {{ $themeCardShadow }};">
                            <span class="text-[9px] font-bold uppercase tracking-wider" style="color: {{ $themePrimary }};">Breaking News</span>
                            <h3 class="mt-1 font-bold text-sm" style="color: {{ $themeBodyHeadingColor }}; font-family: {{ $themeFontHeading }};">
                                Headline: Dynamic Body Colors are Live!
                            </h3>
                            <p class="mt-1 text-[11px] leading-relaxed" style="color: {{ $themeBodyText }}; opacity: 0.85;">
                                This preview reflects the body background, card styling, heading colors, and link colors you
                                just configured. Click save to publish to the production front pages.
                            </p>
                            <a class="mt-2 inline-block text-[10px] font-bold" style="color: {{ $themeBodyLinkColor }};">Read more &rarr;</a>
                        </div>
                        <div class="p-3 space-y-2 rounded"
                             style="background: {{ $themeSectionBg }}; border: 1px solid {{ $themeBorderColor }}; border-radius: {{ $themeCardRadius }};">
                            <div class="text-[9px] font-bold uppercase tracking-wider opacity-70">Trending</div>
                            <div class="text-[11px] font-semibold" style="color: {{ $themeBodyHeadingColor }};">• Category Posts</div>
                            <div class="text-[11px] font-semibold" style="color: {{ $themeBodyHeadingColor }};">• Subcategory Topics</div>
                            <div class="text-[11px] font-semibold" style="color: {{ $themeBodyHeadingColor }};">• Related Stories</div>
                            <button class="w-full mt-2 py-1.5 text-[10px] font-bold text-white rounded"
                                    style="background: {{ $themePrimary }}; border-radius: {{ $themeButtonRadius }};">
                                View All
                            </button>
                        </div>
                    </div>
                    <div class="px-3 py-2 text-[9px] rounded opacity-80"
                         style="background: {{ $themeFooterBg }}; color: {{ $themeFooterText }}; border-radius: {{ $themeCardRadius }};">
                        &copy; {{ date('Y') }} {{ $siteName ?: 'Site' }} — Footer preview
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit"
                class="px-6 py-3 bg-backend-primary hover:bg-backend-primary-hover rounded-lg text-white font-semibold transition-colors shadow-lg shadow-backend-primary/10">
                Save Site Settings
            </button>
        </div>
    </form>
</div>

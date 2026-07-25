<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold tracking-tight" style="color: var(--theme-admin-body-text);">Subcategories</h1>
            <p class="mt-1" style="color: var(--theme-form-label);">Nest subcategories under categories and link them to posts.</p>
        </div>
        <button wire:click="toggleCreate"
            class="px-4 py-2.5 rounded-theme-btn text-sm font-semibold text-white transition-all shadow-lg"
            style="background-color: var(--theme-backend-primary);">
            {{ $isCreating ? 'Back to List' : 'Create Subcategory' }}
        </button>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 rounded-xl text-sm font-medium"
             style="background-color: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981;">
            {{ session('message') }}
        </div>
    @endif

    @if($isCreating)
        <!-- FORM PANEL -->
        <div class="p-6 rounded-2xl shadow-xl lg:col-span-2"
             style="background-color: var(--theme-admin-cards-bg); border: 1px solid var(--theme-border-color);">
            <h2 class="text-xl font-bold mb-6" style="color: var(--theme-admin-body-text);">
                {{ $editingSubcategoryId ? 'Edit Subcategory' : 'Create Subcategory' }}
            </h2>

            <form wire:submit.prevent="saveSubcategory" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider" style="color: var(--theme-form-label);">Parent Category</label>
                        <select wire:model.live="category_id"
                                class="mt-1.5 block w-full px-4 py-2.5 rounded-theme-form transition-all"
                                style="background-color: var(--theme-admin-forms-bg); border: 1px solid var(--theme-form-input-border); color: var(--theme-admin-body-text);">
                            <option value="">-- None / Uncategorized --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">
                                    {{ $cat->name[app()->getLocale()] ?? reset($cat->name) }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-xs mt-1 block font-medium" style="color: var(--theme-form-error);">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider" style="color: var(--theme-form-label);">Slug</label>
                        <input wire:model="slug" type="text" required
                               class="mt-1.5 block w-full px-4 py-2.5 rounded-theme-form transition-all"
                               style="background-color: var(--theme-admin-forms-bg); border: 1px solid var(--theme-form-input-border); color: var(--theme-admin-body-text);"
                               placeholder="ai-tools">
                        @error('slug') <span class="text-xs mt-1 block font-medium" style="color: var(--theme-form-error);">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider" style="color: var(--theme-form-label);">Status</label>
                        <select wire:model="is_active"
                                class="mt-1.5 block w-full px-4 py-2.5 rounded-theme-form transition-all"
                                style="background-color: var(--theme-admin-forms-bg); border: 1px solid var(--theme-form-input-border); color: var(--theme-admin-body-text);">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider" style="color: var(--theme-form-label);">Sort Order (0-10000)</label>
                        <input wire:model.live="sort_order" type="number" min="0" max="10000"
                               class="mt-1.5 block w-full px-4 py-2.5 rounded-theme-form transition-all"
                               style="background-color: var(--theme-admin-forms-bg); border: 1px solid var(--theme-form-input-border); color: var(--theme-admin-body-text);">
                    </div>
                </div>

                <div class="news-divider"></div>
                <h6 class="font-bold uppercase tracking-wider text-xs pb-2" style="color: var(--theme-backend-primary);">Localized Content</h6>

                @foreach($supportedLocales as $locale)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider" style="color: var(--theme-form-label);">Name ({{ strtoupper($locale) }})</label>
                            <input wire:model.live="names.{{ $locale }}" type="text" required
                                   class="mt-1.5 block w-full px-4 py-2.5 rounded-theme-form transition-all"
                                   style="background-color: var(--theme-admin-forms-bg); border: 1px solid var(--theme-form-input-border); color: var(--theme-admin-body-text);"
                                   placeholder="Machine Learning">
                            @error('names.' . $locale) <span class="text-xs mt-1 block font-medium" style="color: var(--theme-form-error);">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider" style="color: var(--theme-form-label);">Short Description ({{ strtoupper($locale) }})</label>
                            <textarea wire:model.live="descriptions.{{ $locale }}" rows="2"
                                      class="mt-1.5 block w-full px-4 py-2.5 rounded-theme-form transition-all"
                                      style="background-color: var(--theme-admin-forms-bg); border: 1px solid var(--theme-form-input-border); color: var(--theme-admin-body-text);"></textarea>
                        </div>
                    </div>
                @endforeach

                <div class="news-divider"></div>
                <h6 class="font-bold uppercase tracking-wider text-xs pb-2" style="color: var(--theme-backend-primary);">SEO Meta</h6>
                @foreach($supportedLocales as $locale)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider" style="color: var(--theme-form-label);">Meta Title ({{ strtoupper($locale) }})</label>
                            <input wire:model.live="meta_titles.{{ $locale }}" type="text"
                                   class="mt-1.5 block w-full px-4 py-2.5 rounded-theme-form transition-all"
                                   style="background-color: var(--theme-admin-forms-bg); border: 1px solid var(--theme-form-input-border); color: var(--theme-admin-body-text);">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider" style="color: var(--theme-form-label);">Meta Description ({{ strtoupper($locale) }})</label>
                            <textarea wire:model.live="meta_descriptions.{{ $locale }}" rows="2"
                                      class="mt-1.5 block w-full px-4 py-2.5 rounded-theme-form transition-all"
                                      style="background-color: var(--theme-admin-forms-bg); border: 1px solid var(--theme-form-input-border); color: var(--theme-admin-body-text);"></textarea>
                        </div>
                    </div>
                @endforeach

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider" style="color: var(--theme-form-label);">Featured Image</label>
                    @if($existing_featured_image)
                        <div class="mt-2 mb-2 flex items-center gap-3">
                            <img src="{{ $existing_featured_image }}" class="h-16 w-24 object-cover rounded-theme-card border" style="border-color: var(--theme-border-color);">
                            <span class="text-xs" style="color: var(--theme-form-label);">Existing uploaded</span>
                        </div>
                    @endif
                    <input wire:model="featured_image" type="file" accept="image/*"
                           class="mt-1.5 block w-full text-sm rounded-theme-form px-3 py-2"
                           style="background-color: var(--theme-admin-forms-bg); border: 1px solid var(--theme-form-input-border); color: var(--theme-admin-body-text);">
                    @error('featured_image') <span class="text-xs mt-1 block font-medium" style="color: var(--theme-form-error);">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" wire:click="toggleCreate"
                            class="px-4 py-2 rounded-theme-btn font-semibold transition-colors"
                            style="background-color: var(--theme-admin-forms-bg); border: 1px solid var(--theme-form-input-border); color: var(--theme-form-label);">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-white font-semibold rounded-theme-btn transition-colors"
                            style="background-color: var(--theme-backend-primary);">
                        Save Subcategory
                    </button>
                </div>
            </form>
        </div>
    @else
        <!-- TABLE PANEL -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input wire:model.live.debounce.300ms="searchTerm" type="search"
                       placeholder="Search by name or slug..."
                       class="block w-full px-4 py-2.5 rounded-theme-form text-sm transition-all"
                       style="background-color: var(--theme-admin-forms-bg); border: 1px solid var(--theme-form-input-border); color: var(--theme-admin-body-text);">
            </div>
            <div>
                <select wire:model.live="filterCategoryId"
                        class="block w-full px-4 py-2.5 rounded-theme-form text-sm transition-all"
                        style="background-color: var(--theme-admin-forms-bg); border: 1px solid var(--theme-form-input-border); color: var(--theme-admin-body-text);">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">
                            {{ $cat->name[app()->getLocale()] ?? reset($cat->name) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="rounded-2xl overflow-hidden shadow-xl"
             style="background-color: var(--theme-admin-cards-bg); border: 1px solid var(--theme-border-color);">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b text-xs font-semibold uppercase tracking-wider"
                            style="border-color: var(--theme-border-color); color: var(--theme-form-label); background-color: var(--theme-admin-forms-bg);">
                            <th class="px-6 py-4">Name</th>
                            <th class="px-6 py-4">Category</th>
                            <th class="px-6 py-4">Slug</th>
                            <th class="px-6 py-4">Posts</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="--tw-divide-opacity: 1; border-color: var(--theme-border-color);">
                        @forelse($subcategories as $sc)
                            <tr class="hover:bg-slate-900/10 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-semibold" style="color: var(--theme-admin-body-text);">
                                        {{ $sc->name[app()->getLocale()] ?? reset($sc->name) }}
                                    </div>
                                    <div class="flex flex-wrap gap-1 mt-1 text-[10px]">
                                        @foreach($sc->name as $lang => $t)
                                            <span class="px-1.5 py-0.5 rounded uppercase"
                                                  style="background-color: var(--theme-admin-forms-bg); color: var(--theme-form-label); border: 1px solid var(--theme-border-color);">
                                                {{ $lang }}: {{ $t }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm" style="color: var(--theme-form-label);">
                                    @if($sc->category)
                                        <span class="font-semibold" style="color: var(--theme-backend-primary);">
                                            {{ $sc->category->name[app()->getLocale()] ?? reset($sc->category->name) }}
                                        </span>
                                    @else
                                        <em>—</em>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm" style="color: var(--theme-admin-body-text);">
                                    <code>{{ $sc->slug }}</code>
                                </td>
                                <td class="px-6 py-4 text-sm" style="color: var(--theme-form-label);">
                                    {{ $sc->posts()->count() }}
                                </td>
                                <td class="px-6 py-4">
                                    <button wire:click="toggleActive({{ $sc->id }})"
                                            class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide"
                                            style="background-color: {{ $sc->is_active ? 'rgba(16,185,129,0.15)' : 'rgba(100,116,139,0.15)' }}; color: {{ $sc->is_active ? '#10b981' : '#64748b' }};">
                                        {{ $sc->is_active ? 'ACTIVE' : 'OFF' }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-right space-x-3 text-sm font-medium">
                                    <button wire:click="editSubcategory({{ $sc->id }})"
                                            class="font-semibold transition-colors"
                                            style="color: var(--theme-backend-primary);">
                                        Edit
                                    </button>
                                    <button onclick="confirm('Delete this subcategory? Linked posts will become unlinked from this subcategory.') || event.stopImmediatePropagation()"
                                            wire:click="deleteSubcategory({{ $sc->id }})"
                                            class="font-semibold transition-colors"
                                            style="color: var(--theme-form-error);">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center" style="color: var(--theme-form-label);">
                                    No subcategories yet. Click <strong>"Create Subcategory"</strong> to organize your content.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

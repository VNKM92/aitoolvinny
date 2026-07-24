<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-white">Form Builder</h1>
            <p class="text-slate-400 mt-1">Create custom contact forms and view user submissions.</p>
        </div>
        <button wire:click="toggleCreate" 
            class="px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 transition-colors shadow-lg shadow-indigo-600/10">
            {{ $isCreating ? 'Back to List' : 'Create Form' }}
        </button>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-950/20 border border-emerald-900/30 text-emerald-400 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    @if($isCreating)
        <!-- Form Builder Canvas -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Canvas Editor -->
            <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl shadow-xl lg:col-span-2 space-y-6">
                <h2 class="text-xl font-bold text-white">
                    {{ $editingFormId ? 'Edit Custom Form' : 'Create Custom Form' }}
                </h2>

                <form wire:submit.prevent="saveForm" class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Form Name</label>
                        <input wire:model="name" type="text" 
                            class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                            placeholder="Contact Us Form" required>
                        @error('name') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Fields list preview -->
                    <div class="space-y-3 pt-4 border-t border-slate-800">
                        <h3 class="text-sm font-bold text-slate-300">Form Fields (Preview)</h3>
                        @forelse($fields as $index => $field)
                            <div class="flex items-center justify-between p-3.5 bg-slate-950 border border-slate-850 rounded-xl">
                                <div>
                                    <div class="text-xs font-semibold text-white">{{ $field['label'] }} <span class="text-[10px] text-slate-500">({{ $field['name'] }})</span></div>
                                    <div class="text-[10px] text-slate-500 mt-0.5">Type: <span class="uppercase font-bold text-indigo-400">{{ $field['type'] }}</span> &bull; Required: {{ $field['required'] ? 'Yes' : 'No' }}</div>
                                </div>
                                <button type="button" wire:click="removeField({{ $index }})" class="text-xs font-semibold text-rose-500 hover:text-rose-455">
                                    Remove
                                </button>
                            </div>
                        @empty
                            <p class="text-xs text-slate-500 py-4 text-center">No fields added to this form yet. Use the configurator to add fields.</p>
                        @endforelse
                        @error('fields') <span class="text-xs text-rose-500 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-slate-800">
                        <button type="button" wire:click="toggleCreate" 
                            class="px-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-slate-400 hover:text-white transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-white font-semibold transition-colors">
                            Save Form Layout
                        </button>
                    </div>
                </form>
            </div>

            <!-- Field Configurator Drawer -->
            <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl shadow-xl h-fit">
                <h3 class="text-md font-bold text-white mb-4">Add Form Field</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Field Label</label>
                        <input wire:model.live="field_label" type="text" 
                            class="mt-1.5 block w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white text-xs" 
                            placeholder="Full Name">
                        @error('field_label') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Field KeyName (Alpha Numeric)</label>
                        <input wire:model="field_name" type="text" 
                            class="mt-1.5 block w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white text-xs" 
                            placeholder="fullname">
                        @error('field_name') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Field Type</label>
                        <select wire:model="field_type" 
                            class="mt-1.5 block w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white text-xs">
                            <option value="text">Single Line Text</option>
                            <option value="email">Email Address</option>
                            <option value="number">Numeric Input</option>
                            <option value="textarea">Large Text Area</option>
                        </select>
                        @error('field_type') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center pt-2">
                        <label class="flex items-center cursor-pointer select-none">
                            <input type="checkbox" wire:model="field_required" class="sr-only peer">
                            <div class="w-4 h-4 bg-slate-950 border border-slate-800 rounded flex items-center justify-center peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all mr-2">
                                <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-xs text-slate-400 font-medium">Field is Required</span>
                        </label>
                    </div>

                    <button type="button" wire:click="addField" 
                        class="w-full py-2 bg-slate-900 border border-slate-800 hover:border-slate-700 text-indigo-400 hover:text-white rounded-lg text-xs font-semibold transition-colors mt-4">
                        + Append Field to Form
                    </button>
                </div>
            </div>
        </div>
    @elseif($viewingFormId)
        <!-- Submissions Explorer -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl shadow-xl space-y-6">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <h2 class="text-xl font-bold text-white">Form Submissions</h2>
                <button wire:click="$set('viewingFormId', null)" class="text-xs font-semibold text-indigo-400 hover:underline">
                    &larr; Back to Forms
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 font-semibold uppercase tracking-wider bg-slate-950/40">
                            <th class="px-6 py-4">Submitted Data</th>
                            <th class="px-6 py-4">Submission Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($submissions as $sub)
                            <tr class="hover:bg-slate-900/20 transition-colors">
                                <td class="px-6 py-4 space-y-1">
                                    @foreach($sub->data as $key => $val)
                                        <div>
                                            <span class="font-bold text-slate-400 uppercase tracking-wide text-[9px]">{{ $key }}:</span> 
                                            <span class="text-white">{{ $val }}</span>
                                        </div>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 text-slate-500 font-medium">
                                    {{ $sub->created_at->format('M d, Y @ H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-8 text-center text-slate-500">
                                    No submissions received for this form yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Forms list -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 text-xs font-semibold uppercase tracking-wider bg-slate-950/40">
                            <th class="px-6 py-4">Form Name</th>
                            <th class="px-6 py-4">Layout Fields</th>
                            <th class="px-6 py-4">Responses</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($forms as $form)
                            <tr class="hover:bg-slate-900/20 transition-colors">
                                <td class="px-6 py-4 text-sm font-semibold text-white">
                                    {{ $form->name }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($form->fields as $field)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] bg-slate-950 border border-slate-850 text-slate-450 uppercase">
                                                {{ $field['label'] }} ({{ $field['type'] }})
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-300">
                                    {{ $form->submissions()->count() }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-3 text-sm font-medium">
                                    <button wire:click="viewSubmissions({{ $form->id }})" 
                                        class="text-emerald-400 hover:text-emerald-350 font-semibold transition-colors">
                                        View Responses
                                    </button>
                                    <button wire:click="editForm({{ $form->id }})" 
                                        class="text-indigo-400 hover:text-indigo-300 font-semibold transition-colors">
                                        Edit Layout
                                    </button>
                                    <button onclick="confirm('Delete this form and all responses permanently?') || event.stopImmediatePropagation()"
                                        wire:click="deleteForm({{ $form->id }})" 
                                        class="text-rose-500 hover:text-rose-455 font-semibold transition-colors">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                    No custom forms created yet. Click "Create Form" to design one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

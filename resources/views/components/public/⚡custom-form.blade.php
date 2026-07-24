<?php

use Livewire\Component;
use App\Models\Form;
use App\Models\FormSubmission;

new class extends Component
{
    public int $formId;
    public string $formName = '';
    public array $fields = [];
    
    // Dynamic input container
    public array $inputData = [];

    public function mount(int $formId)
    {
        $this->formId = $formId;
        $form = Form::findOrFail($formId);
        $this->formName = $form->name;
        $this->fields = $form->fields;

        foreach ($this->fields as $field) {
            $this->inputData[$field['name']] = '';
        }
    }

    public function submit()
    {
        // Construct dynamic validation rules
        $rules = [];
        $messages = [];
        
        foreach ($this->fields as $field) {
            $rule = [];
            $rule[] = $field['required'] ? 'required' : 'nullable';
            
            if ($field['type'] === 'email') {
                $rule[] = 'email';
            }
            if ($field['type'] === 'number') {
                $rule[] = 'numeric';
            }
            
            $rules['inputData.' . $field['name']] = implode('|', $rule);
            $messages['inputData.' . $field['name'] . '.required'] = "The {$field['label']} field is required.";
        }

        $this->validate($rules, $messages);

        FormSubmission::create([
            'form_id' => $this->formId,
            'data' => $this->inputData,
        ]);

        // Reset inputs
        foreach ($this->fields as $field) {
            $this->inputData[$field['name']] = '';
        }

        session()->flash('form_success_' . $this->formId, 'Form response submitted successfully!');
    }
};
?>

<div class="backdrop-blur-md bg-slate-900/40 border border-slate-900 p-6 rounded-2xl">
    <h3 class="text-sm font-bold text-white mb-4">{{ $formName }}</h3>

    @if(session()->has('form_success_' . $formId))
        <div class="p-3 bg-emerald-950/20 border border-emerald-900/30 text-emerald-400 rounded-lg text-xs font-semibold">
            {{ session('form_success_' . $formId) }}
        </div>
    @else
        <form wire:submit.prevent="submit" class="space-y-4">
            @foreach($fields as $field)
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                        {{ $field['label'] }} @if($field['required']) <span class="text-rose-500">*</span> @endif
                    </label>
                    
                    @if($field['type'] === 'textarea')
                        <textarea wire:model="inputData.{{ $field['name'] }}" rows="4" 
                            class="mt-1 block w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="Enter {{ strtolower($field['label']) }}..." @if($field['required']) required @endif></textarea>
                    @else
                        <input wire:model="inputData.{{ $field['name'] }}" type="{{ $field['type'] }}" 
                            class="mt-1 block w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="Enter {{ strtolower($field['label']) }}..." @if($field['required']) required @endif>
                    @endif
                    
                    @error('inputData.' . $field['name']) 
                        <span class="text-[10px] text-rose-500 font-medium mt-1 block">{{ $message }}</span> 
                    @enderror
                </div>
            @endforeach

            <button type="submit" 
                class="px-4 py-2 bg-indigo-650 hover:bg-indigo-550 rounded-lg text-xs font-semibold text-white transition-colors shadow-lg shadow-indigo-600/10">
                Submit Response
            </button>
        </form>
    @endif
</div>
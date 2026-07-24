<?php

namespace App\Livewire\Admin;

use App\Models\Form;
use App\Models\FormSubmission;
use App\Services\ActivityLogger;
use Livewire\Component;

class Forms extends Component
{
    // Form Creation Properties
    public string $name = '';
    public array $fields = []; // [{"name": "fullname", "label": "Full Name", "type": "text", "required": true}]
    public bool $isCreating = false;

    // Field Configurator Inputs
    public string $field_name = '';
    public string $field_label = '';
    public string $field_type = 'text';
    public bool $field_required = true;

    // Submission explorer properties
    public ?int $viewingFormId = null;
    public $submissions = [];

    // Edit Mode
    public ?int $editingFormId = null;

    protected array $rules = [
        'name' => 'required|string|max:255',
        'fields' => 'required|array|min:1',
    ];

    public function toggleCreate()
    {
        $this->isCreating = !$this->isCreating;
        $this->viewingFormId = null;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->fields = [];
        $this->editingFormId = null;
        $this->resetFieldConfigurator();
    }

    private function resetFieldConfigurator()
    {
        $this->field_name = '';
        $this->field_label = '';
        $this->field_type = 'text';
        $this->field_required = true;
    }

    public function addField()
    {
        $this->validate([
            'field_name' => 'required|string|alpha_dash|max:50',
            'field_label' => 'required|string|max:100',
            'field_type' => 'required|in:text,email,textarea,number',
            'field_required' => 'boolean',
        ]);

        // Add to fields array
        $this->fields[] = [
            'name' => trim(strtolower($this->field_name)),
            'label' => $this->field_label,
            'type' => $this->field_type,
            'required' => $this->field_required,
        ];

        $this->resetFieldConfigurator();
    }

    public function removeField(int $index)
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
    }

    public function saveForm()
    {
        $this->validate();

        if ($this->editingFormId) {
            $form = Form::findOrFail($this->editingFormId);
            $form->update([
                'name' => $this->name,
                'fields' => $this->fields,
            ]);
            ActivityLogger::log('form_updated', "Updated custom form: {$this->name}");
            session()->flash('message', 'Form updated successfully.');
        } else {
            Form::create([
                'name' => $this->name,
                'fields' => $this->fields,
            ]);
            ActivityLogger::log('form_created', "Created custom form: {$this->name}");
            session()->flash('message', 'Form builder layout saved.');
        }

        $this->isCreating = false;
        $this->resetInputFields();
    }

    public function editForm(int $id)
    {
        $form = Form::findOrFail($id);
        $this->editingFormId = $id;
        $this->name = $form->name;
        $this->fields = $form->fields;
        $this->isCreating = true;
        $this->viewingFormId = null;
    }

    public function viewSubmissions(int $id)
    {
        $form = Form::findOrFail($id);
        $this->viewingFormId = $id;
        $this->submissions = $form->submissions()->orderBy('id', 'desc')->get();
        $this->isCreating = false;
    }

    public function deleteForm(int $id)
    {
        $form = Form::findOrFail($id);
        $form->delete();
        ActivityLogger::log('form_deleted', "Deleted custom form: {$form->name}");
        session()->flash('message', 'Form and all submissions deleted successfully.');
        
        if ($this->viewingFormId === $id) {
            $this->viewingFormId = null;
        }
    }

    public function render()
    {
        $forms = Form::orderBy('id', 'desc')->get();
        return view('livewire.admin.forms', compact('forms'))
            ->layout('components.layouts.admin');
    }
}

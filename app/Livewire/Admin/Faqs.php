<?php

namespace App\Livewire\Admin;

use App\Models\Faq;
use App\Services\ActivityLogger;
use App\Services\TenantManager;
use Livewire\Component;

class Faqs extends Component
{
    public array $questions = []; // [locale => question]
    public array $answers = []; // [locale => answer]
    public int $order = 0;

    public ?int $editingFaqId = null;
    public bool $isCreating = false;

    public array $supportedLocales = [];

    protected function rules(): array
    {
        return [
            'questions.*' => 'required|string|max:255',
            'answers.*' => 'required|string',
            'order' => 'required|integer',
        ];
    }

    public function mount()
    {
        $this->supportedLocales = \App\Services\SiteSettings::get('supported_locales', ['en']);
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->questions = [];
        $this->answers = [];
        foreach ($this->supportedLocales as $locale) {
            $this->questions[$locale] = '';
            $this->answers[$locale] = '';
        }
        $this->order = 0;
        $this->editingFaqId = null;
    }

    public function toggleCreate()
    {
        $this->isCreating = !$this->isCreating;
        $this->resetInputFields();
    }

    public function editFaq(int $id)
    {
        $faq = Faq::findOrFail($id);
        $this->editingFaqId = $id;
        $this->isCreating = true;

        $this->order = $faq->order;
        $this->questions = [];
        $this->answers = [];
        foreach ($this->supportedLocales as $locale) {
            $this->questions[$locale] = $faq->question[$locale] ?? '';
            $this->answers[$locale] = $faq->answer[$locale] ?? '';
        }
    }

    public function saveFaq()
    {
        $this->validate();

        $faqData = [
            'question' => $this->questions,
            'answer' => $this->answers,
            'order' => $this->order,
        ];

        if ($this->editingFaqId) {
            $faq = Faq::findOrFail($this->editingFaqId);
            $faq->update($faqData);
            ActivityLogger::log('faq_updated', "Updated FAQ Accordion item: #{$faq->id}");
            session()->flash('message', 'FAQ updated successfully.');
        } else {
            $faq = Faq::create($faqData);
            ActivityLogger::log('faq_created', "Created FAQ Accordion item: #{$faq->id}");
            session()->flash('message', 'FAQ item created.');
        }

        $this->isCreating = false;
        $this->resetInputFields();
    }

    public function deleteFaq(int $id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();
        ActivityLogger::log('faq_deleted', "Deleted FAQ Accordion item: #{$faq->id}");
        session()->flash('message', 'FAQ item removed.');
    }

    public function render()
    {
        $faqs = Faq::orderBy('order', 'asc')->orderBy('id', 'desc')->get();
        return view('livewire.admin.faqs', compact('faqs'))
            ->layout('components.layouts.admin');
    }
}

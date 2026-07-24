<?php

namespace App\Livewire\Admin;

use App\Models\AdPlacement;
use App\Models\AffiliateLink;
use Livewire\Component;
use Livewire\WithPagination;

class Monetization extends Component
{
    use WithPagination;

    public string $activeTab = 'ads'; // ads, affiliates
    public bool $isCreatingAd = false;
    public bool $isCreatingAffiliate = false;

    // Ad Fields
    public ?int $editingAdId = null;
    public string $adName = '';
    public string $adType = 'custom'; // adsense, custom
    public string $adLocation = 'header'; // header, footer, sidebar, post_top, post_bottom, in_feed, sticky, anchor
    public string $adCode = '';
    public string $adDestinationUrl = '';
    public bool $adIsActive = true;

    // Affiliate Fields
    public ?int $editingAffiliateId = null;
    public string $affiliateSlug = '';
    public string $affiliateKeyword = '';
    public string $affiliateTargetUrl = '';

    public array $adLocations = [
        'header' => 'Header Banner',
        'footer' => 'Footer Banner',
        'sidebar' => 'Sidebar Ad',
        'post_top' => 'Post Content Top',
        'post_bottom' => 'Post Content Bottom',
        'in_feed' => 'In-Feed Ad (Loop list)',
        'sticky' => 'Sticky Bottom Overlay',
        'anchor' => 'Anchor Mobile Overlay',
    ];

    public function selectTab(string $tab)
    {
        $this->activeTab = $tab;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->editingAdId = null;
        $this->adName = '';
        $this->adType = 'custom';
        $this->adLocation = 'header';
        $this->adCode = '';
        $this->adDestinationUrl = '';
        $this->adIsActive = true;
        $this->isCreatingAd = false;

        $this->editingAffiliateId = null;
        $this->affiliateSlug = '';
        $this->affiliateKeyword = '';
        $this->affiliateTargetUrl = '';
        $this->isCreatingAffiliate = false;
    }

    // --- AD ACTIONS ---

    public function editAd(int $id)
    {
        $ad = AdPlacement::findOrFail($id);
        $this->editingAdId = $id;
        $this->adName = $ad->name;
        $this->adType = $ad->type;
        $this->adLocation = $ad->location;
        $this->adCode = $ad->code;
        $this->adDestinationUrl = $ad->destination_url ?: '';
        $this->adIsActive = $ad->is_active;
        $this->isCreatingAd = true;
    }

    public function saveAd()
    {
        $this->validate([
            'adName' => 'required|string|max:100',
            'adType' => 'required|string',
            'adLocation' => 'required|string',
            'adCode' => 'required|string',
            'adDestinationUrl' => 'nullable|url',
        ]);

        $data = [
            'name' => $this->adName,
            'type' => $this->adType,
            'location' => $this->adLocation,
            'code' => $this->adCode,
            'destination_url' => $this->adDestinationUrl ?: null,
            'is_active' => $this->adIsActive,
        ];

        if ($this->editingAdId) {
            AdPlacement::findOrFail($this->editingAdId)->update($data);
            session()->flash('message', 'Ad placement updated successfully.');
        } else {
            AdPlacement::create($data);
            session()->flash('message', 'New ad placement created.');
        }

        $this->resetInputFields();
    }

    public function toggleAdStatus(int $id)
    {
        $ad = AdPlacement::findOrFail($id);
        $ad->update(['is_active' => !$ad->is_active]);
        session()->flash('message', 'Ad status toggled.');
    }

    public function deleteAd(int $id)
    {
        AdPlacement::findOrFail($id)->delete();
        session()->flash('message', 'Ad placement deleted.');
    }

    // --- AFFILIATE ACTIONS ---

    public function editAffiliate(int $id)
    {
        $aff = AffiliateLink::findOrFail($id);
        $this->editingAffiliateId = $id;
        $this->affiliateSlug = $aff->slug;
        $this->affiliateKeyword = $aff->keyword;
        $this->affiliateTargetUrl = $aff->target_url;
        $this->isCreatingAffiliate = true;
    }

    public function saveAffiliate()
    {
        $this->validate([
            'affiliateSlug' => 'required|string|max:100|alpha_dash|unique:affiliate_links,slug,' . $this->editingAffiliateId,
            'affiliateKeyword' => 'required|string|max:100|unique:affiliate_links,keyword,' . $this->editingAffiliateId,
            'affiliateTargetUrl' => 'required|url',
        ]);

        $data = [
            'slug' => $this->affiliateSlug,
            'keyword' => $this->affiliateKeyword,
            'target_url' => $this->affiliateTargetUrl,
        ];

        if ($this->editingAffiliateId) {
            AffiliateLink::findOrFail($this->editingAffiliateId)->update($data);
            session()->flash('message', 'Affiliate link updated.');
        } else {
            AffiliateLink::create($data);
            session()->flash('message', 'New affiliate link added.');
        }

        $this->resetInputFields();
    }

    public function deleteAffiliate(int $id)
    {
        AffiliateLink::findOrFail($id)->delete();
        session()->flash('message', 'Affiliate link removed.');
    }

    public function render()
    {
        $ads = AdPlacement::orderBy('location')->orderBy('id', 'desc')->paginate(10, ['*'], 'adsPage');
        $affiliates = AffiliateLink::orderBy('keyword')->paginate(10, ['*'], 'affsPage');

        return view('livewire.admin.monetization', compact('ads', 'affiliates'))
            ->layout('components.layouts.admin');
    }
}

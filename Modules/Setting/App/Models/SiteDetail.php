<?php

namespace Modules\Setting\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\FileManager\App\Models\Image;
use Modules\Setting\Database\Factories\SiteDetailFactory;

class SiteDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'keywords',
        'footer_text',
        'main_logo_id',
        'second_logo_id',
        'favicon_id',
    ];

    protected static function newFactory(): SiteDetailFactory
    {
        return SiteDetailFactory::new();
    }

    public function mainLogo(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'main_logo_id');
    }

    public function secondLogo(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'second_logo_id');
    }

    public function favicon(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'favicon_id');
    }

    public function mainLogoLink(): string
    {
        return $this->customLogo()?->url() ?? asset('brand/slodziaki.jpg');
    }

    public function customLogo(): ?Image
    {
        return in_array($this->mainLogo?->alt_text, ['Default Logo', 'Logo sŁodziak', 'Logo sŁodziaki'], true)
            ? null
            : $this->mainLogo;
    }

    public function getLogo($relation): string
    {
        return $relation && ! in_array($relation->alt_text, ['Default Logo', 'Logo sŁodziak', 'Logo sŁodziaki'], true)
            ? $relation->url()
            : asset('brand/slodziaki.jpg');
    }

    public function secondLogoLink(): string
    {
        return $this->getLogo($this->secondLogo);
    }

    public function faviconLink(): string
    {
        if (! $this->favicon || in_array($this->favicon->alt_text, ['Default Favicon', 'Ikona sŁodziak', 'Ikona sŁodziaki'], true)) {
            return asset('brand/slodziaki.jpg');
        }

        return $this->favicon->url();
    }
}

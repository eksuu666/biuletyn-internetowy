@props(['site' => null])

@php($logo = $site?->customLogo())
<span {{ $attributes->class(['brand-logo', 'brand-logo--custom' => $logo]) }}>
    <img src="{{ $logo?->url() ?? asset('brand/slodziaki.jpg') }}" alt="sŁodziaki" @unless($logo) width="3508" height="2480" @endunless>
</span>

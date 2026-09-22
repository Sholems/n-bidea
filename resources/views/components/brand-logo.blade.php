@props([
    'variant' => 'header',
    'loading' => 'eager',
])

<span {{ $attributes->class(['brand-logo', 'brand-logo--'.$variant]) }}>
    <img
        src="{{ asset('images/nb-cci-logo.jpeg') }}"
        alt="Nigeria-Benin Chamber of Commerce and Industry"
        loading="{{ $loading }}"
        decoding="async"
    >
</span>

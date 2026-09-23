@php
    $seoTitle = trim($__env->yieldContent('title', config('seo.default_title')));
    $seoDescription = trim($__env->yieldContent('meta_description', config('seo.default_description')));
    $seoImage = trim($__env->yieldContent('meta_image', asset(config('seo.default_image'))));
    $seoImageType = trim($__env->yieldContent('meta_image_type', 'image/jpeg'));
    $seoImageAlt = trim($__env->yieldContent('meta_image_alt', config('seo.site_name').' logo'));
    $seoType = trim($__env->yieldContent('meta_type', 'website'));
    $seoRobots = trim($__env->yieldContent('meta_robots', 'index, follow'));
    $canonicalUrl = trim($__env->yieldContent('canonical_url', url()->current()));
@endphp

<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<meta name="robots" content="{{ $seoRobots }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

<meta property="og:locale" content="{{ config('seo.locale') }}">
<meta property="og:type" content="{{ $seoType }}">
<meta property="og:site_name" content="{{ config('seo.site_name') }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:image:type" content="{{ $seoImageType }}">
<meta property="og:image:width" content="{{ config('seo.default_image_width') }}">
<meta property="og:image:height" content="{{ config('seo.default_image_height') }}">
<meta property="og:image:alt" content="{{ $seoImageAlt }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">
<meta name="twitter:image:alt" content="{{ $seoImageAlt }}">

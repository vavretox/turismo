@props(['title', 'icon'])
<fieldset class="provider-card">
    <legend class="sr-only">{{ $title }}</legend>
    <h2 class="mb-6 flex items-center gap-3 text-xl font-black text-[#4a0711]"><span class="grid h-10 w-10 place-items-center rounded-full bg-red-50 text-red-800"><i class="fa-solid {{ $icon }}"></i></span>{{ $title }}</h2>
    <div class="grid gap-5 md:grid-cols-2">{{ $slot }}</div>
</fieldset>

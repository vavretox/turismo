@props(['name', 'label', 'type' => 'text', 'value' => null])
<label class="block" for="{{ $name }}"><span class="provider-label">{{ $label }}</span><input {{ $attributes->merge(['class' => 'provider-input']) }} id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ $value ?? old($name) }}">@error($name)<span class="provider-error">{{ $message }}</span>@enderror</label>

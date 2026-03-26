@props([
    'label',
    'name',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'icon' => '•',
])

<div class="form-group">
    <label class="form-label" for="{{ $name }}">{{ $label }}</label>

    <div class="input-wrap">
        <span class="input-icon">{{ $icon }}</span>

        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge(['class' => 'form-input']) }}
        >
    </div>

    @error($name)
        <div class="form-error">{{ $message }}</div>
    @enderror
</div>
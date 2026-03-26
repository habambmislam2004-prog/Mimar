<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-primary']) }}>
    <span>{{ $slot }}</span>
</button>
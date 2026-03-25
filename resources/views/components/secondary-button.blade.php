<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-secondary justify-center items-center py-2.5 shadow-sm']) }}>
    {{ $slot }}
</button>

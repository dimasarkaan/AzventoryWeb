<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary justify-center items-center py-2.5 shadow-md shadow-primary-500/20']) }}>
    {{ $slot }}
</button>

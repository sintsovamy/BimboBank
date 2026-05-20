@props(['class' => 'w-5 h-5 mr-1'])

<svg xmlns="http://www.w3.org/2000/svg" 
     {{ $attributes->merge(['class' => "inline-block {$class}", 'style' => 'color: #9ca3af;']) }} 
     fill="none" 
     viewBox="0 0 24 24" 
     stroke="currentColor">
    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
    <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2"/>
</svg>

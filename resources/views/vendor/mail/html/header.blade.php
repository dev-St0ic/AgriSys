<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
@php
    $logoPath = public_path('images/logos/agrii-removebg.png');
    
    if (file_exists($logoPath)) {
        $imageData = base64_encode(file_get_contents($logoPath));
        echo '<img src="data:image/png;base64,' . $imageData . '" class="logo" alt="AgriSys Logo" style="height: 50px; width: auto;">';
    } else {
        echo '<span style="font-weight: bold; font-size: 24px;">AgriSys</span>';
    }
@endphp
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
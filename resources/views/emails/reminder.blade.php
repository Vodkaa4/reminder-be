@component('mail::message')
# Pengingat {{ strtoupper($entity) }} H-{{ $daysBefore }}

**{{ $title }}**  
Jatuh tempo: **{{ \Carbon\Carbon::parse($targetDate)->format('d M Y') }}**

@component('mail::button', ['url' => config('app.url').'/admin'])
Buka Dashboard
@endcomponent

Terima kasih,  
{{ config('app.name') }}
@endcomponent

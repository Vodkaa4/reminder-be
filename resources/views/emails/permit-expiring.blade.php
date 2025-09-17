@component('mail::message')
# ⚠️ Pengingat Izin Mendekati Masa Berakhir

Halo,

Berikut adalah daftar izin yang akan segera berakhir dan memerlukan perpanjangan:

@foreach($permits as $permit)
## {{ $permit->type }} - {{ $permit->number }}

**Pemegang:** {{ $permit->holder }}  
**Lokasi:** {{ $permit->asset_location }}  
**Tanggal Berakhir:** {{ $permit->expires_at->format('d F Y') }}  
**Sisa Waktu:** {{ $permit->expires_at->diffForHumans() }}  

@if($permit->notes)
**Catatan:** {{ $permit->notes }}
@endif

---
@endforeach

## 📝 Tindakan yang Diperlukan:
- Segera proses perpanjangan izin yang akan berakhir
- Persiapkan dokumen yang diperlukan  
- Hubungi pihak terkait untuk proses perpanjangan
- Update status izin setelah diperpanjang

**PIC:** {{ $picEmail }}  
**Tanggal Pengingat:** {{ now()->format('d F Y H:i') }}

@component('mail::button', ['url' => config('app.url').'/admin'])
Buka Dashboard Admin
@endcomponent

Terima kasih atas perhatiannya.

Terima kasih,  
{{ config('app.name') }}

---
*Email ini dikirim secara otomatis dari sistem PT Eksonindo MPI.*
@endcomponent

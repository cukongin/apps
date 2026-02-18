<div class="w-full mb-4 text-center">
    {{-- Coba load SVG dulu, kalau gagal (onerror) baru load PNG --}}
    <img src="{{ asset('assets/img/logo_mi.png') }}"
         onerror="this.onerror=null; this.src='{{ asset('assets/img/logo_mts.png') }}'"
         alt="Kop Surat"
         class="w-full h-auto object-contain max-h-[80px] mx-auto print:max-h-[60px]">
</div>

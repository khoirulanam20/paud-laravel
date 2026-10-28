<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl" style="color:#2C2C2C;">Buat Slip Gaji</h2>
    </x-slot>
    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
        @if($errors->any())<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        <form method="POST" action="{{ route('admin.gaji-guru.store') }}" class="card p-6 space-y-4">
            @csrf
            @include('admin.gaji-guru._form')
            <div class="flex flex-wrap gap-3 pt-4 border-t" style="border-color:rgba(0,0,0,0.06);">
                <a href="{{ route('admin.gaji-guru.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">Simpan Draft</button>
                <button type="submit" name="approve" value="1" class="btn-primary" style="background:#0d5c5c;">Simpan &amp; Setujui</button>
            </div>
        </form>
    </div>
</x-app-layout>

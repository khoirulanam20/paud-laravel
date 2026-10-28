<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl" style="color:#2C2C2C;">Edit Slip Gaji</h2>
    </x-slot>
    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
        @if($errors->any())<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        <form method="POST" action="{{ route('admin.gaji-guru.update', $gaji) }}" class="card p-6 space-y-4">
            @csrf @method('PUT')
            @include('admin.gaji-guru._form', ['gaji' => $gaji])
            <div class="flex flex-wrap gap-3 pt-4 border-t" style="border-color:rgba(0,0,0,0.06);">
                <a href="{{ route('admin.gaji-guru.show', $gaji) }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-app-layout>

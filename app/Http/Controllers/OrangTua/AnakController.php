<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnakPendaftaranRequest;
use App\Services\AnakRegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AnakController extends Controller
{
    public function __construct(
        protected AnakRegistrationService $anakRegistration
    ) {}

    public function create(): View
    {
        $user = auth()->user();
        $user->loadMissing('sekolah:id,name');

        return view('orangtua.anak.create', [
            'sekolah' => $user->sekolah,
        ]);
    }

    public function store(StoreAnakPendaftaranRequest $request): RedirectResponse
    {
        $user = $request->user();

        $this->anakRegistration->createPendingForParent($user, [
            'name' => $request->input('name'),
            'dob' => $request->input('dob'),
            'catatan_ortu' => $request->input('catatan_ortu'),
        ], $request->file('photo'));

        return redirect()
            ->route('dashboard')
            ->with('status', 'Pendaftaran anak dikirim. Menunggu persetujuan admin sekolah.');
    }
}

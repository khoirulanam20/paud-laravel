<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\LembagaProvisioningService;
use App\Support\RegistrationSpam;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class LembagaRegistrationController extends Controller
{
    public function __construct(
        protected LembagaProvisioningService $provisioning
    ) {}

    public function create(): View
    {
        return view('guest.daftar-lembaga');
    }

    public function store(Request $request): RedirectResponse
    {
        $spam = RegistrationSpam::textRule();

        $validated = $request->validate([
            'lembaga_name' => ['required', 'string', 'max:255', $spam],
            'lembaga_address' => ['nullable', 'string', $spam],
            'lembaga_phone' => ['nullable', 'string', 'max:20'],
            'pendiri' => ['nullable', 'string', 'max:255', $spam],
            'organisasi' => ['nullable', 'string', 'max:255', $spam],
            'sekolah_name' => ['required', 'string', 'max:255', $spam],
            'sekolah_address' => ['nullable', 'string', $spam],
            'sekolah_phone' => ['nullable', 'string', 'max:20'],
            'contact_name' => ['required', 'string', 'max:255', $spam],
            'contact_email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class, RegistrationSpam::emailRule()],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $this->provisioning->registerPending($validated);

        return redirect()->route('login')->with(
            'status',
            'Pendaftaran lembaga berhasil dikirim. Tim kami akan meninjau dan menghubungi Anda setelah disetujui.'
        );
    }
}

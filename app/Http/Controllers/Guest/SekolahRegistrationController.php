<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Services\SekolahProvisioningService;
use App\Support\GuestCms;
use App\Support\RegistrationSpam;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class SekolahRegistrationController extends Controller
{
    public function __construct(
        protected SekolahProvisioningService $provisioning
    ) {}

    public function create(): View
    {
        $cms = GuestCms::data();

        return view('guest.daftar-sekolah', compact('cms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $spam = RegistrationSpam::textRule();

        $validated = $request->validate([
            'sekolah_name' => ['required', 'string', 'max:255', $spam],
            'sekolah_address' => ['nullable', 'string', $spam],
            'sekolah_phone' => ['nullable', 'string', 'max:20'],
            'admin_name' => ['required', 'string', 'max:255', $spam],
            'admin_email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email'), RegistrationSpam::emailRule()],
            'admin_phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $this->provisioning->registerPending($validated);

        return redirect()->route('login')->with(
            'status',
            'Pendaftaran sekolah berhasil dikirim. Tim kami akan meninjau dan menghubungi Anda setelah disetujui.'
        );
    }
}

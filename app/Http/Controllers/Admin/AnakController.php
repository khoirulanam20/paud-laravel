<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnakPendaftaranRequest;
use App\Models\Anak;
use App\Models\Kelas;
use App\Models\User;
use App\Services\AnakRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Traits\CanUploadImage;
use Illuminate\Validation\Rule;

class AnakController extends Controller
{
    use CanUploadImage;

    public function __construct(
        protected AnakRegistrationService $anakRegistration
    ) {}

    public function index(Request $request)
    {
        $sekolah_id = auth()->user()->sekolah_id;

        $query = Anak::where('sekolah_id', $sekolah_id)
            ->where('status', 'approved')
            ->with(['user', 'kelas'])
            ->latest();

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', '%' . $term . '%')
                    ->orWhere('nickname', 'like', '%' . $term . '%');
            });
        }

        $anaks = $query->paginate(15)->withQueryString();
        $kelas = Kelas::where('sekolah_id', $sekolah_id)->orderBy('name')->get();

        return view('admin.anak.index', compact('anaks', 'kelas'));
    }

    public function show(Anak $anak)
    {
        abort_if($anak->sekolah_id !== auth()->user()->sekolah_id, 403);

        $anak->load([
            'user',
            'kelas',
            'kesehatans' => fn($q) => $q->orderBy('tanggal_pemeriksaan', 'desc'),
            'pencapaians' => fn($q) => $q->with(['kegiatan', 'matrikulasi'])->latest()
        ]);

        return view('admin.anak.show', compact('anak'));
    }

    public function store(Request $request)
    {
        $sekolah_id = auth()->user()->sekolah_id;
        $parentMode = $request->input('parent_mode', 'new');

        $rules = array_merge(
            [
                'parent_mode' => ['required', Rule::in(['new', 'existing'])],
                'name' => 'required|string|max:255',
            ],
            StoreAnakPendaftaranRequest::adminOptionalRules()
        );

        $rules['dob'] = 'nullable|date|before:today';
        $rules['photo'] = 'nullable|image|max:2048';

        if ($parentMode === 'existing') {
            $rules['parent_user_id'] = [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('sekolah_id', $sekolah_id)),
            ];
        } else {
            $rules['parent_name'] = 'required|string|max:255';
            $rules['parent_email'] = 'required|email|max:255|unique:users,email';
        }

        $validated = $request->validate($rules);

        if ($parentMode === 'existing') {
            $user = User::query()
                ->where('id', $validated['parent_user_id'])
                ->where('sekolah_id', $sekolah_id)
                ->role('Orang Tua')
                ->firstOrFail();
        } else {
            $user = User::create([
                'name' => $validated['parent_name'],
                'email' => $validated['parent_email'],
                'password' => Hash::make('password123'),
                'sekolah_id' => $sekolah_id,
            ]);
            $user->assignRole('Orang Tua');
        }

        $anakData = [
            'name' => $validated['name'],
            'dob' => $validated['dob'] ?? null,
            'nickname' => $validated['nickname'] ?? null,
            'nik' => $validated['nik'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
            'kelas_id' => $validated['kelas_id'] ?? null,
            'nik_bapak' => $validated['nik_bapak'] ?? null,
            'nama_bapak' => $validated['nama_bapak'] ?? null,
            'nik_ibu' => $validated['nik_ibu'] ?? null,
            'nama_ibu' => $validated['nama_ibu'] ?? null,
        ];

        $this->anakRegistration->createApprovedForParent(
            $user,
            $anakData,
            $sekolah_id,
            $request->file('photo')
        );

        $message = $parentMode === 'existing'
            ? 'Data anak berhasil ditambahkan ke akun orang tua yang dipilih.'
            : 'Data Anak dan Orang Tua berhasil ditambahkan. Password default Ortu: password123';

        return redirect()->route('admin.anak.index')->with('success', $message);
    }

    public function update(Request $request, Anak $anak)
    {
        abort_if($anak->sekolah_id !== auth()->user()->sekolah_id, 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:50',
            'dob' => 'nullable|date',
            'nik' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'nik_bapak' => 'nullable|string|max:50',
            'nama_bapak' => 'nullable|string|max:255',
            'nik_ibu' => 'nullable|string|max:50',
            'nama_ibu' => 'nullable|string|max:255',
            'parent_name' => 'required|string|max:255',
            'parent_email' => 'required|email|max:255',
        ]);

        $user = $anak->user;
        if ($user && $user->email !== $request->parent_email) {
            $exists = User::where('email', $request->parent_email)->where('id', '!=', $user->id)->exists();
            if ($exists) {
                return back()->withInput()->withErrors(['parent_email' => 'Email wali sudah digunakan oleh pengguna lain.']);
            }
            $user->update(['email' => $request->parent_email]);
        }

        if ($user) {
            $user->update(['name' => $request->parent_name]);
        }

        $dataArr = [
            'name' => $request->name,
            'nickname' => filled(trim($request->input('nickname', '')))
                ? trim($request->input('nickname'))
                : null,
            'dob' => $request->dob,
            'kelas_id' => $request->kelas_id,
            'nik' => $request->nik,
            'alamat' => $request->alamat,
            'jenis_kelamin' => $request->jenis_kelamin,
            'nik_bapak' => $request->nik_bapak,
            'nama_bapak' => $request->nama_bapak,
            'nik_ibu' => $request->nik_ibu,
            'nama_ibu' => $request->nama_ibu,
            'parent_name' => $request->parent_name,
        ];

        if ($request->hasFile('photo')) {
            if ($anak->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($anak->photo);
            }
            $dataArr['photo'] = $this->uploadImage($request->file('photo'), 'anak');
        }

        $anak->update($dataArr);

        return redirect()->route('admin.anak.index')->with('success', 'Data Anak berhasil diperbarui.');
    }

    public function destroy(Anak $anak)
    {
        abort_if($anak->sekolah_id !== auth()->user()->sekolah_id, 403);

        $user = $anak->user;
        if ($anak->photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($anak->photo);
        }
        $anak->delete();

        if ($user && $user->hasRole('Orang Tua') && $user->anaks()->count() === 0) {
            $user->delete();
        }

        return redirect()->route('admin.anak.index')->with('success', 'Data Anak berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Lembaga;
use App\Models\User;
use App\Services\LembagaProvisioningService;
use App\Support\PaginationPerPage;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LembagaController extends Controller
{
    public function __construct(
        protected LembagaProvisioningService $provisioning
    ) {}

    public function index(Request $request)
    {
        $status = $request->query('status', 'all');

        $query = Lembaga::withCount('sekolahs')->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $lembagas = $query->paginate(PaginationPerPage::resolve($request))->withQueryString();
        $pendingCount = Lembaga::where('status', Lembaga::STATUS_PENDING)->count();

        return view('superadmin.lembaga.index', compact('lembagas', 'status', 'pendingCount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'pendiri' => 'nullable|string|max:255',
            'organisasi' => 'nullable|string|max:255',
            'no_akta' => 'nullable|string|max:255',
            'no_pengesahan' => 'nullable|string|max:255',
        ]);

        Lembaga::create(array_merge($validated, ['status' => Lembaga::STATUS_ACTIVE]));

        return redirect()->route('superadmin.lembaga.index')
            ->with('success', 'Lembaga berhasil ditambahkan.');
    }

    public function update(Request $request, Lembaga $lembaga)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'pendiri' => 'nullable|string|max:255',
            'organisasi' => 'nullable|string|max:255',
            'no_akta' => 'nullable|string|max:255',
            'no_pengesahan' => 'nullable|string|max:255',
        ]);

        $lembaga->update($validated);

        return redirect()->route('superadmin.lembaga.index')
            ->with('success', 'Data lembaga berhasil diperbarui.');
    }

    public function approve(Request $request, Lembaga $lembaga)
    {
        abort_unless($lembaga->status === Lembaga::STATUS_PENDING, 400);

        $this->provisioning->approve($lembaga, $request->user());

        return back()->with('success', 'Lembaga "'.$lembaga->name.'" disetujui dan aktif.');
    }

    public function reject(Request $request, Lembaga $lembaga)
    {
        abort_unless($lembaga->status === Lembaga::STATUS_PENDING, 400);

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $this->provisioning->reject($lembaga, $validated['rejection_reason']);

        return back()->with('success', 'Pendaftaran lembaga ditolak.');
    }

    public function destroy(Lembaga $lembaga)
    {
        $sekolahIds = $lembaga->sekolahs()->pluck('id');
        $hasSiswa = $sekolahIds->isNotEmpty()
            && Anak::withoutSekolahScope()->whereIn('sekolah_id', $sekolahIds)->exists();
        $canCascade = in_array($lembaga->status, [Lembaga::STATUS_REJECTED, Lembaga::STATUS_PENDING], true);

        if ($sekolahIds->isNotEmpty() && ($hasSiswa || ! $canCascade)) {
            return back()->withErrors([
                'lembaga' => $hasSiswa
                    ? 'Lembaga masih memiliki data siswa. Hapus data siswa terlebih dahulu.'
                    : 'Lembaga aktif masih memiliki cabang sekolah. Hapus sekolah terlebih dahulu.',
            ]);
        }

        try {
            DB::transaction(function () use ($lembaga, $sekolahIds) {
                $users = User::query()
                    ->where(function ($query) use ($lembaga, $sekolahIds) {
                        $query->where('lembaga_id', $lembaga->id);
                        if ($sekolahIds->isNotEmpty()) {
                            $query->orWhereIn('sekolah_id', $sekolahIds);
                        }
                    })
                    ->get();

                foreach ($users as $user) {
                    if ($user->hasRole('Superadmin') || $user->id === auth()->id()) {
                        $user->forceFill(['lembaga_id' => null, 'sekolah_id' => null])->save();

                        continue;
                    }

                    $this->detachAccess($user);
                    $user->tokens()->delete();
                    $user->delete();
                }

                $lembaga->sekolahs()->get()->each->delete();
                $lembaga->delete();
            });
        } catch (QueryException) {
            return back()->withErrors([
                'lembaga' => 'Lembaga tidak bisa dihapus karena masih ada data terkait.',
            ]);
        }

        return redirect()->route('superadmin.lembaga.index')
            ->with('success', 'Lembaga berhasil dihapus.');
    }

    private function detachAccess(User $user): void
    {
        $tables = config('permission.table_names');
        $modelKey = config('permission.column_names.model_morph_key');

        foreach (['model_has_roles', 'model_has_permissions'] as $name) {
            DB::table($tables[$name])
                ->where($modelKey, $user->id)
                ->where('model_type', $user->getMorphClass())
                ->delete();
        }
    }
}

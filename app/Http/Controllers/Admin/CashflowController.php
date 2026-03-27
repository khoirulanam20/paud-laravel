<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cashflow;
use Illuminate\Http\Request;

class CashflowController extends Controller
{
    public function index()
    {
        $sekolah_id = auth()->user()->sekolah_id;
        $cashflows = Cashflow::where('sekolah_id', $sekolah_id)->orderBy('date', 'desc')->paginate(15);
        
        $totalIn = Cashflow::where('sekolah_id', $sekolah_id)->where('type', 'in')->sum('amount');
        $totalOut = Cashflow::where('sekolah_id', $sekolah_id)->where('type', 'out')->sum('amount');
        $balance = $totalIn - $totalOut;

        return view('admin.cashflow.index', compact('cashflows', 'totalIn', 'totalOut', 'balance'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
        ]);

        Cashflow::create([
            'sekolah_id' => auth()->user()->sekolah_id,
            'date' => $request->date,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.cashflow.index')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function update(Request $request, Cashflow $cashflow)
    {
        abort_if($cashflow->sekolah_id !== auth()->user()->sekolah_id, 403);

        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
        ]);

        $cashflow->update([
            'date' => $request->date,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.cashflow.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Cashflow $cashflow)
    {
        abort_if($cashflow->sekolah_id !== auth()->user()->sekolah_id, 403);
        $cashflow->delete();
        return redirect()->route('admin.cashflow.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Motor;
use Illuminate\Support\Facades\Auth;

class MotorController extends Controller
{
    public function index(Request $request)
{
    $query = Motor::query();

    if ($request->search) {
        $search = $request->search;

        $query->where('nama', 'like', '%' . $search . '%')
    ->orderByRaw("
        CASE
            WHEN nama = ? THEN 0
            WHEN nama LIKE ? THEN 1
            ELSE 2
        END
    ", [$search, $search . '%']);
    }

    $motors = $query->orderBy('nama', 'asc')->get();

    return view('motor.index', compact('motors'));
}

    public function create()
    {
        return view('motor.create');
    }



    public function edit($id)
    {
        $motor = Motor::findOrFail($id);
        return view('motor.edit', compact('motor'));
    }

   

    public function destroy($id)
    {
        Motor::findOrFail($id)->delete();

        return redirect()->route('motor.index')->with('success', 'Motor berhasil dihapus');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'category' => 'required|in:fast_moving,premium,slow_moving'
        ]);

        Motor::create([
            'nama' => $request->nama,
            'user_id' => Auth::id()
        ]);

        return redirect()->route('motor.index')
            ->with('success', 'Motor berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        // ✅ VALIDASI
        $request->validate([
            'nama' => 'required|string|max:255'
        ]);

        $motor = Motor::findOrFail($id);

        $motor->update([
            'nama' => $request->nama,
            'user_id' => Auth::id()
        ]);

        return redirect()->route('motor.index')
            ->with('success', 'Motor berhasil diupdate');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OperationalCost;

class OperationalCostController extends Controller
{
    // Menampilkan semua data operational
    public function index(Request $request)
    {
        if (auth('company')->check()) {
            $companyId = auth('company')->id();
        } elseif (auth('web')->check()) {
            $companyId = auth('web')->user()->company_id;
        } else {
            return abort(403, 'Unauthorized');
        }

        $query = OperationalCost::where('company_id', $companyId);

        // // Filtering berdasarkan type/note
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->Where('note', 'like', '%' . $request->search . '%');
            });
        }

        // Sorting
        if ($request->has('sort') && in_array($request->sort, ['asc', 'desc'])) {
            $query->orderBy('date', $request->sort);
        }

        $operationals = $query->paginate(10);

        return view('operational.index', compact('operationals'));
    }

    // Tampilkan form tambah
    public function create()
    {
        return view('operational.add');
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'note' => 'required|string',
        ]);

        if (auth('company')->check()) {
            $companyId = auth('company')->id();
        } elseif (auth('web')->check()) {
            $companyId = auth('web')->user()->company_id;
        } else {
            return abort(403, 'Unauthorized');
        }

        OperationalCost::create([
            'date' => $request->date,
            'amount' => $request->amount,
            'note' => $request->note,
            'company_id' => $companyId,
        ]);

        return redirect()->route('operational.index')->with('success', 'OperationalCost data added successfully!');
    }

    // Tampilkan form edit
    public function edit($id)
    {
        $operational = OperationalCost::findOrFail($id);
        return view('operational.edit', compact('operational'));
    }

    // Update data
    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'note' => 'required|string',
        ]);

        $operational = OperationalCost::findOrFail($id);
        $operational->update($request->only(['date', 'amount', 'note']));

        return redirect()->route('operational.index')->with('success', 'OperationalCost data updated successfully!');
    }

    // Hapus data
    public function destroy($id)
    {
        if (auth('company')->check()) {
            $companyId = auth('company')->id();
        } elseif (auth('web')->check()) {
            $companyId = auth('web')->user()->company_id;
        } else {
            return abort(403, 'Unauthorized');
        }

        $operational = OperationalCost::where('id', $id)
            ->where('company_id', $companyId)
            ->firstOrFail();

        $operational->delete();

        return redirect()->route('operational.index')->with('success', 'OperationalCost data deleted successfully!');
    }
}

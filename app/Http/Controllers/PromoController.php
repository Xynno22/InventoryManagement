<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use App\Models\PromoType;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Promo::with('promoType')->where('company_id', auth('company')->id());

        // Filtering by search
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sorting
        if ($request->has('sort') && in_array($request->sort, ['asc', 'desc'])) {
            $query->orderBy('name', $request->sort);
        }

        $promos = $query->paginate(10); // Pastikan hanya panggil paginate() di akhir.

        return view('promo.index', compact('promos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $promoTypes = PromoType::all();
        return view('promo.add', compact('promoTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'end_date' => 'required|date|after:today',
            'type' => 'required|in:percentage,"fixed amount"',
            'fixedamount' => 'nullable|numeric|min:0',
            'percentageamount' => 'nullable|numeric|min:0|max:100',
        ]);

        // Mencari ID promo_type berdasarkan nama
        $promoType = PromoType::where('name', strtolower($request->type))->first();

        if (!$promoType) {
            return back()->withErrors(['type' => 'Invalid promo type selected'])->withInput();
        }

        // Ambil amount berdasarkan type
        $amount = $request->type === 'percentage' ? $request->percentageamount : $request->fixedamount;

        // Jika amount tetap null, kembalikan error
        if (is_null($amount)) {
            return back()->withErrors(['amount' => 'The amount field is required.'])->withInput();
        }

        // Simpan data promo dengan company_id dari auth('company')
        Promo::create([
            'name' => $request->name,
            'end_date' => $request->end_date,
            'promo_type_id' => $promoType->id,
            'amount' => $amount,
            'company_id' => auth('company')->id(), // Menyimpan company_id
        ]);

        return redirect()->route('promo.index')->with('success', 'Promo added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Promo $promo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $promo = Promo::findOrFail($id);
        $promoTypes = PromoType::all();
        return view('promo.edit', compact('promo', 'promoTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Promo $promo)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'end_date' => 'required|date|after:today',
            'type' => 'required|in:percentage,"fixed amount"',
            'fixedamount' => 'nullable|numeric|min:0|max:1000000',
            'percentageamount' => 'nullable|numeric|min:0|max:100',
        ]);

        // Mencari ID promo_type berdasarkan nama
        $promoType = PromoType::where('name', strtolower($request->type))->first();

        if (!$promoType) {
            return back()->withErrors(['type' => 'Invalid promo type selected'])->withInput();
        }

        // Ambil amount berdasarkan type
        $amount = $request->type === 'percentage' ? $request->percentageamount : $request->fixedamount;

        // Jika amount tetap null, kembalikan error
        if (is_null($amount)) {
            return back()->withErrors(['amount' => 'The amount field is required.'])->withInput();
        }

        // Update data promo
        $promo->update([
            'name' => $request->name,
            'promo_type_id' => $promoType->id,
            'end_date' => $request->end_date,
            'amount' => $amount,
        ]);

        return redirect()->route('promo.index')->with('success', 'Promo updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $promo = Promo::where('id', $id)
                ->where('company_id', auth('company')->id()) // Pastikan hanya bisa menghapus promo milik perusahaan yang login
                ->firstOrFail();

            $promo->delete();

            return response()->json(['success' => true, 'message' => 'Promo deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete promo: ' . $e->getMessage()], 500);
        }
    }
}

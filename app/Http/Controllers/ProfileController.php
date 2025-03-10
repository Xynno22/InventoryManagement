<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{    

    // Tampilkan halaman profil
    public function profile()
    {
        $company = Auth::guard('company')->user();
        return view('profile.profile', compact('company'));
    }

    // Tampilkan halaman edit profil
    public function edit()
    {
        $company = Auth::guard('company')->user();
        return view('profile.editProfile', compact('company'));
    }

    // Update data profil
    public function update(Request $request)
    {
        $company = Auth::guard('company')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
            'password' => 'nullable|min:6|confirmed',
        ]);

        // Update data
        $company->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'password' => $request->password ? Hash::make($request->password) : $company->password,
        ]);

        return redirect()->route('profile.index')->with('success', 'Profile updated successfully!');
    }
}

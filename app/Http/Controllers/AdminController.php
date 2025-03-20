<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $admins = User::where('company_id', auth('company')->id())->get();
        $roles = Role::where('company_id', auth('company')->id())->get();
        return view('admin.index', ['admins' => $admins, 'roles' => $roles]);
    }
    public function updateRole(Request $request, $id)
    {
        $admin = User::findOrFail($id);
        $admin->syncRoles([$request->role]);
    
        return redirect()->back()->with('success', 'Role updated successfully!');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => auth('company')->id(),
        ]);

        return redirect()->route('admin.index')->with('success', 'Admin added successfully.');
    }

    public function destroy($id)
    {
        $category = User::where('id', $id)
                ->where('company_id', auth('company')->id()) 
                // Ensures the category belongs to the logged-in company
                ->firstOrFail();
        $category->delete();


        return redirect()->route('admin.index')->with('success', 'Admin deleted.');
    }
}

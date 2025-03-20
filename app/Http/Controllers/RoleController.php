<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::where('company_id', auth('company')->id())->get();
        $permissions = Permission::all();
        
        return view('roles.index', [
            'roles' => $roles, 'permissions' => $permissions
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'nullable|exists:companies,id'
        ]);

        // Cek apakah role sudah ada dalam satu company
        $exists = Role::where('name', $request->name)
                    ->where('guard_name', 'web')
                    ->where('company_id', auth('company')->id())
                    ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'This role already exists for this company.']);
        }

        // Buat role dengan company_id
        Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
            'company_id' => auth('company')->id(),
        ]);

        return redirect()->route('roles.index')->with('success', 'Role added successfully.');
    }
    public function editPermissions(Role $role)
    {
        $permissions = Permission::all();
        return view('roles.permissions', compact('role', 'permissions'));
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $role->syncPermissions($request->permissions);
        return redirect()->route('roles.permissions.edit', $role)->with('success', 'Permissions updated successfully.');
    }
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        // Update permissions
        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')->with('success', 'Permissions updated successfully.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}

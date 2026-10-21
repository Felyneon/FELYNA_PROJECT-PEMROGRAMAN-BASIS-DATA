<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    public function index() {
        $roles = Role::all();
        return view('role.index', compact('roles'));
    }

    public function store(Request $request) {
        $request->validate(['nama_role' => 'required']);
        Role::create(['nama_role' => $request->nama_role]);
        return redirect('/role')->with('success', 'Role berhasil ditambahkan');
    }

    public function update(Request $request, $id) {
        $request->validate(['nama_role' => 'required']);
        $role = Role::findOrFail($id);
        $role->update(['nama_role' => $request->nama_role]);
        return redirect('/role')->with('success', 'Role berhasil diperbarui');
    }

    public function destroy($id) {
        Role::destroy($id);
        return redirect('/role')->with('success', 'Role berhasil dihapus');
    }
}
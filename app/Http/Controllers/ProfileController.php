<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        $iduser = session('iduser');
        if (!$iduser) {
            return redirect('/login');
        }
        $user = User::with('role')->findOrFail($iduser);
        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $iduser = session('iduser');
        $user = User::findOrFail($iduser);

        $request->validate([
            'nama_lengkap' => 'nullable|string|max:100',
            'no_hape' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'help_center' => 'nullable|string|max:255',
        ]);

        $user->update([
            'nama_lengkap' => $request->nama_lengkap,
            'no_hape' => $request->no_hape,
            'email' => $request->email,
            'help_center' => $request->help_center,
        ]);

        return redirect('/profile')->with('success', 'Profil berhasil diperbarui!');
    }
}
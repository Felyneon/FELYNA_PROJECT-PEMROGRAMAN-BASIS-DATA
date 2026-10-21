<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            session(['iduser' => $user->iduser, 'username' => $user->username]);
            return redirect('/dashboard');
        }

        return back()->withErrors(['username' => 'Username atau password salah.']);
    }

    public function showRegister() {
        $roles = Role::all();
        return view('auth.register', compact('roles'));
    }

    public function register(Request $request) {
        $request->validate([
            'username' => 'required|unique:user,username',
            'password' => 'required|min:6',
            'idrole' => 'required'
        ]);

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'idrole' => $request->idrole
        ]);

        return redirect('/login')->with('success', 'Registrasi berhasil, silakan login!');
    }

    public function logout(Request $request) {
        session()->flush();
        return redirect('/login');
    }
}
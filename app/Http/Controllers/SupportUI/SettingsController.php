<?php

namespace App\Http\Controllers\SupportUI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index()
    {
        return view('support.settings.index');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required','email','max:255', Rule::unique('users')->ignore($user->id)],
        ]);
        $user->update($data);
        return back()->with('success', 'Profil mis à jour');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);
        if (! Hash::check($data['current_password'], $user->password)) {
            return back()->with('error', 'Mot de passe actuel incorrect');
        }
        $user->password = Hash::make($data['password']);
        $user->save();
        return back()->with('success', 'Mot de passe mis à jour');
    }
}

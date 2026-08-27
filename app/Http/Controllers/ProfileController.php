<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        // Ambil user login
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */
        $request->validate([
            'full_name' => 'required',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        /*
        |--------------------------------------------------------------------------
        | UPDATE NAMA
        |--------------------------------------------------------------------------
        */
        $user->full_name = $request->full_name;

        /*
        |--------------------------------------------------------------------------
        | UPLOAD AVATAR
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('avatar')) {

            // hapus avatar lama (kalau ada)
            if ($user->avatar_url) {
                Storage::delete('public/avatars/' . $user->avatar_url);
            }

            // simpan file ke storage
            $file = $request->file('avatar');

            $filename = time() . '.' . $file->getClientOriginalExtension();

            $file->storeAs('public/avatars', $filename);

            // simpan ke database
            $user->avatar_url = $filename;
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN KE DATABASE
        |--------------------------------------------------------------------------
        */
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui');
    }
}
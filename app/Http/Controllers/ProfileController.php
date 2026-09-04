<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | VALIDASI EDIT PROFIL & AVATAR
        |--------------------------------------------------------------------------
        */
        $request->validate([
            'full_name' => ['required', 'regex:/^[A-Za-z\s]+$/'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']
        ], [
            'full_name.required' => 'Nama lengkap wajib diisi',
            'full_name.regex' => 'Gunakan nama yang sebenarnya',
            'avatar.image' => 'File harus berupa gambar',
            'avatar.mimes' => 'Format file harus JPG, JPEG, PNG, atau WEBP',
            'avatar.max' => 'Ukuran gambar maksimal 2MB'
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

            // Hapus avatar lama jika ada
            if ($user->avatar_url) {
                Storage::delete('public/avatars/' . $user->avatar_url);
            }

            $file = $request->file('avatar');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/avatars', $filename);

            // Simpan ke kolom database avatar_url
            $user->avatar_url = $filename;
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE PASSWORD
    |--------------------------------------------------------------------------
    */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => ['required'],
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                'confirmed'
            ],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi',
            'password.required' => 'Password baru wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.regex' => 'Password harus ada huruf besar, kecil, dan angka',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        // Cek apakah password lama sesuai
        if (!Hash::check($request->current_password, $user->password_hash)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai']);
        }

        // Update ke password baru
        $user->password_hash = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password berhasil diubah');
    }
}
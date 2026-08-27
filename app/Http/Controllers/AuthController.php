<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $credentials = [
        'email' => $request->email,
        'password' => $request->password
    ];

    if (Auth::attempt($credentials, $request->remember)) {

        $request->session()->regenerate();
        if ($request->remember) {
            session([
                'remember_email' => $request->email,
                'remember_checked' => true
            ]);
        } else {
            //HAPUS kalau tidak dicentang
            session()->forget(['remember_email', 'remember_checked']);
        }

        return redirect()->route('home');
    }

    return back()
        ->with('error', 'Email atau password salah')
        ->withInput();
}

public function logout(Request $request)
{
    Auth::logout();

    $rememberEmail = session('remember_email');
    $rememberChecked = session('remember_checked');

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    if ($rememberChecked) {
        session([
            'remember_email' => $rememberEmail,
            'remember_checked' => true
        ]);
    }

    return redirect()->route('login');
}
public function register(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | FORMAT NOMOR HP DULU (WAJIB SEBELUM VALIDASI)
    |--------------------------------------------------------------------------
    */
    $phone = preg_replace('/^0/', '62', $request->phone);

    $request->merge([
        'phone' => $phone
    ]);

    /*
    |--------------------------------------------------------------------------
    | VALIDATOR
    |--------------------------------------------------------------------------
    */
    $validator = Validator::make($request->all(), [

        'name' => ['required', 'regex:/^[A-Za-z\s]+$/'],

        'email' => [
            'required',
            'email:rfc,dns', // 🔥 FIX
            'unique:users,email'
        ],

        'phone' => [
            'required',
            'regex:/^628[1-9][0-9]{7,10}$/', // 🔥 lebih ketat
            'unique:users,phone'
                ],

        'password' => [
            'required',
            'min:8',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'confirmed'
        ],

    ], [

        'name.required' => 'Nama wajib diisi',
        'name.regex' => 'Gunakan nama yang sebenarnya',

        'email.required' => 'Email wajib diisi',
        'email.email' => 'Format email tidak valid',
        'email.unique' => 'Email sudah terdaftar',

        'phone.required' => 'Nomor HP wajib diisi',
        'phone.regex' => 'Nomor HP hanya boleh angka',
        'phone.min' => 'Nomor HP minimal 10 digit',
        'phone.unique' => 'Nomor HP sudah digunakan',

        'password.required' => 'Password wajib diisi',
        'password.min' => 'Password minimal 8 karakter',
        'password.regex' => 'Password harus ada huruf besar, kecil, dan angka',
        'password.confirmed' => 'Konfirmasi password tidak cocok',
    ]);

if ($validator->fails()) {
        return back()
            ->with('error', $validator->errors()->first())
            ->with('active_tab', 'register')
            ->withInput();
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN USER
    |--------------------------------------------------------------------------
    */
    User::create([
        'full_name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone, // sudah format 62
        'password_hash' => Hash::make($request->password),
    ]);

    return redirect()->route('login')->with('success', 'Registrasi berhasil!');
}
}
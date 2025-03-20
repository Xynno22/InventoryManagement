<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    
    public function register(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:companies',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Simpan data perusahaan baru
        $company = Company::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'phone' => $request->phone,
        ]);

        event(new Registered($company)); // Kirim email verifikasi

        Auth::guard('company')->login($company);

        // Redirect ke halaman verifikasi email
        return redirect()->route('verification.notice');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $credentials = $request->only('email', 'password');

        // Coba autentikasi dengan guard 'company'
        if (Auth::guard('company')->attempt($credentials)) {
            $user = Auth::guard('company')->user();

            // Cek apakah email sudah diverifikasi
            if (!$user->email_verified_at) {
                Auth::guard('company')->logout(); // Logout jika belum diverifikasi
                return response()->json([
                    'message' => 'Email belum diverifikasi. Silakan cek email Anda.',
                ], 403);
            }

            return redirect('/dashboard')->with('status', 'Anda Berhasil Login.');
        }

        return response()->json([
            'message' => 'Invalid email or password.',
        ], 401);
    }
    public function loginUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('web')->attempt($credentials)) {
            return redirect('/dashboard')->with('status', 'Anda Berhasil Login.');
        }
        
        return response()->json([
            'message' => 'Invalid email or password.',
        ], 401);
    }
    public function destroy(Request $request)
    {
        $company = auth('company')->user(); // Ambil data company yang sedang login
        auth('company')->logout();

        $company->delete(); 

        return redirect('/login')->with('status', 'Akun perusahaan berhasil dihapus.');
    }
    public function showLinkRequestForm()
    {
        return view('authentication.forgot-password');
    }

    // Kirim link reset ke email
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:companies,email',
        ]);

        $company = Company::where('email', $request->email)->first();

        // Buat token reset password (bisa simpan di table password_resets atau logic kamu sendiri)
        $token = \Str::random(60);

        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $company->email],
            ['token' => $token, 'created_at' => now()]
        );

        // Kirim email reset link
        \Mail::send('verification.verificationEmail', ['token' => $token, 'email' => $company->email], function ($message) use ($company) {
            $message->to($company->email);
            $message->subject('Reset Your Password');
        });

        return back()->with('success', 'Reset link has been sent to your email.');
    }

    // Tampilkan form reset password
    public function showResetForm($token, Request $request)
    {
        return view('authentication.reset-password', ['token' => $token, 'email' => $request->query('email'),]);
    }

    // Proses reset password
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
    
        // Cek apakah token valid di tabel password_resets
        $reset = \DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->where('token', $request->token)
                    ->first();
    
    
        // Cari company berdasarkan email
        $company = Company::where('email', $request->email)->first();
    
        
        $updated = $company->update([
            'password' => bcrypt($request->password)
        ]);
        
        $company->refresh(); // refresh biar ambil data terbaru dari database
    
        // Hapus token setelah sukses reset
        \DB::table('password_reset_tokens')->where('email', $request->email)->delete();
    
        return redirect('/login')->with('success', 'The password has been successfully reset. Please log in.');
    }
    
}

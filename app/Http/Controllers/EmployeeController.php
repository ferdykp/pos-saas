<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class EmployeeController extends Controller
{
    // Tampilkan Daftar Pegawai
    public function index()
    {
        // Hanya mengambil pegawai yang satu toko/tenant dengan admin yang login
        $employees = User::where('tenant_id', Auth::user()->tenant_id)
            ->orderBy('role', 'asc')
            ->get();

        return view('employees.index', compact('employees'));
    }

    // Simpan Pegawai Baru ke Database
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'role' => ['required', 'in:admin,manager,kasir'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'tenant_id' => Auth::user()->tenant_id, // Otomatis mengikat ke tenant admin
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('employees.index')->with('success', 'Pegawai baru berhasil didaftarkan!');
    }

    // Hapus Pegawai
    public function destroy($id)
    {
        $employee = User::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);

        // Mencegah admin menghapus dirinya sendiri
        if ($employee->id === Auth::id()) {
            return redirect()->route('employees.index')->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Akun pegawai berhasil dihapus.');
    }
}

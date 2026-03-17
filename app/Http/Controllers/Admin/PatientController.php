<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Exports\PatientExport;
use App\Imports\PatientImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_rm', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        $patients = $query->latest()->paginate(15)->withQueryString();

        return view('admin.patients.index', compact('patients'));
    }

    public function create()
    {
        return view('admin.patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'             => 'required|string|max:100',
            'tanggal_lahir'    => 'nullable|date|before:today',
            'jenis_kelamin'    => 'nullable|in:L,P',
            'no_hp'            => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:100',
            'alamat'           => 'nullable|string',
            'riwayat_penyakit' => 'nullable|string',
        ]);

        $validated['no_rm'] = Patient::generateNoRM();

        Patient::create($validated);

        return redirect()->route('patients.index')
            ->with('success', "Pasien {$validated['nama']} berhasil ditambahkan dengan No. RM {$validated['no_rm']}.");
    }

    public function show(Patient $patient)
    {
        $patient->load([
            'medicalRecords.dokter',
            'transactions.items.product',
            'transactions.kasir',
        ]);

        return view('admin.patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('admin.patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'nama'             => 'required|string|max:100',
            'tanggal_lahir'    => 'nullable|date|before:today',
            'jenis_kelamin'    => 'nullable|in:L,P',
            'no_hp'            => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:100',
            'alamat'           => 'nullable|string',
            'riwayat_penyakit' => 'nullable|string',
        ]);

        $patient->update($validated);

        return redirect()->route('patients.index')
            ->with('success', 'Data pasien berhasil diperbarui.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Data pasien berhasil dihapus.');
    }

    public function export()
    {
        return Excel::download(new PatientExport, 'patients_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        $import = new PatientImport;
        Excel::import($import, $request->file('file'));

        $successCount = $import->getSuccessCount();
        $errors = $import->getErrors();

        $message = "Import selesai. {$successCount} data berhasil diimpor.";
        if (!empty($errors)) {
            $message .= " Tapi ada " . count($errors) . " error(s): " . implode('; ', $errors);
        }

        return redirect()->route('patients.index')
            ->with('success', $message);
    }

    // AJAX — search pasien untuk POS
    public function search(Request $request)
    {
        $patients = Patient::where('nama', 'like', "%{$request->q}%")
            ->orWhere('no_rm', 'like', "%{$request->q}%")
            ->limit(10)
            ->get(['id', 'no_rm', 'nama', 'no_hp']);

        return response()->json($patients);
    }
}

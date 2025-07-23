<?php


namespace App\Http\Controllers;
use App\Models\JenisAkun;
use Illuminate\Http\Request;

class JenisAkunController extends Controller
{
    public function index()
    {
        $jenisAkuns = JenisAkun::latest()->paginate(10);
        return view('pages.jenis.index', compact('jenisAkuns'));
    }

    public function create()
    {
        return view('pages.jenis.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_akun' => 'required|string|max:255|unique:jenis_akuns,nama_akun',
            'keterangan' => 'nullable|string',
            'jenis' => 'required|in:Harta,Utang,Modal,Pendapatan,Beban',
            'status' => 'required|in:Aktif,Tidak Aktif',
        ]);

        JenisAkun::create($request->all());

        return redirect()->route('jenis-akun.index')->with('success', 'Jenis Akun berhasil ditambahkan.');
    }

    public function edit(JenisAkun $jenisAkun)
    {
        return view('pages.jenis.edit', compact('jenisAkun'));
    }

    public function update(Request $request, JenisAkun $jenisAkun)
    {
        $request->validate([
            'nama_akun' => 'required|string|max:255|unique:jenis_akuns,nama_akun,' . $jenisAkun->id,
            'keterangan' => 'nullable|string',
            'jenis' => 'required|in:Harta,Utang,Modal,Pendapatan,Beban',
            'status' => 'required|in:Aktif,Tidak Aktif',
        ]);

        $jenisAkun->update($request->all());

        return redirect()->route('jenis-akun.index')->with('success', 'Jenis Akun berhasil diperbarui.');
    }

    public function destroy(JenisAkun $jenisAkun)
    {
        try {
            $jenisAkun->delete();
            return response()->json(['success' => 'Jenis Akun berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus. Akun ini mungkin terkait dengan data lain.'], 500);
        }
    }
}

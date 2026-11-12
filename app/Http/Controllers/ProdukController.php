<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;

/**
 * Class ProdukController
 * 
 * Mengelola fitur Manajemen Produk (CRUD) makanan dan minuman,
 * termasuk upload foto menu, penetapan harga, stok, dan kategori.
 * 
 * @package App\Http\Controllers
 */
class ProdukController extends Controller
{
    /**
     * Menampilkan daftar seluruh produk menu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Produk::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('nama_produk', 'like', "%{$search}%");
        }

        if ($request->filled('kategori') && $request->input('kategori') !== 'all') {
            $query->where('kategori', $request->input('kategori'));
        }

        $produks = $query->orderBy('nama_produk', 'asc')->get();

        return view('produk.index', compact('produks'));
    }

    /**
     * Menyimpan produk menu baru ke database (beserta upload foto jika ada).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => ['required', 'string', 'max:100'],
            'kategori'    => ['required', 'string', 'in:makanan,minuman,snack'],
            'harga'       => ['required', 'numeric', 'min:500'],
            'stok'        => ['required', 'integer', 'min:0'],
            'foto'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'kategori.required'    => 'Pilih kategori produk.',
            'harga.required'       => 'Harga produk wajib diisi.',
            'harga.numeric'        => 'Harga produk harus berupa angka.',
            'stok.required'        => 'Stok produk wajib diisi.',
            'stok.integer'         => 'Stok harus berupa bilangan bulat.',
            'foto.image'           => 'File yang diunggah harus berupa gambar.',
            'foto.max'             => 'Ukuran foto maksimal 2MB.',
        ]);

        // Upload Foto Menu
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/produk'), $fileName);
            $validated['foto'] = $fileName;
        }

        Produk::create($validated);

        return redirect()->route('produk.index')
            ->with('success', "Menu '{$validated['nama_produk']}' berhasil ditambahkan!");
    }

    /**
     * Memperbarui data produk menu dan foto jika di-upload ulang.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        $produk = Produk::findOrFail($id);

        $validated = $request->validate([
            'nama_produk' => ['required', 'string', 'max:100'],
            'kategori'    => ['required', 'string', 'in:makanan,minuman,snack'],
            'harga'       => ['required', 'numeric', 'min:500'],
            'stok'        => ['required', 'integer', 'min:0'],
            'foto'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        // Jika upload foto baru, hapus foto lama dan simpan yang baru
        if ($request->hasFile('foto')) {
            if ($produk->foto && file_exists(public_path('uploads/produk/' . $produk->foto))) {
                @unlink(public_path('uploads/produk/' . $produk->foto));
            }
            $file = $request->file('foto');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/produk'), $fileName);
            $validated['foto'] = $fileName;
        }

        $produk->update($validated);

        return redirect()->route('produk.index')
            ->with('success', "Menu '{$produk->nama_produk}' berhasil diperbarui!");
    }

    /**
     * Menghapus produk menu beserta file fotonya dari sistem.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        $produk = Produk::findOrFail($id);
        $nama = $produk->nama_produk;

        if ($produk->foto && file_exists(public_path('uploads/produk/' . $produk->foto))) {
            @unlink(public_path('uploads/produk/' . $produk->foto));
        }

        $produk->delete();

        return redirect()->route('produk.index')
            ->with('success', "Menu '{$nama}' berhasil dihapus.");
    }
}

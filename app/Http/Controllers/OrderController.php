<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Customer;
use App\Models\Member;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * 1. HALAMAN MANAJEMEN PESANAN (INDEX)
     * Menampilkan daftar order dengan Relasi Customer & Details
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'details'])->latest();

        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function($q) use ($keyword) {
                $q->where('no_invoice', 'like', "%{$keyword}%")
                  ->orWhereHas('customer', function($c) use ($keyword) {
                      $c->where('nama', 'like', "%{$keyword}%")
                        ->orWhere('no_hp', 'like', "%{$keyword}%");
                  });
            });
        }

        $orders = $query->paginate(10);
        return view('pesanan.index', compact('orders')); // Sesuaikan jika nama file view berbeda
    }

    /**
     * [PENTING] MENAMPILKAN DETAIL PESANAN
     * Method ini wajib ada agar tidak error saat klik tombol Detail
     */
    public function show($id)
    {
        $order = Order::with(['customer', 'details'])->findOrFail($id);
        return view('pesanan.show', compact('order'));
    }

    /**
     * [PENTING] UPDATE DATA UTAMA (Untuk Pop-up Edit)
     * Method ini menangani form edit yang muncul di Pop-up
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_customer' => 'required|string',
            'status' => 'required|string',
        ]);

        try {
            DB::beginTransaction();
            
            $order = Order::findOrFail($id);
            
            // 1. Update Data Order
            $order->status_order = $request->status;
            $order->catatan = $request->catatan; 
            $order->save();

            // 2. Update Nama Customer
            if ($order->customer) {
                $order->customer->nama = $request->nama_customer;
                $order->customer->save();
            }

            DB::commit();
            return back()->with('success', 'Data pesanan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * 2. CEK STATUS CUSTOMER SEBELUM ORDER
     * (New / Repeat / Member)
     */
    public function check(Request $request)
    {
        // Cek data customer berdasarkan No HP (jika ada input search)
        $customer = Customer::where('no_hp', $request->no_hp)->with('member')->first();

        // Data default untuk view
        $data = [
            'no_hp' => $request->no_hp,
            'customer' => null,
            'status' => 'New Customer',
            'color' => 'text-blue-500 bg-blue-50 border-blue-200',
            'is_member' => false,
            'poin' => 0,
        ];

        if ($customer) {
            $data['customer'] = $customer;
            $data['no_hp'] = $customer->no_hp;
            
            if ($customer->member) {
                $data['status'] = 'MEMBER';
                $data['color'] = 'text-pink-600 bg-pink-100 border-pink-200';
                $data['is_member'] = true;
                $data['poin'] = $customer->member->poin;
            } else {
                $data['status'] = 'Repeat Order';
                $data['color'] = 'text-green-600 bg-green-100 border-green-200';
                $data['is_member'] = false;
            }
        }

        return view('input-order', $data);
    }

    /**
     * 3. SIMPAN ORDER (CORE FUNCTION)
     * Menyimpan ke 3 Tabel sekaligus (Customers -> Orders -> OrderDetails)
     */
    public function store(Request $request)
    {
        // 1. VALIDASI DIHIDUPKAN KEMBALI
        $request->validate([
            'nama_customer' => 'required',
            'no_hp' => 'required',
            'item.*' => 'required',
            'harga.*' => 'required', // Hapus 'numeric' agar tidak error kena "Rp"
        ]);

        try {
            DB::beginTransaction(); // Mulai Transaksi Database

            // ... Simpan Customer ...
            $customer = Customer::firstOrCreate(
                ['no_hp' => $request->no_hp],
                ['nama' => $request->nama_customer]
            );

            // Jika nama berubah, update namanya
            if($customer->nama !== $request->nama_customer) {
                $customer->update(['nama' => $request->nama_customer]);
            }

            // B. Generate Nomor Invoice (Format: INV-20260123-001)
            $count = Order::whereDate('created_at', today())->count() + 1;
            $invoice = 'INV-' . date('Ymd') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

            // C. Hitung Total Harga Bersih (Hapus karakter non-angka)
            $totalHarga = 0;
            if (is_array($request->harga)) {
                $totalHarga = array_sum(array_map(function ($h) {
                    return (int) preg_replace('/[^0-9]/', '', $h);
                }, $request->harga));
            }

            // D. Logika Status & Metode Pembayaran
            $statusPembayaran = $request->status_pembayaran ?? 'Belum Lunas';
            $metodePembayaran = $request->metode_pembayaran ?? 'Tunai';
            
            // Bersihkan input "paid_amount" (uang diterima) dari karakter rupiah
            $inputPaidAmount = $request->paid_amount ? (int) preg_replace('/[^0-9]/', '', $request->paid_amount) : 0;
            
            $jumlahBayar = 0;
            if ($statusPembayaran == 'Lunas') {
                // Jika lunas, jumlah bayar = total tagihan (atau sesuai input user jika lebih besar)
                $jumlahBayar = ($inputPaidAmount > 0) ? $inputPaidAmount : $totalHarga;
            } elseif ($statusPembayaran == 'DP') {
                $jumlahBayar = $inputPaidAmount;
            } else {
                $jumlahBayar = 0;
                $metodePembayaran = null; 
            }

            // E. Simpan ke Tabel Orders
            $order = Order::create([
                'no_invoice' => $invoice,
                'customer_id' => $customer->id,
                'tgl_masuk' => now(),
                'total_harga' => $totalHarga,
                'paid_amount' => $jumlahBayar,
                'metode_pembayaran' => $metodePembayaran,
                'status_pembayaran' => $statusPembayaran,
                'status_order' => 'Proses', // Default status pengerjaan
                'tipe_customer' => $request->tipe_customer,
                'sumber_info' => $request->sumber_info,
                'catatan' => $request->catatan[0] ?? '-', // Catatan umum ambil dari item pertama
                'kasir' => $request->cs ?? 'Admin',
            ]);

            // F. Simpan Detail Item (Looping)
            $items = $request->item;
            if (is_array($items)) {
                for ($i = 0; $i < count($items); $i++) {
                    if (!empty($items[$i])) {
                        $hargaBersih = (int) preg_replace('/[^0-9]/', '', $request->harga[$i] ?? 0);

                        OrderDetail::create([
                            'order_id' => $order->id,
                            'nama_barang' => $items[$i],
                            // Ambil input manual kategori treatment
                            'layanan' => $request->kategori_treatment[$i] ?? 'General', 
                            'harga' => $hargaBersih,
                            'estimasi_keluar' => $request->tanggal_keluar[$i] ?? null,
                            'catatan' => $request->catatan[$i] ?? null,
                            'status' => 'Proses',
                        ]);
                    }
                }
            }

            // G. Tambah Poin Member (Jika Member)
            if ($customer->member) {
                // Tambah total transaksi kumulatif
                $customer->member->increment('total_transaksi', $totalHarga);
                
                // Hitung poin (misal: 1 poin tiap kelipatan 50.000)
                $poinBaru = floor($totalHarga / 50000);
                if ($poinBaru > 0) {
                    $customer->member->increment('poin', $poinBaru);
                }
            }

            DB::commit(); // Simpan permanen ke database

            // Redirect ke halaman List Pesanan
            return redirect()->route('pesanan.index')->with('success', 'Order berhasil disimpan! ' . $invoice);

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan jika error
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * AJAX: CEK CUSTOMER DI HALAMAN INPUT
     */
    public function checkCustomer(Request $request)
    {
        $customer = Customer::with('member')
                    ->where('no_hp', $request->no_hp)
                    ->first();

        if ($customer) {
            $poin = $customer->member ? $customer->member->poin : 0;
            $targetPoin = 8; 
            
            return response()->json([
                'found' => true,
                'nama' => $customer->nama,
                'tipe' => $customer->member ? 'Member' : 'Regular',
                'poin' => $poin,
                'target' => $targetPoin, 
                'bisa_claim' => $poin >= $targetPoin,
                'member_id' => $customer->member ? $customer->member->id : null,
            ]);
        }

        return response()->json(['found' => false]);
    }
    
    // --- FUNGSI PENDUKUNG LAINNYA ---

    public function index(Request $request)
    {
        $query = Order::with(['customer', 'details'])->latest();
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where('no_invoice', 'like', "%{$keyword}%")
                  ->orWhereHas('customer', function($q) use ($keyword) {
                      $q->where('nama', 'like', "%{$keyword}%");
                  });
        }
        $orders = $query->paginate(10);
        return view('pesanan.index', compact('orders'));
    }

    public function updateDetail(Request $request, $id)
    {
        $detail = OrderDetail::findOrFail($id);
        
        if ($request->has('status')) {
            $detail->status = $request->status;
            $detail->save();
        }

        // Cek apakah semua item sudah selesai
        $order = $detail->order;
        $itemBelumSelesai = $order->details()
            ->whereNotIn('status', ['Selesai', 'Diambil'])
            ->count();

        $order->status_order = ($itemBelumSelesai == 0) ? 'Selesai' : 'Proses';
        $order->save();

        return back()->with('success', 'Status berhasil diperbarui');
    }

    // --- FUNGSI TOGGLE WA ---
    public function toggleWa($id, $type)
    {
        $order = Order::findOrFail($id);
        
        if ($type == 1) {
            $order->wa_sent_1 = !$order->wa_sent_1;
        } elseif ($type == 2) {
            $order->wa_sent_2 = !$order->wa_sent_2;
        }
        
        $order->save();
        return back();
    }

    // --- FUNGSI AJAX CEK CUSTOMER ---
    public function checkCustomer(Request $request)
    {
        $customer = Customer::with('member')->where('no_hp', $request->no_hp)->first();

        if ($customer) {
            $poin = $customer->member ? $customer->member->poin : 0;
            $targetPoin = 8; 
            
            return response()->json([
                'found' => true,
                'nama' => $customer->nama,
                'tipe' => $customer->member ? 'Member' : 'Regular',
                'poin' => $poin,
                'target' => $targetPoin, 
                'bisa_claim' => $poin >= $targetPoin,
                'member_id' => $customer->member ? $customer->member->id : null,
            ]);
        }

        return response()->json(['found' => false]);
    }
}
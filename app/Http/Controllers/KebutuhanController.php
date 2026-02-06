<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kebutuhan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class KebutuhanController extends Controller
{
    /**
     * Tampilkan halaman input kebutuhan.
     */
    public function index()
    {
        // 1. Ambil data (Misal: data hari ini saja, atau semua data terbaru)
        $data = Kebutuhan::orderBy('created_at', 'desc')->get();

        // 2. Format ulang agar mudah dibaca JS (terutama tanggal)
        $formattedData = $data->map(function ($item) {
            return [
                'id' => $item->id, // ID Database
                'nama' => $item->nama_kebutuhan,
                'stok' => $item->stok_terakhir,
                'tanggal' => Carbon::parse($item->tanggal)->format('d/m/Y'),
            ];
        });

        // 3. Kirim ke view
        return view('kebutuhan', ['kebutuhans' => $formattedData]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'stok' => 'required|string|max:255',
            'tanggal' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        try {
            $tanggal = Carbon::createFromFormat('d/m/Y', $request->tanggal)->format('Y-m-d');

            // LOGIKA UPDATE ATAU CREATE
            // Jika ada 'id' dikirim, maka Update. Jika tidak, Create baru.
            $kebutuhan = Kebutuhan::updateOrCreate(
                ['id' => $request->id], // Kunci pencarian (ID)
                [
                    'nama_kebutuhan' => $request->nama,
                    'stok_terakhir'  => $request->stok,
                    'tanggal'        => $tanggal,
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disimpan',
                'data' => $kebutuhan
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
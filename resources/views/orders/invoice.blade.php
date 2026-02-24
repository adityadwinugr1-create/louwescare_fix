<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->no_invoice }}</title>
    <style>
        /* Reset & Base Font */
        body {
            font-family: "Helvetica", "Arial", sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 10px;
            background-color: #f3f4f6; /* Background abu untuk pratinjau monitor */
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Container Kertas Struk (80mm) */
        .invoice-container {
            width: 80mm;
            background-color: #fff;
            padding: 5mm; 
            box-sizing: border-box;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            line-height: 1.2;
        }

        /* Teks & Formatting */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        
        .font-bold { font-weight: bold; }
        .font-normal { font-weight: normal; }
        .italic { font-style: italic; }
        .uppercase { text-transform: uppercase; }
        .underline { text-decoration: underline; }
        
        .text-xl { font-size: 20px; }
        .text-2xl { font-size: 24px; }
        .text-sm { font-size: 14px; }
        .text-xs { font-size: 12px; }
        .text-\[10px\] { font-size: 10px; }
        .text-\[9px\] { font-size: 9px; }
        .text-\[11px\] { font-size: 11px; }

        /* Margin & Padding */
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 12px; }
        .mb-4 { margin-bottom: 16px; }
        .mb-6 { margin-bottom: 24px; }
        .mt-1 { margin-top: 4px; }
        .mt-2 { margin-top: 8px; }
        .mt-6 { margin-top: 24px; }
        .py-1 { padding-top: 4px; padding-bottom: 4px; }
        .py-2 { padding-top: 8px; padding-bottom: 8px; }
        .pr-1 { padding-right: 4px; }
        .pr-2 { padding-right: 8px; }
        .p-1 { padding: 4px; }

        /* Flexbox */
        .flex { display: flex; justify-content: space-between; align-items: flex-start; }
        .flex-center { display: flex; justify-content: center; align-items: center; width: 100%; }
        .items-end { align-items: flex-end; }
        .gap-4 { gap: 16px; }
        
        /* Width */
        .w-full { width: 100%; }
        .w-1\/2 { width: 50%; }
        .w-5\/12 { width: 41.66%; }
        .w-3\/12 { width: 25%; }
        .w-2\/12 { width: 16.66%; }

        /* Lines & Borders */
        .dashed-line { border-bottom: 1px dashed #000; }
        .thick-line { border-bottom: 2px solid #000; }
        .border-b { border-bottom: 1px solid #000; }
        .border-black { border: 1px solid #000; }

        /* Tracking */
        .tracking-widest { letter-spacing: 0.1em; }
        .tracking-wide { letter-spacing: 0.025em; }
        .leading-tight { line-height: 1.2; }

        /* Table */
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td, th { vertical-align: top; word-wrap: break-word; overflow-wrap: break-word; }
        th:nth-child(1), td:nth-child(1), th:nth-child(2), td:nth-child(2) { text-align: left; padding-left: 0 !important; }

        /* List */
        ul { padding-left: 15px; margin: 5px 0; }
        li { margin-bottom: 2px; }
        
        /* Image */
        img { display: block; margin: 0 auto; max-width: 60mm; height: auto; }

        /* Screen-only buttons */
        .no-print { margin-top: 20px; display: flex; gap: 10px; width: 80mm; }
        
        /* Colors (Akan tercetak hitam putih pada printer thermal) */
        .text-gray-500 { color: #6b7280; }
        .text-gray-600 { color: #4b5563; }
        .text-gray-700 { color: #374151; }
        .text-gray-800 { color: #1f2937; }

        /* Print Settings */
        @media print {
            @page { size: 80mm auto; margin: 0; }
            body { background-color: white; margin: 0 !important; padding: 0 !important; display: block; }
            .invoice-container { width: 100%; box-shadow: none; padding: 0; }
            .no-print { display: none !important; }
            table, tr, td, th, p, div { page-break-inside: avoid !important; break-inside: avoid !important; }
            html, body { height: auto !important; min-height: 0 !important; overflow: visible !important; }
            thead { display: table-row-group; }
        }
    </style>
</head>
<body>

    <div class="invoice-container">
        <div class="text-center mb-2">
            <div class="flex-center mb-2">
                <img src="{{ asset('assets/icons/logolouwes.png') }}" alt="Logo" style="width: 120px; height: auto;">
            </div>
            <h2 class="text-xl font-bold tracking-widest uppercase mb-1">LOUWES CARE</h2>
            <p class="font-bold text-[10px] text-gray-600 uppercase tracking-wide">Clean - Fresh - Better</p>
            <p class="text-[9px] mt-1 text-gray-500">Jl. Ringroad Timur No 9, Plumbon, Banguntapan, Bantul, DIY 55196</p>
            <p class="text-[9px] text-gray-500">Instagram: @Louwes Shoes Care | WA: 081390154885</p>
        </div>

        <div class="thick-line mb-3"></div>

        <div class="flex items-end mb-4">
            <div class="text-sm font-bold">
                CS Masuk: <span class="font-normal">{{ $order->kasir ?? '-' }}</span><br>
                CS Keluar: <span class="font-normal">{{ $order->kasir_keluar ?? '-' }}</span>
            </div>
            <div class="text-2xl font-bold tracking-widest">INVOICE</div>
        </div>

        <div class="border-b mb-2"></div>

        <div class="flex mb-4 text-[11px]">
            <div class="w-1/2">
                <div class="font-bold mb-1">CUSTOMER:</div>
                <div class="uppercase font-bold text-sm">{{ $order->customer->nama ?? '-' }}</div>
                <div>{{ $order->customer->no_hp ?? '-' }}</div>
            </div>
            <div class="w-1/2 text-right">
                <div class="font-bold mb-1">DETAILS:</div>
                <div>No: <span class="font-bold">{{ $order->no_invoice }}</span></div>
                <div>Date: <span>{{ $order->created_at->format('d/m/Y') }}</span></div>
            </div>
        </div>

        <div class="mb-4">
            <table class="w-full text-left text-[10px]">
                <thead>
                    <tr class="dashed-line text-gray-600 uppercase">
                        <th class="py-2 w-5/12 font-bold">ITEM & CATATAN</th>
                        <th class="py-2 w-3/12 font-bold">TREATMENT</th>
                        <th class="py-2 w-2/12 text-center font-bold">EST JADI</th>
                        <th class="py-2 w-2/12 text-right font-bold">HARGA</th>
                    </tr>
                </thead>
                <tbody class="dashed-line">
                    @php
                        // Grouping logic 
                        $groupedDetails = $order->details->groupBy(function($item) {
                            return strtolower(trim($item->nama_barang));
                        });
                        $originalTotal = 0;
                    @endphp

                    @foreach($groupedDetails as $groupName => $details)
                        @php
                            $firstItem = $details->first();
                            $groupPrice = $details->sum('harga');
                            $originalTotal += $groupPrice;
                            
                            $layananList = $details->pluck('layanan')->unique()->implode(' + ');
                            $catatanList = $details->pluck('catatan')->filter(fn($c) => $c && $c !== '-')->implode(', ');
                            
                            $estDate = $details->max('estimasi_keluar');
                            $estStr = $estDate ? \Carbon\Carbon::parse($estDate)->format('d/m/Y') : '-';
                        @endphp
                        <tr>
                            <td class="border-b py-1 pr-1">
                                <span>{{ $firstItem->nama_barang }}</span>
                                @if($catatanList)
                                    <br><span style="font-size: 9px;">Catatan: {{ $catatanList }}</span>
                                @endif
                            </td>
                            <td class="border-b py-1 text-[10px]">{{ $layananList }}</td>
                            <td class="border-b py-1 text-center text-[10px]">
                                {{ $estStr }}
                                
                                @php
                                    // Hitung berapa item dalam grup ini yang statusnya sudah 'Diambil'
                                    $diambilCount = $details->where('status', 'Diambil')->count();
                                    $totalItem = $details->count();
                                @endphp

                                @if($diambilCount > 0)
                                    <br>
                                    <span class="font-bold text-[12px]">✓</span>
                                    {{-- Jika item yang sama ada lebih dari 1, dan baru diambil sebagian, tampilkan angkanya (misal: ✓ 1/2) --}}
                                    @if($totalItem > 1)
                                        <span class="text-[9px]">({{ $diambilCount }}/{{ $totalItem }})</span>
                                    @endif
                                @endif
                            </td>
                            <td class="border-b py-1 text-right">{{ number_format($groupPrice, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach

                    {{-- Logic Parfum Claim --}}
                    @php
                        $claimType = $order->klaim;
                        $qtyParfum = 0;
                        if ($claimType && stripos($claimType, 'Parfum') !== false) {
                            if (preg_match('/(\d+)\s*x\s*Parfum/i', $claimType, $m)) {
                                $qtyParfum = (int)$m[1];
                            } else {
                                $qtyParfum = 1;
                            }
                        }
                    @endphp

                    @if($qtyParfum > 0)
                    <tr>
                        <td class="border-b py-1 pr-1"><span>{{ $qtyParfum }}x Free Parfum</span></td>
                        <td class="border-b py-1 text-[10px]">-</td>
                        <td class="border-b py-1 text-center text-[10px]">-</td>
                        <td class="border-b py-1 text-right">0</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="flex justify-end mb-6">
            <div class="w-full">
                <table class="w-full text-[11px]">
                    <tr>
                        <td class="py-1 text-right pr-2 text-gray-600">Subtotal :</td>
                        <td class="py-1 text-right">{{ number_format($originalTotal, 0, ',', '.') }}</td>
                    </tr>

                    @php
                        $discountAmount = $originalTotal - $order->total_harga;
                        $qtyDiskon = 0;
                        if ($claimType && stripos($claimType, 'Diskon') !== false) {
                            if (preg_match('/(\d+)\s*x\s*Diskon/i', $claimType, $m)) {
                                $qtyDiskon = (int)$m[1];
                            } else {
                                $qtyDiskon = 1;
                            }
                        }
                    @endphp

                    @if($discountAmount > 0)
                    <tr class="dashed-line">
                        <td class="py-1 text-right pr-2 text-gray-600">{{ $qtyDiskon > 0 ? $qtyDiskon.'x ' : '' }}Diskon :</td>
                        <td class="py-1 text-right">- {{ number_format($discountAmount, 0, ',', '.') }}</td>
                    </tr>
                    @endif

                    <tr>
                        <td class="py-2 text-sm text-right pr-2 font-bold">TOTAL :</td>
                        <td class="py-2 text-sm text-right font-bold">{{ number_format($order->total_harga, 0, ',', '.') }}</td>
                    </tr>

                    @if($order->status_pembayaran == 'DP')
                        <tr>
                            <td class="py-1 text-right pr-2 text-gray-600 font-bold">DP Dibayar <span class="font-normal italic text-[9px]">(via {{ $order->metode_pembayaran ?? '-' }})</span> :</td>
                            <td class="py-1 text-right font-bold">{{ number_format($order->paid_amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="dashed-line">
                            <td class="py-1 text-right pr-2 text-gray-800 font-bold italic">SISA TAGIHAN :</td>
                            <td class="py-1 text-right text-gray-800 font-bold italic">{{ number_format($order->total_harga - $order->paid_amount, 0, ',', '.') }}</td>
                        </tr>
                    @else
                        <tr>
                            <td class="py-1 uppercase text-right pr-2 font-bold">{{ $order->status_pembayaran ? strtoupper($order->status_pembayaran) : '-' }}</td>
                            <td class="py-1 text-right text-[10px]">via {{ $order->metode_pembayaran ?? '-' }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>

        <div class="dashed-line mb-3"></div>

        <div class="flex flex-col gap-3 text-[9px] text-gray-700">
            <div class="w-full">
                <p class="font-bold mb-1">"Jika sudah tanggal deadline tetapi belum kami hubungi, mohon WA kami"</p>
                <p class="italic">*Simpan nota ini sebagai bukti pengambilan</p>
                
                @if($claimType)
                <div class="mt-2 font-bold border-black p-1 text-center">
                    *** REWARD: {{ strtoupper($claimType) }} ***
                </div>
                @endif
            </div>
            
            <div class="w-full">
                <p class="font-bold underline mb-1">NB (Syarat & Ketentuan):</p>
                <ul class="leading-tight">
                    <li>Barang rusak karena bahan sudah rapuh bukan tanggungjawab kami.</li>
                    <li>Apabila barang tidak diambil lebih dari 3 Bulan setelah jadi, hilang bukan tanggung jawab kami.</li>
                </ul>
            </div>
        </div>

        <div class="text-center mt-6 text-[10px] text-gray-500">-- Terima Kasih --</div>
    </div>

    <div class="no-print" style="margin-top: 20px; display: flex; gap: 10px; width: 80mm;">
        <button onclick="window.print()" style="flex: 1; background-color: #1f2937; color: white; padding: 10px; border-radius: 5px; font-weight: bold; border: none; cursor: pointer;">Cetak</button>
        <a href="{{ route('pesanan.index') }}" style="flex: 1; background-color: #fee2e2; color: #dc2626; padding: 10px; border-radius: 5px; font-weight: bold; text-align: center; text-decoration: none;">Tutup</a>
    </div>

    <script>
        window.onload = function() {
            // Memberi jeda sedikit agar font dan gambar (terutama logo) ter-load sepenuhnya sebelum diprint.
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
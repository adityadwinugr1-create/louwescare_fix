<x-app-layout>
    {{-- Library SheetJS untuk Export Excel --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <style>
        @media print {
            @page { size: landscape; margin: 5mm; }
            /* Sembunyikan elemen navigasi, filter, dan tombol saat mencetak */
            nav, aside, .no-print, form { display: none !important; }
            /* Pastikan layout lebar penuh */
            .max-w-7xl { max-width: 100% !important; padding: 0 !important; margin: 0 !important; }
            body { background-color: white !important; font-size: 9px; }
            .shadow-sm, .shadow-md, .border { box-shadow: none !important; border: none !important; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid black !important; padding: 3px !important; }
            .print-hidden { display: none !important; }
        }
    </style>

    <div class="py-6" x-data="{ showFilterModal: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- HEADER & FILTER SECTION --}}
            <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4">
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 uppercase tracking-wide">Laporan Pendapatan</h2>
                    <p class="text-sm text-gray-500">
                        Periode: 
                        {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                    </p>
                </div>

                <div class="flex items-center gap-2 no-print">
                    {{-- TOMBOL FILTER POPUP --}}
                    <button @click="showFilterModal = true" class="bg-[#003d4d] text-white px-4 py-2 rounded-lg hover:bg-cyan-800 transition shadow-sm flex items-center gap-2 h-full font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        Filter
                    </button>

                    {{-- TOMBOL EXPORT EXCEL --}}
                    <button onclick="exportToExcel()" class="bg-green-700 text-white px-4 py-2 rounded-lg hover:bg-green-900 transition shadow-sm flex items-center gap-2 h-full font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.586l4 4a1 1 0 01.586 1.414V19a2 2 0 01-2 2z"></path></svg>
                        Export Excel
                    </button>
                </div>
            </div>

            {{-- TOTAL PENDAPATAN SECTION --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 print-hidden">
                <div class="p-6 flex flex-col md:flex-row justify-between items-center">
                    <div>
                        <h3 class="text-gray-500 font-bold uppercase tracking-wider text-sm">Total Pendapatan (Terfilter)</h3>
                        <p class="text-xs text-gray-400 mt-1">Total uang masuk (Paid Amount) pada periode ini</p>
                    </div>
                    <div class="text-4xl font-black text-emerald-600 mt-4 md:mt-0">
                        Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            
            {{-- TABEL TRANSAKSI --}}
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-bold text-gray-700">Rincian Transaksi</h3>
                </div>
                <div class="overflow-x-auto">
                    <table id="table-laporan" class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b">
                            <tr>
                                <th class="px-3 py-3">ID</th>
                                <th class="px-3 py-3">Nama Cust</th>
                                <th class="px-3 py-3">No WA</th>
                                <th class="px-3 py-3">Item</th>
                                <th class="px-3 py-3">Catatan</th>
                                <th class="px-3 py-3">Treatment</th>
                                <th class="px-3 py-3 text-right">Harga</th>
                                <th class="px-3 py-3">Ket. Bayar</th>
                                <th class="px-3 py-3">Waktu Masuk</th>
                                <th class="px-3 py-3">Waktu Keluar</th>
                                <th class="px-3 py-3 text-center">WA Nota</th>
                                <th class="px-3 py-3 text-center">WA Ambil</th>
                                <th class="px-3 py-3">Kategori</th>
                                <th class="px-3 py-3">Tipe Cust</th>
                                <th class="px-3 py-3 text-right">Jml DP/Bayar</th>
                                <th class="px-3 py-3">Sumber Info</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-3 py-3 font-bold text-xs">{{ $order->no_invoice }}</td>
                                    <td class="px-3 py-3">{{ $order->customer->nama ?? '-' }}</td>
                                    <td class="px-3 py-3 text-xs">{{ $order->customer->no_hp ?? '-' }}</td>
                                    <td class="px-3 py-3 text-xs">
                                        @foreach($order->details as $d)
                                            <div class="whitespace-nowrap">- {{ $d->nama_barang }}</div>
                                        @endforeach
                                    </td>
                                    <td class="px-3 py-3 text-xs italic max-w-[150px] truncate hover:whitespace-normal">{{ $order->catatan ?? '-' }}</td>
                                    <td class="px-3 py-3 text-xs">
                                        @foreach($order->details as $d)
                                            <div class="whitespace-nowrap">{{ $d->layanan }}</div>
                                        @endforeach
                                    </td>
                                    <td class="px-3 py-3 text-right font-bold">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                                    <td class="px-3 py-3 text-xs">
                                        <span class="font-bold block">{{ $order->status_pembayaran }}</span>
                                        <span class="text-[10px] text-gray-500">{{ $order->metode_pembayaran ?? '-' }}</span>
                                    </td>
                                    
                                    {{-- Kolom Waktu Masuk --}}
                                    <td class="px-3 py-3 text-xs">
                                        <span class="block font-medium text-gray-700">{{ $order->created_at->format('d/m/y') }}</span>
                                        <span class="text-[10px] text-gray-400 font-bold">{{ $order->created_at->format('H:i') }} WIB</span>
                                    </td>
                                    
                                    {{-- Kolom Waktu Keluar (Estimasi) --}}
                                    <td class="px-3 py-3 text-xs">
                                        @if($order->waktu_diambil)
                                            <span class="block font-medium text-emerald-600">{{ \Carbon\Carbon::parse($order->waktu_diambil)->format('d/m/y') }}</span>
                                            <span class="text-[10px] text-emerald-500 font-bold">{{ \Carbon\Carbon::parse($order->waktu_diambil)->format('H:i') }} WIB</span>
                                        @else
                                            <span class="text-[10px] text-gray-400 font-bold px-2 py-1 bg-gray-100 rounded">Belum Diambil</span>
                                        @endif
                                    </td>
                                    
                                    <td class="px-3 py-3 text-center text-xs">
                                        {{ $order->wa_sent_1 ? 'Sudah' : 'Belum' }}
                                    </td>
                                    <td class="px-3 py-3 text-center text-xs">
                                        {{ $order->wa_sent_2 ? 'Sudah' : 'Belum' }}
                                    </td>
                                    <td class="px-3 py-3 text-xs">{{ $order->tipe_customer }}</td>
                                    <td class="px-3 py-3 text-xs">{{ $order->customer->tipe ?? '-' }}</td>
                                    <td class="px-3 py-3 text-right font-bold">Rp {{ number_format($order->paid_amount, 0, ',', '.') }}</td>
                                    <td class="px-3 py-3 text-xs">{{ $order->customer->sumber_info ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="16" class="px-6 py-8 text-center text-gray-500 italic">Tidak ada data transaksi pada periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- MODAL FILTER (RESPONSIVE MOBILE & DESKTOP) --}}
            <div x-show="showFilterModal" 
                 class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm sm:p-4"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
                 style="display: none;">
                 
                {{-- Container Modal --}}
                <div class="bg-white w-full max-w-2xl rounded-t-3xl sm:rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col" @click.away="showFilterModal = false">
                    
                    <form id="filterForm" action="{{ route('owner.laporan') }}" method="GET" class="flex flex-col h-full overflow-hidden">
                        
                        {{-- Modal Header (Sticky di Atas) --}}
                        <div class="bg-gray-100 px-5 sm:px-6 py-4 border-b border-gray-200 flex justify-between items-center flex-shrink-0">
                            <h3 class="font-bold text-lg sm:text-xl text-gray-800 uppercase tracking-wide">Filter Laporan</h3>
                            <button type="button" @click="showFilterModal = false" class="text-gray-400 hover:text-gray-700 text-3xl leading-none font-bold transition">&times;</button>
                        </div>

                        {{-- Modal Body (Area Scrollable) --}}
                        <div class="p-5 sm:p-6 space-y-5 overflow-y-auto flex-1">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                
                                {{-- BARIS 1: Tanggal Masuk & Keluar --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tanggal Masuk</label>
                                    <div class="flex flex-row gap-2">
                                        <input type="date" name="tgl_masuk_start" value="{{ request('tgl_masuk_start', $startDate) }}" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500">
                                        <input type="date" name="tgl_masuk_end" value="{{ request('tgl_masuk_end', $endDate) }}" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tanggal Keluar (Estimasi)</label>
                                    <div class="flex flex-row gap-2">
                                        <input type="date" name="tgl_keluar_start" value="{{ request('tgl_keluar_start') }}" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500">
                                        <input type="date" name="tgl_keluar_end" value="{{ request('tgl_keluar_end') }}" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500">
                                    </div>
                                </div>

                                {{-- BARIS 2: Range Harga --}}
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Range Harga (Rp)</label>
                                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 items-center">
                                        <input type="number" name="min_harga" placeholder="Minimal Rp" value="{{ request('min_harga') }}" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500">
                                        <span class="text-gray-400 font-bold hidden sm:block">-</span>
                                        <input type="number" name="max_harga" placeholder="Maksimal Rp" value="{{ request('max_harga') }}" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500">
                                    </div>
                                </div>

                                {{-- BARIS 3: Kategori Customer & Treatment --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Kategori Customer</label>
                                    <select name="kategori_customer" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500">
                                        <option value="">Semua Kategori</option>
                                        <option value="Member" {{ request('kategori_customer') == 'Member' ? 'selected' : '' }}>Member</option>
                                        <option value="Repeat Order" {{ request('kategori_customer') == 'Repeat Order' ? 'selected' : '' }}>Repeat Order</option>
                                        <option value="New Customer" {{ request('kategori_customer') == 'New Customer' ? 'selected' : '' }}>New Customer</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Treatment</label>
                                    {{-- Box Scrollable untuk Checkbox (Multi-select) --}}
                                    <div class="w-full max-h-48 overflow-y-auto border border-gray-300 rounded-lg p-2 space-y-1 bg-white">
                                        @php 
                                            $selectedTreatments = request('treatment', []); 
                                            if(!is_array($selectedTreatments)) {
                                                $selectedTreatments = [$selectedTreatments];
                                            }
                                        @endphp
                                        
                                        @foreach($treatments as $t)
                                            <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 p-2 rounded-md transition">
                                                <input type="checkbox" name="treatment[]" value="{{ $t->nama_treatment }}" 
                                                    {{ in_array($t->nama_treatment, $selectedTreatments) ? 'checked' : '' }}
                                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                                                <span class="text-sm text-gray-700">{{ $t->nama_treatment }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-1 italic">*Centang beberapa opsi untuk filter ganda</p>
                                </div>

                            </div>

                            {{-- Komplain Checkbox --}}
                            <div class="flex items-start sm:items-center gap-3 bg-red-50 p-4 rounded-xl border border-red-100">
                                <input type="checkbox" name="komplain" id="komplain" value="1" {{ request('komplain') ? 'checked' : '' }} class="mt-0.5 sm:mt-0 rounded border-gray-300 text-red-600 focus:ring-red-500 w-5 h-5">
                                <label for="komplain" class="text-sm font-bold text-red-700 select-none cursor-pointer leading-snug">Tampilkan pesanan yang memiliki Komplain / Catatan Khusus</label>
                            </div>

                        </div>

                        {{-- Footer Buttons (Sticky di Bawah) --}}
                        <div class="bg-gray-50 border-t border-gray-200 px-5 py-4 flex flex-col-reverse sm:flex-row justify-between gap-3 flex-shrink-0">
                            <button type="button" onclick="clearFilterForm()" class="w-full sm:w-auto justify-center px-5 py-2.5 text-red-500 font-bold hover:bg-red-100 rounded-lg transition flex items-center border border-red-100 sm:border-transparent">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Reset Filter
                            </button>
                            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 w-full sm:w-auto">
                                <button type="button" @click="showFilterModal = false" class="w-full sm:w-auto justify-center px-5 py-2.5 text-gray-600 font-bold bg-white border border-gray-300 hover:bg-gray-100 rounded-lg transition">Batal</button>
                                <button type="submit" class="w-full sm:w-auto justify-center px-6 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-lg transition">
                                    Terapkan Filter
                                </button>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        function exportToExcel() {
            var table = document.getElementById("table-laporan");
            var wb = XLSX.utils.table_to_book(table, { sheet: "Laporan" });
            XLSX.writeFile(wb, "Laporan_Pendapatan.xlsx");
        }

        function clearFilterForm() {
            const form = document.getElementById('filterForm');
            form.querySelectorAll('input:not([type=checkbox]):not([type=radio]), select').forEach(el => el.value = '');
            form.querySelectorAll('input[type=checkbox], input[type=radio]').forEach(el => el.checked = false);
        }
    </script>
</x-app-layout>
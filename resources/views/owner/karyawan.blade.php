<x-app-layout>
    <div class="py-6 px-4 sm:px-0" x-data="{ openEdit: false, currId: null, currNama: '' }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <h1 class="text-2xl sm:text-4xl font-bold text-[#7FB3D5] mb-6 sm:mb-8 text-center sm:text-left">Manajemen Karyawan</h1>

            {{-- FORM TAMBAH --}}
            <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm mb-6 sm:mb-8 border border-gray-100">
                {{-- Di HP form menurun (flex-col), di Tablet/Laptop menyamping (sm:flex-row) --}}
                <form action="{{ route('owner.karyawan.store') }}" method="POST" class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                    @csrf
                    <input type="text" name="nama_karyawan" placeholder="Masukkan Nama Karyawan Baru..." required
                        class="w-full sm:flex-1 p-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-400 outline-none transition text-sm sm:text-base">
                    <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-[#3b66ff] text-white font-bold rounded-xl hover:bg-blue-700 transition shadow-md flex justify-center items-center">
                        + Tambah Karyawan
                    </button>
                </form>
            </div>

            {{-- TABEL KARYAWAN --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                {{-- Wrapper agar tabel bisa digeser (scroll) ke kanan/kiri di HP --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[300px]">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-bold text-gray-500 uppercase">Nama Karyawan</th>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-bold text-gray-500 uppercase text-center w-24 sm:w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($karyawans as $k)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-700 font-medium text-sm sm:text-base">{{ $k->nama_karyawan }}</td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 flex justify-center gap-2 sm:gap-3">
                                    {{-- EDIT BUTTON --}}
                                    <button @click="openEdit = true; currId = '{{ $k->id }}'; currNama = '{{ $k->nama_karyawan }}'"
                                        class="p-2 sm:p-2 text-yellow-500 hover:bg-yellow-50 hover:text-yellow-600 rounded-lg transition">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                    
                                    {{-- DELETE BUTTON --}}
                                    <form action="{{ route('owner.karyawan.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Hapus karyawan ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 sm:p-2 text-red-500 hover:bg-red-50 hover:text-red-600 rounded-lg transition">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="px-6 py-8 text-center text-gray-400 text-sm italic">Belum ada data karyawan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- MODAL EDIT (ALPINE JS) --}}
            {{-- Di HP modal bergaya Bottom Sheet (menempel di bawah layar), di desktop tetap di tengah --}}
            <div x-show="openEdit" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 p-0 sm:p-4" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
                 style="display: none;">
                
                <div class="bg-white w-full max-w-md rounded-t-3xl sm:rounded-3xl p-6 sm:p-8 shadow-2xl pb-8 sm:pb-8" @click.away="openEdit = false">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Edit Karyawan</h2>
                        {{-- Tombol (X) silang khusus HP agar mudah ditutup --}}
                        <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-gray-600 sm:hidden">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <form :action="'/owner/karyawan/' + currId" method="POST">
                        @csrf @method('PUT')
                        <input type="text" name="nama_karyawan" x-model="currNama" required
                            class="w-full p-3 sm:p-4 bg-gray-50 border border-gray-200 rounded-xl sm:rounded-2xl mb-6 outline-none focus:ring-2 focus:ring-blue-400 text-sm sm:text-base">
                        
                        {{-- Di HP tombol tersusun vertikal (flex-col-reverse), di desktop horizontal --}}
                        <div class="flex flex-col-reverse sm:flex-row gap-3 sm:gap-4">
                            <button type="button" @click="openEdit = false" class="w-full sm:flex-1 py-3 text-gray-500 font-bold hover:bg-gray-100 rounded-xl transition border border-gray-200 sm:border-transparent">Batal</button>
                            <button type="submit" class="w-full sm:flex-1 py-3 bg-[#3b66ff] text-white font-bold rounded-xl hover:bg-blue-700 shadow-lg transition">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
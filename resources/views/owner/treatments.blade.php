<x-app-layout>
    <div class="p-6 bg-white min-h-screen">
        {{-- Header Halaman --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-[#7FB3D5]">Manajemen Treatment</h1>
            <button onclick="openModal('add')" class="bg-[#3b66ff] text-white px-6 py-2 rounded-lg font-bold shadow-md hover:bg-blue-700 transition">
                + Tambah Treatment
            </button>
        </div>

        {{-- Tabel Data Treatment --}}
        <div class="overflow-x-auto bg-white rounded-xl shadow-md border border-gray-200">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="p-4 text-sm font-bold text-gray-600 uppercase w-1/3">Kategori</th>
                        <th class="p-4 text-sm font-bold text-gray-600 uppercase w-1/2">Nama Layanan</th>
                        <th class="p-4 text-sm font-bold text-gray-600 uppercase text-center w-1/6">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($treatments as $t)
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="p-4 font-medium text-gray-800">
                            <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-bold uppercase border border-blue-100">
                                {{ $t->kategori }}
                            </span>
                        </td>
                        <td class="p-4 text-gray-700 font-semibold">{{ $t->nama_treatment }}</td>
                        <td class="p-4 flex justify-center gap-2">
                            {{-- Tombol Edit --}}
                            <button onclick="editTreatment({{ $t }})" class="bg-yellow-400 text-white p-2 rounded-lg shadow hover:bg-yellow-500 transition" title="Edit Layanan">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                            
                            {{-- Tombol Hapus --}}
                            <form action="{{ route('owner.treatments.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan ini?')">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white p-2 rounded-lg shadow hover:bg-red-600 transition" title="Hapus Layanan">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="p-10 text-center text-gray-500 italic">Belum ada data treatment yang tersedia.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL FORM (ADD & EDIT) --}}
    <div id="modal-treatment" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60 hidden" onclick="closeModalOutside(event)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden animate-fade-in" onclick="event.stopPropagation()">
            {{-- Modal Header --}}
            <div class="bg-[#3b66ff] p-4 flex justify-between items-center text-white">
                <h3 id="modal-title" class="font-bold text-lg">Tambah Treatment</h3>
                <button onclick="closeModal()" class="text-2xl font-bold hover:text-gray-200 transition">&times;</button>
            </div>

            {{-- Modal Body --}}
            <form id="form-treatment" method="POST" class="p-6 space-y-5">
                @csrf
                <div id="method-field"></div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wider">Kategori Layanan</label>
                    
                    {{-- Dropdown Kategori Utama --}}
                    <select id="select-kategori" name="kategori" class="w-full border-gray-300 rounded-lg focus:ring-[#3b66ff] focus:border-[#3b66ff] cursor-pointer" onchange="toggleKategoriBaru()" required>
                        <option value="" disabled selected>-- Pilih Kategori --</option>
                        @php 
                            $kategoriUnik = $treatments->pluck('kategori')->unique(); 
                        @endphp
                        
                        @foreach($kategoriUnik as $kat)
                            <option value="{{ $kat }}">{{ $kat }}</option>
                        @endforeach
                        
                        <option disabled>──────────</option>
                        <option value="TAMBAH_BARU" class="font-bold text-blue-600 bg-blue-50">+ Buat Kategori Baru...</option>
                    </select>

                    {{-- Input Text (Muncul jika pilih "+ Buat Kategori Baru...") --}}
                    <div id="container-kategori-baru" class="mt-2 hidden flex-row gap-2">
                        <input type="text" id="input-kategori" class="w-full border-gray-300 rounded-lg focus:ring-[#3b66ff] focus:border-[#3b66ff] placeholder-gray-400 text-sm" placeholder="Ketik nama kategori baru disini...">
                        
                        {{-- Tombol Batal yang lebih jelas, responsif, dan diletakkan bersebelahan --}}
                        <button type="button" onclick="batalKategoriBaru()" class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 rounded-lg font-bold text-sm transition shadow-sm whitespace-nowrap flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Batal
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wider">Nama Treatment</label>
                    <input type="text" name="nama_treatment" id="input-nama" class="w-full border-gray-300 rounded-lg focus:ring-[#3b66ff] focus:border-[#3b66ff] placeholder-gray-400" placeholder="Cth: Deep Clean Small / Leather Care" required>
                </div>
                
                {{-- Footer Modal --}}
                <div class="flex justify-end gap-3 pt-6 border-t">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-600 font-bold hover:text-gray-800 transition">Batal</button>
                    <button type="submit" class="bg-[#3b66ff] text-white px-8 py-2 rounded-lg font-bold shadow-lg hover:bg-blue-700 transition transform hover:scale-105 active:scale-95">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('modal-treatment');
        const form = document.getElementById('form-treatment');
        const title = document.getElementById('modal-title');
        const methodField = document.getElementById('method-field');
        
        // Element Kategori
        const selectKategori = document.getElementById('select-kategori');
        const containerKategoriBaru = document.getElementById('container-kategori-baru');
        const inputKategori = document.getElementById('input-kategori');

        // Fungsi untuk mengontrol perpindahan input kategori
        // Fungsi untuk mengontrol perpindahan input kategori
        function toggleKategoriBaru() {
            if (selectKategori.value === 'TAMBAH_BARU') {
                // Tampilkan input text (ganti menggunakan flex agar sejajar)
                containerKategoriBaru.classList.remove('hidden');
                containerKategoriBaru.classList.add('flex'); // Tambahan baru
                
                // Pindahkan atribut 'name' dan 'required' agar Laravel membaca input text ini
                inputKategori.setAttribute('name', 'kategori');
                inputKategori.setAttribute('required', 'required');
                
                // Cabut 'name' dari select agar tidak bentrok
                selectKategori.removeAttribute('name');
                selectKategori.removeAttribute('required');
                
                inputKategori.focus();
            } else {
                // Sembunyikan input text
                containerKategoriBaru.classList.add('hidden');
                containerKategoriBaru.classList.remove('flex'); // Tambahan baru
                
                // Kembalikan atribut 'name' ke select
                inputKategori.removeAttribute('name');
                inputKategori.removeAttribute('required');
                inputKategori.value = ''; 
                
                selectKategori.setAttribute('name', 'kategori');
                selectKategori.setAttribute('required', 'required');
            }
        }

        // Fungsi jika user batal membuat kategori baru
        function batalKategoriBaru() {
            selectKategori.value = ""; // Reset dropdown ke opsi "-- Pilih Kategori --"
            toggleKategoriBaru();      // Jalankan fungsi sembunyikan input text
        }

        function openModal(type) {
            modal.classList.remove('hidden');
            if (type === 'add') {
                title.innerText = "Tambah Treatment";
                form.action = "{{ route('owner.treatments.store') }}";
                methodField.innerHTML = "";
                form.reset();
                
                // Reset form kategori UI ke mode normal
                selectKategori.value = "";
                toggleKategoriBaru();
            }
        }

        function editTreatment(data) {
            modal.classList.remove('hidden');
            title.innerText = "Edit Treatment";
            form.action = `/owner/treatments/${data.id}`;
            methodField.innerHTML = `<input type="hidden" name="_method" value="PUT">`;
            
            // Set value ke dropdown
            selectKategori.value = data.kategori;
            toggleKategoriBaru(); // Pastikan input text mode "baru" tersembunyi
            
            document.getElementById('input-nama').value = data.nama_treatment;
        }

        function closeModal() {
            modal.classList.add('hidden');
        }

        function closeModalOutside(event) {
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</x-app-layout>
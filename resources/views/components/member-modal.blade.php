<div id="memberModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 hidden flex items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform scale-95 transition-transform duration-300" id="modalContent">
        
        <div class="bg-[#3b66ff] p-4 flex justify-between items-center">
            <h3 class="text-white font-bold text-lg">Registrasi Member Baru</h3>
            <button type="button" onclick="closeMemberModal()" class="text-white hover:text-gray-200 font-bold text-xl">&times;</button>
        </div>

        <form id="formMemberAjax" onsubmit="submitMemberAjax(event)" class="p-6 space-y-4">
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Customer</label>
                <input type="text" name="nama" id="modalNama" required class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 font-bold text-gray-700" readonly>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">No. HP</label>
                <input type="text" name="no_hp" value="{{ $no_hp ?? '' }}" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-200 text-gray-500 cursor-not-allowed">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Total Belanja</label>
                    <input type="text" id="modalTotalDisplay" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 font-bold text-gray-800 text-right">
                    <input type="hidden" name="initial_total" id="modalTotalValue">
                </div>
                <div>
                    <label class="block text-xs font-bold text-blue-500 uppercase mb-1">Poin Didapat</label>
                    <div class="relative">
                        <input type="number" name="initial_poin" id="modalPoin" readonly class="w-full px-4 py-2 border border-blue-300 rounded-lg bg-blue-50 font-black text-blue-600 text-center text-lg">
                        <span class="absolute right-3 top-2.5 text-xs text-blue-400 font-bold">PTS</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 mt-2">
                <button type="button" onclick="closeMemberModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-bold">Batal</button>
                <button type="submit" id="btnSimpanMember" class="px-6 py-2 bg-[#3b66ff] text-white rounded-lg font-bold shadow-lg hover:bg-blue-700">SIMPAN MEMBER</button>
            </div>
        </form>
    </div>
</div>

<script>
    function closeMemberModal() {
        const modal = document.getElementById('memberModal');
        const content = document.getElementById('modalContent');

        content.classList.remove('scale-100');
        content.classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>
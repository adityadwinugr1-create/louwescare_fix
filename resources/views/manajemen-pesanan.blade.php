<x-app-layout>
    <div class="p-4 md:p-6 bg-gray-50 min-h-screen">
        
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
            <h1 class="text-3xl md:text-4xl font-bold text-[#7FB3D5]">Manajemen Pesanan</h1>
            
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
                <form action="{{ route('pesanan.index') }}" method="GET" class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari ID / Nama..." 
                           class="pl-10 pr-10 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-blue-400 w-full bg-white transition shadow-sm">
                    
                    @if(request('search'))
                        <a href="{{ route('pesanan.index') }}" class="absolute inset-y-0 right-0 flex items-center pr-3 text-red-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    @endif
                </form>

                <div class="flex gap-2 w-full sm:w-auto">
                    <button class="flex items-center justify-center space-x-2 font-semibold text-gray-700 hover:text-blue-600 transition bg-white px-4 py-2 rounded-full border border-gray-300 w-full sm:w-auto shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                        <span>Filter</span>
                    </button>

                    <button 
                        x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'claim-modal')"
                        class="flex items-center justify-center space-x-2 font-semibold text-white bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 transition px-4 py-2 rounded-full border border-orange-300 w-full sm:w-auto shadow-sm"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path></svg>
                        <span>Claim Reward</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="hidden lg:block overflow-hidden bg-white shadow-md rounded-lg border border-gray-200">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="p-4 font-bold text-xs uppercase tracking-wider text-center w-16">ID</th>
                        <th class="p-4 font-bold text-xs uppercase tracking-wider">Tgl Masuk</th>
                        <th class="p-4 font-bold text-xs uppercase tracking-wider">Customer</th>
                        <th class="p-4 font-bold text-xs uppercase tracking-wider">List Sepatu & Treatment</th>
                        <th class="p-4 font-bold text-xs uppercase tracking-wider text-right">Total</th>
                        <th class="p-4 font-bold text-xs uppercase tracking-wider text-center">Status</th>
                        <th class="p-4 font-bold text-xs uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    {{-- FIX: Loop data order yang benar (memperbaiki error "item on bool") --}}
                    @forelse($orders as $order)
                        <tr class="hover:bg-blue-50 transition duration-150">
                            <td class="p-4 text-sm text-center font-bold text-gray-700">#{{ $order->id }}</td>
                            <td class="p-4 text-sm">
                                <div class="font-bold text-gray-800">{{ $order->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $order->created_at->format('H:i') }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-gray-800">{{ $order->customer->nama ?? 'Umum' }}</div>
                                <div class="text-xs text-gray-600">{{ $order->customer->no_hp ?? '-' }}</div>
                                <div class="mt-1">
                                    @if($order->tipe_customer == 'Member')
                                        <span class="bg-pink-100 text-pink-600 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Member</span>
                                    @else
                                        <span class="bg-blue-100 text-blue-600 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Baru</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4 text-sm">
                                <ul class="space-y-1">
                                    {{-- Loop detail item --}}
                                    @foreach($order->details as $detail)
                                        <li class="flex justify-between text-xs border-b border-gray-50 pb-1">
                                            <span>{{ $detail->nama_barang }} <span class="text-gray-400">({{ $detail->layanan }})</span></span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="p-4 font-bold text-right text-gray-800">
                                Rp{{ number_format($order->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-center">
                                <span class="bg-green-100 text-green-700 text-[10px] font-bold px-3 py-1 rounded-full border border-green-200 uppercase">
                                    {{ $order->status_order }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex justify-center space-x-2">
                                    <button class="text-blue-500 hover:text-blue-700">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path></svg>
                                    </button>
                                    @if($order->customer)
                                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', $order->customer->no_hp) }}" target="_blank" class="text-green-500">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"></path></svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-6 text-gray-500">Belum ada pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="lg:hidden space-y-4">
            @forelse($orders as $order)
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <span class="text-xs font-bold text-blue-500 uppercase">#{{ $order->id }}</span>
                            <h2 class="font-bold text-gray-800">{{ $order->customer->nama ?? 'Umum' }}</h2>
                            <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-1 rounded-md border border-green-200">
                            {{ $order->status_order }}
                        </span>
                    </div>
                    
                    <div class="border-t border-dashed border-gray-200 py-3 space-y-2">
                        @foreach($order->details as $detail)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">{{ $detail->nama_barang }}</span>
                                <span class="text-gray-400 text-xs">{{ $detail->layanan }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                        <div class="text-lg font-bold text-gray-800">
                            Rp{{ number_format($order->total_harga, 0, ',', '.') }}
                        </div>
                        <div class="flex space-x-2">
                             @if($order->customer)
                                <a href="https://wa.me/{{ preg_replace('/^0/', '62', $order->customer->no_hp) }}" target="_blank" class="p-2 bg-green-50 rounded-full text-green-600 border border-green-100">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"></path></svg>
                                </a>
                             @endif
                             <button class="p-2 bg-blue-50 rounded-full text-blue-600 border border-blue-100">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path></svg>
                             </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 bg-white rounded-lg border border-dashed border-gray-300 text-gray-500">
                    Tidak ada data pesanan.
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    </div>

    <x-modal name="claim-modal" focusable>
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-medium text-gray-900">
                    🎁 Pilih Reward
                </h2>
                <button x-on:click="$dispatch('close')" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <p class="text-sm text-gray-500 mb-4">
                Pilih item di bawah ini untuk diklaim oleh customer (pastikan poin mencukupi).
            </p>

            <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                @if(isset($treatments) && $treatments->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($treatments as $treatment)
                            <li class="p-4 hover:bg-gray-50 flex justify-between items-center">
                                <div>
                                    <p class="font-bold text-gray-800">{{ $treatment->nama_treatment }}</p>
                                    <p class="text-xs text-gray-500">Harga Normal: Rp {{ number_format($treatment->harga, 0, ',', '.') }}</p>
                                </div>
                                
                                <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-xs font-bold transition shadow-sm">
                                    Pilih
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="p-8 text-center flex flex-col items-center justify-center text-gray-400">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                        <span>Belum ada data reward tersedia.</span>
                    </div>
                @endif
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Tutup') }}
                </x-secondary-button>
            </div>
        </div>
    </x-modal>

</x-app-layout>
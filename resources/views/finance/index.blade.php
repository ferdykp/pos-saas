<x-app-layout>
    <div class="container grid px-6 mx-auto mt-8">
        <h2 class="my-6 text-2xl font-semibold text-gray-700">
            Keuangan & Dompet Toko
        </h2>

        <!-- GRID KARTU INFORMASI SALDO -->
        <div class="grid gap-6 mb-8 md:grid-cols-3">
            <!-- Kartu 1: Saldo Utama (Bisa Ditarik) -->
            <div class="flex items-center p-4 bg-white border border-gray-100 shadow-sm rounded-2xl">
                <div class="p-3 mr-4 text-emerald-500 bg-emerald-50 rounded-xl">
                    <i class="text-xl fa-solid fa-wallet"></i>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">Saldo yang Dapat Ditarik</p>
                    <p class="text-xl font-bold text-gray-800">Rp {{ number_format($wallet->balance ?? 0, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <!-- Kartu 2: Rekening Terdaftar -->
            <div class="flex items-center justify-between p-4 bg-white border border-gray-100 shadow-sm rounded-2xl">
                <div class="flex items-center">
                    <div class="p-3 mr-4 text-blue-500 bg-blue-50 rounded-xl">
                        <i class="text-xl fa-solid fa-bank"></i>
                    </div>
                    <div>
                        <p class="mb-1 text-sm font-medium text-gray-600">Rekening Tujuan</p>
                        @if (isset($wallet->account_number) && $wallet->account_number != '')
                            <p class="text-sm font-bold text-gray-800">{{ $wallet->bank_name }} -
                                {{ $wallet->account_number }}</p>
                            <p class="text-xs text-gray-500">a.n. {{ $wallet->account_name }}</p>
                        @else
                            <p class="text-sm italic font-medium text-amber-600">Belum diatur</p>
                        @endif
                    </div>
                </div>
                <button onclick="openBankSettingsModal()"
                    class="px-2 py-1 ml-2 text-xs font-semibold text-blue-600 underline transition rounded-md hover:text-blue-800 bg-blue-50">
                    <i class="fa-solid fa-pen-to-square"></i> Atur
                </button>
            </div>

            <!-- Kartu 3: Tombol Aksi Tarik Dana -->
            <div class="flex items-center justify-center p-4 bg-white border border-gray-100 shadow-sm rounded-2xl">
                <button onclick="openWithdrawModal()"
                    class="w-full py-3 px-4 font-medium text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 transition duration-150 shadow-md shadow-emerald-100 {{ ($wallet->balance ?? 0) <= 0 || !isset($wallet->account_number) ? 'opacity-50 cursor-not-allowed' : '' }}"
                    {{ ($wallet->balance ?? 0) <= 0 || !isset($wallet->account_number) ? 'disabled' : '' }}>
                    <i class="mr-2 fa-solid fa-money-bill-transfer"></i> Tarik Saldo Ke Bank
                </button>
            </div>
        </div>

        <!-- TABEL RIWAYAT PENARIKAN DANA -->
        <div class="w-full mb-8 overflow-hidden bg-white border border-gray-100 shadow-sm rounded-2xl">
            <div class="flex items-center justify-between p-5 border-b border-gray-50">
                <h3 class="font-bold text-gray-700">Riwayat Pengajuan Penarikan</h3>
            </div>
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                            <th class="px-4 py-3">No. Referensi</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Tujuan Bank</th>
                            <th class="px-4 py-3">Nominal</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 bg-white divide-y divide-gray-50">
                        @forelse($withdrawals as $wdr)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $wdr->reference_number }}</td>
                                <td class="px-4 py-3 text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($wdr->created_at)->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <span class="font-semibold">{{ $wdr->bank_name }}</span><br>
                                    <span class="text-xs text-gray-500">{{ $wdr->account_number }} (a.n
                                        {{ $wdr->account_name }})</span>
                                </td>
                                <td class="px-4 py-3 font-semibold text-gray-900">Rp
                                    {{ number_format($wdr->amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-xs">
                                    @if ($wdr->status === 'pending')
                                        <span
                                            class="px-2 py-1 font-semibold leading-tight rounded-full text-amber-700 bg-amber-50">Menunggu
                                            Transfer</span>
                                    @elseif($wdr->status === 'approved')
                                        <span
                                            class="px-2 py-1 font-semibold leading-tight rounded-full text-emerald-700 bg-emerald-50">Selesai/Ditransfer</span>
                                    @else
                                        <span
                                            class="px-2 py-1 font-semibold leading-tight rounded-full text-rose-700 bg-rose-50">Ditolak</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 italic text-center text-gray-400">Belum ada riwayat
                                    penarikan dana.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 1: FORMULIR TARIK SALDO              -->
    <!-- ========================================== -->
    <div id="withdrawModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="w-full max-w-md p-6 mx-4 bg-white shadow-xl rounded-2xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800"><i
                        class="mr-2 fa-solid fa-paper-plane text-emerald-500"></i> Form Penarikan Dana</h3>
                <button onclick="closeWithdrawModal()"
                    class="text-xl text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            @if (!isset($wallet->account_number) || $wallet->account_number == '')
                <div class="p-4 mb-4 text-sm bg-amber-50 rounded-xl text-amber-800">
                    <i class="mr-1 fa-solid fa-triangle-exclamation"></i> Anda belum mengatur nomor rekening bank.
                    Silakan lengkapi informasi rekening Anda terlebih dahulu.
                </div>
                <button onclick="closeWithdrawModal(); openBankSettingsModal();"
                    class="w-full py-3 font-medium text-white transition bg-blue-600 rounded-xl hover:bg-blue-700">Atur
                    Rekening Sekarang</button>
            @else
                <form id="withdrawForm">
                    @csrf
                    <div class="p-3 mb-4 space-y-1 text-xs text-gray-600 border border-gray-100 bg-gray-50 rounded-xl">
                        <p><span class="font-semibold">Transfer Ke:</span> {{ $wallet->bank_name }}</p>
                        <p><span class="font-semibold">No. Rekening:</span> {{ $wallet->account_number }}</p>
                        <p><span class="font-semibold">Nama Pemilik:</span> {{ $wallet->account_name }}</p>
                        <p class="font-bold text-emerald-600"><span class="font-normal text-gray-600">Maksimal
                                Penarikan:</span> Rp {{ number_format($wallet->balance, 0, ',', '.') }}</p>
                    </div>

                    <div class="mb-5">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Jumlah Penarikan (Rp)</label>
                        <input type="text" id="inputAmount" onkeyup="formatCurrency(this)" required
                            class="w-full px-4 py-3 text-lg font-semibold text-gray-800 border border-gray-200 bg-gray-50 rounded-xl focus:outline-none focus:border-emerald-500"
                            placeholder="Contoh: 50.000">
                    </div>

                    <div class="flex gap-3">
                        <button type="button" onclick="closeWithdrawModal()"
                            class="w-1/2 py-3 font-medium text-gray-600 transition bg-gray-100 hover:bg-gray-200 rounded-xl">Batal</button>
                        <button type="submit" id="btnSubmitWithdraw"
                            class="w-1/2 py-3 font-medium text-white transition shadow-md bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-emerald-100">Kirim
                            Pengajuan</button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 2: FORMULIR PENGATURAN BANK (BARU)   -->
    <!-- ========================================== -->
    <div id="bankSettingsModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="w-full max-w-md p-6 mx-4 bg-white shadow-xl rounded-2xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800"><i class="mr-2 text-blue-500 fa-solid fa-university"></i>
                    Pengaturan Rekening Bank</h3>
                <button onclick="closeBankSettingsModal()"
                    class="text-xl text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form id="bankSettingsForm">
                @csrf
                <div class="mb-4">
                    <label class="block mb-1 text-sm font-medium text-gray-700">Nama Bank</label>
                    <select id="bank_name" required
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 text-gray-800">
                        <option value="" disabled selected>-- Pilih Bank --</option>
                        <option value="BCA" {{ ($wallet->bank_name ?? '') == 'BCA' ? 'selected' : '' }}>Bank Central
                            Asia (BCA)</option>
                        <option value="Mandiri" {{ ($wallet->bank_name ?? '') == 'Mandiri' ? 'selected' : '' }}>Bank
                            Mandiri</option>
                        <option value="BRI" {{ ($wallet->bank_name ?? '') == 'BRI' ? 'selected' : '' }}>Bank Rakyat
                            Indonesia (BRI)</option>
                        <option value="BNI" {{ ($wallet->bank_name ?? '') == 'BNI' ? 'selected' : '' }}>Bank Negara
                            Indonesia (BNI)</option>
                        <option value="BSI" {{ ($wallet->bank_name ?? '') == 'BSI' ? 'selected' : '' }}>Bank Syariah
                            Indonesia (BSI)</option>
                        <option value="CIMB Niaga" {{ ($wallet->bank_name ?? '') == 'CIMB Niaga' ? 'selected' : '' }}>
                            CIMB Niaga</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block mb-1 text-sm font-medium text-gray-700">Nomor Rekening</label>
                    <input type="text" id="account_number" required value="{{ $wallet->account_number ?? '' }}"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 text-gray-800"
                        placeholder="Masukkan nomor rekening tanpa spasi/strip">
                </div>

                <div class="mb-5">
                    <label class="block mb-1 text-sm font-medium text-gray-700">Nama Pemilik Rekening</label>
                    <input type="text" id="account_name" required value="{{ $wallet->account_name ?? '' }}"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 text-gray-800"
                        placeholder="Nama lengkap sesuai buku tabungan">
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeBankSettingsModal()"
                        class="w-1/2 py-3 font-medium text-gray-600 transition bg-gray-100 hover:bg-gray-200 rounded-xl">Batal</button>
                    <button type="submit" id="btnSaveBank"
                        class="w-1/2 py-3 font-medium text-white transition bg-blue-600 shadow-md hover:bg-blue-700 rounded-xl shadow-blue-100">Simpan
                        Rekening</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // FUNGSI KONTROL MODAL UTAMA
        function openWithdrawModal() {
            document.getElementById('withdrawModal').classList.remove('hidden');
        }

        function closeWithdrawModal() {
            document.getElementById('withdrawModal').classList.add('hidden');
            if (document.getElementById('withdrawForm')) document.getElementById('withdrawForm').reset();
        }

        function openBankSettingsModal() {
            document.getElementById('bankSettingsModal').classList.remove('hidden');
        }

        function closeBankSettingsModal() {
            document.getElementById('bankSettingsModal').classList.add('hidden');
        }

        function formatCurrency(input) {
            let value = input.value.replace(/\D/g, "");
            input.value = value !== "" ? new Intl.NumberFormat('id-ID').format(value) : "";
        }

        // AJAX SUBMIT 1: PENGATURAN BANK
        document.getElementById('bankSettingsForm').onsubmit = async function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSaveBank');
            btn.disabled = true;
            btn.innerText = "MENYIMPAN...";

            const data = {
                bank_name: document.getElementById('bank_name').value,
                account_number: document.getElementById('account_number').value,
                account_name: document.getElementById('account_name').value
            };

            try {
                const response = await fetch('/finance/settings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });

                const res = await response.json();
                if (res.success) {
                    alert('Informasi rekening bank berhasil disimpan!');
                    location.reload();
                } else {
                    alert('Gagal: ' + res.message);
                    btn.disabled = false;
                    btn.innerText = "Simpan Rekening";
                }
            } catch (error) {
                alert('Terjadi kesalahan sistem internal.');
                btn.disabled = false;
                btn.innerText = "Simpan Rekening";
            }
        };

        // AJAX SUBMIT 2: PENARIKAN DANA
        if (document.getElementById('withdrawForm')) {
            document.getElementById('withdrawForm').onsubmit = async function(e) {
                e.preventDefault();
                const btn = document.getElementById('btnSubmitWithdraw');
                const rawAmount = document.getElementById('inputAmount').value.replace(/\./g, "") || 0;
                const amount = parseInt(rawAmount);

                if (amount <= 0) return alert('Jumlah penarikan harus lebih dari Rp 0');

                btn.disabled = true;
                btn.innerText = "MEMPROSES...";

                try {
                    const response = await fetch('/finance/withdraw', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            amount: amount
                        })
                    });

                    const res = await response.json();
                    if (res.success) {
                        alert('Pengajuan penarikan dana berhasil dikirim!');
                        location.reload();
                    } else {
                        alert('Gagal: ' + res.message);
                        btn.disabled = false;
                        btn.innerText = "Kirim Pengajuan";
                    }
                } catch (error) {
                    alert('Terjadi kesalahan koneksi sistem.');
                    btn.disabled = false;
                    btn.innerText = "Kirim Pengajuan";
                }
            };
        }
    </script>
</x-app-layout>

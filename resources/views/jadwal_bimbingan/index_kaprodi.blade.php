@extends('layouts.app')


@section('content')
    <div class="p-4 sm:ml-64">
        <div class="px-10 py-6 mt-1 flex flex-col md:flex-row items-center h-auto rounded-md bg-white border border-gray-200 p-5">
            <h1 class="text-2xl font-bold  text-left mb-4 md:mb-0 md:w-auto md:flex-1">Data Jadwal Bimbingan</h1>
            <x-breadcrumb parent="Bimbingan" item="Data Jadwal Bimbingan" />
        </div>
        <div class="px-10 py-8 mt-3 p-5 max-h-[500px] overflow-y-auto rounded-md bg-white border border-gray-200">
            @if($user->role === 'Dosen' && $user->dosen && $user->dosen->jabatan === 'Koordinator Program Studi')
                <form id="searchForm" action="{{ route('jadwal_bimbingan.index_kaprodi.dropdown-search') }}" method="GET" class="mb-4">
                    <div class="flex flex-col md:flex-row gap-4 w-full mb-4">
                        <!-- Dropdown Nama Dosen -->
                        <div class="flex-1 min-w-[200px]">
                            <label for="nama_dosen" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Nama Dosen</label>
                            <select name="nama_dosen" id="nama_dosen"
                                class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                                onchange="document.getElementById('searchForm').submit();">
                                <option value="">Semua Dosen</option>
                                @foreach($dosen as $d)
                                    <option value="{{ $d->nama_dosen }}" {{ request()->get('nama_dosen') == $d->nama_dosen ? 'selected' : '' }}>
                                        {{ $d->nama_dosen }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tanggal (Kalender) -->
                        <div class="flex-1 min-w-[200px]">
                            <label for="tanggal" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal"
                                class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                                value="{{ request()->get('tanggal') }}"
                                onchange="document.getElementById('searchForm').submit();">
                        </div>

                        <!-- Status -->
                        <div class="flex-1 min-w-[200px]">
                            <label for="status" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Status</label>
                            <select name="status" id="status"
                                class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                                onchange="document.getElementById('searchForm').submit();">
                                <option value="">Semua Status</option>
                                @foreach(['Selesai', 'Sedang Berlangsung', 'Terjadwal'] as $status)
                                    <option value="{{ $status }}" {{ request()->get('status') == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            @endif

            @if($jadwalBimbingan->isEmpty())
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                    <strong class="font-bold">Perhatian!</strong>
                    <span class="block sm:inline">Data jadwal bimbingan tidak ada.</span>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="mb-4 table-auto w-full border-collapse border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr class="text-center">
                                <th class="w-1/12 border border-gray-300 px-4 py-2">No.</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Nama Dosen</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Tanggal</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Waktu</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Kuota</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwalBimbingan as $jadwal)
                                <tr class="hover:bg-gray-50 text-center">
                                    <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">{{ $loop->iteration + ($jadwalBimbingan->currentPage() - 1) * $jadwalBimbingan->perPage() }}</td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $jadwal->dosen->nama_dosen }}</td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') }}</td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $jadwal->waktu }}</td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $jadwal->kuota }}</td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $jadwal->status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <nav aria-label="Page navigation example">
                    {{ $jadwalBimbingan->links() }} <!-- Ini akan menghasilkan pagination -->
                </nav>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const modalToggleButtons = document.querySelectorAll('[data-modal-toggle]');

            modalToggleButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const modalId = button.getAttribute('data-modal-target');
                    const modal = document.getElementById(modalId);
                    modal.classList.toggle('hidden'); // Toggle modal visibility
                    modal.classList.toggle('flex'); // Toggle modal visibility
                });
            });
        });
    </script>
@endsection




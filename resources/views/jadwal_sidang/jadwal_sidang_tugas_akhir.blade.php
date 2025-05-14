@extends('layout')
@section('breadcrumb-parent')
    <li>
        @section('breadcrumb-parent')
            <li class="inline-flex items-center">
                <a href="/data-master" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-4 h-4 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 22">
                        <path fill-rule="evenodd" d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm3 11a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6H8v-6Zm1-7V5h6v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                        <path fill-rule="evenodd" d="M14 17h-4v-2h4v2Z" clip-rule="evenodd"/>
                    </svg>
                    Bimbingan
                </a>
            </li>
        @endsection
    </li>
@endsection
@section('breadcrumb-item')
        <li>
        <div class="flex items-center">
            <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
            </svg>
            <span class="text-sm font-medium text-gray-500">Jadwal Bimbingan</span>
        </div>
    </li>
@endsection

@section('content')
    <div class="sm:ml-64">
        <div class="mt-8 flex flex-col md:flex-row items-center h-auto rounded-md bg-gray-50 border border-gray-200 p-5">
            <h1 class="text-2xl font-bold  text-left mb-4 md:mb-0 md:w-auto md:flex-1">Data Jadwal Sidang Tugas Akhir</h1>
            @include('layouts.breadcrumb')
        </div>
        <div class="mt-3 p-5 rounded-md bg-gray-50 border border-gray-200">
            @if (session()->has('success'))
                <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                    {{ session('error') }}
                </div>
            @endif


            <form action="{{ route('jadwal_sidang_tugas_akhir.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf
                <input type="file" name="file" id="fileInput" accept=".csv, .xlsx, .xls" style="display: none;" onchange="document.getElementById('importForm').submit();">
                <button type="button" onclick="document.getElementById('fileInput').click();" class="flex items-center gap-2 focus:outline-none text-white bg-blue-500 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-6 py-3 me-2 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="white" viewBox="0 0 48 48" id="import">
                        <path d="m18 6-8 7.98h6V28h4V13.98h6L18 6zm14 28.02V20h-4v14.02h-6L30 42l8-7.98h-6z"></path>
                        <path fill="none" d="M0 0h48v48H0z"></path>
                    </svg>
                    Import Jadwal Sidang Tugas Akhir
                </button>
            </form>

            @if($jadwals->isEmpty())
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                    <strong class="font-bold">Perhatian!</strong>
                    <span class="block sm:inline">Belum ada Jadwal Sidang Tugas Akhir. SIlakan tunggu informasi ini secara berkala!</span>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="mb-4 table-auto w-full border-collapse border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr class="text-center">
                                <th class="w-1/12 border border-gray-300 px-4 py-2">No.</th>
                                <th class="w-2/12 border border-gray-300 px-4 py-2">Mahasiswa</th>
                                <th class="w-2/12 border border-gray-300 px-4 py-2">Pembimbing Utama</th>
                                <th class="w-2/12 border border-gray-300 px-4 py-2">Pembimbing Pendamping</th>
                                <th class="w-3/12 border border-gray-300 px-4 py-2">Penguji Utama</th>
                                <th class="w-2/12 border border-gray-300 px-4 py-2">Penguji Pendamping</th>
                                <th class="w-2/12 border border-gray-300 px-4 py-2">Tanggal</th>
                                <th class="w-2/12 border border-gray-300 px-4 py-2">Waktu</th>
                                <th class="w-3/12 border border-gray-300 px-4 py-2">Ruangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwals as $jadwal)
                                    <tr class="hover:bg-gray-50 text-center">
                                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $loop->iteration + ($jadwals->currentPage() - 1) * $jadwals->perPage() }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $jadwal->mahasiswa->nama_mahasiswa }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $jadwal->pembimbingUtama->nama_dosen }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $jadwal->pembimbingPendamping->nama_dosen }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $jadwal->pengujiUtama->nama_dosen }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $jadwal->pengujiPendamping->nama_dosen }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ \Carbon\Carbon::parse($jadwal->tanggal)->format('d M Y') }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $jadwal->ruanganSidang->nama_ruangan }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                </div>

                <nav aria-label="Page navigation example">
                    {{ $jadwals->links() }} <!-- Ini akan menghasilkan pagination -->
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




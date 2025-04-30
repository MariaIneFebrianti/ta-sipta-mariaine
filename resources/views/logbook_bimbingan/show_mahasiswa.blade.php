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
                <span class="text-sm font-medium text-gray-500">Logbook Bimbingan {{ $dosen->nama_dosen ?? 'Dosen tidak ditemukan' }} </span>
            </div>
        </li>
    @endsection

@section('content')
<div class="sm:ml-64">
    <div class="mt-8 flex flex-col md:flex-row items-center h-auto rounded-md bg-gray-50 border border-gray-200 p-5">
        <h1 class="text-2xl font-bold  text-left mb-4 md:mb-0 md:w-auto md:flex-1">Logbook Bimbingan</h1>
        @include('layouts.breadcrumb')
    </div>

    <div class="mt-3 p-6 rounded-lg bg-gray-50 shadow-md border border-gray-200">
        <table class="w-full md:w-1/2"> <!-- Lebar penuh pada layar kecil, 1/2 pada layar normal -->
            <tbody>
                <tr>
                    <td class="py-1"><strong>Nama Dosen</strong></td>
                    <td class="py-1"> : {{ $dosen->nama_dosen ?? 'Dosen tidak ditemukan' }}</td>
                </tr>
                <tr>
                    <td class="py-1"><strong>Nama Mahasiswa</strong></td>
                    <td class="py-1"> : {{ $pengajuan->mahasiswa->nama_mahasiswa }}</td>
                </tr>
                <tr>
                    <td class="py-1"><strong>NIM</strong></td>
                    <td class="py-1"> : {{ $pengajuan->mahasiswa->nim }}</td>
                </tr>
            </tbody>
        </table>

        <div class="flex justify-between mt-4">
            <a href="{{ route('logbook_bimbingan.index_mahasiswa') }}" class="mr-2">
                <button class="text-white bg-gray-600 hover:bg-gray-800 font-medium rounded-lg text-sm px-5 py-2.5">
                    Kembali
                </button>
            </a>

            @if ($availablePendaftaranBimbingan->isNotEmpty())
                <button id="openModal" class="text-white bg-blue-600 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                    Silakan isi logbook bimbingan
                </button>
            @endif
        </div>

        @if ($availablePendaftaranBimbingan->isEmpty())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative text-center mt-4" role="alert">
                <strong class="font-bold">Perhatian!</strong>
                <span class="block sm:inline">Belum ada jadwal yang Anda daftarkan! Anda tidak bisa mengisikan logbook bimbingan.</span>
            </div>
        @endif

        @if($logbooks->isEmpty())
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center mt-4" role="alert">
                <strong class="font-bold">Perhatian!</strong>
                @if($userRole === 'Mahasiswa')
                    <span class="block sm:inline">Anda tidak mempunyai data logbook bimbingan.</span>
                @elseif($userRole === 'Dosen')
                    <span class="block sm:inline">Tidak ada logbook yang diisi oleh mahasiswa bimbingan Anda.</span>
                @elseif($userRole === 'Koordinator Program Studi')
                    <span class="block sm:inline">Belum ada data logbook bimbingan yang diisi.</span>
                @endif
            </div>
        @else
            <!-- Tabel untuk menampilkan logbook -->
            <div class="mt-6">
                <h2 class="text-lg font-semibold">Daftar Logbook</h2>
                <table class="min-w-full mt-2 border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-4 py-2">Tanggal</th>
                            <th class="border px-4 py-2">Permasalahan</th>
                            <th class="border px-4 py-2">File</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logbooks as $logbook)
                            <tr>
                                <td class="border px-4 py-2">
                                    {{ \Carbon\Carbon::parse($logbook->pendaftaranBimbingan->jadwalBimbingan->tanggal)->format('d F Y') }}
                                <td class="border px-4 py-2">{{ $logbook->permasalahan }}</td>
                                <td class="border px-4 py-2">
                                    @if($logbook->file_bimbingan)
                                    <a href="#"
                                        onclick="openModal('{{ asset('storage/' . $logbook->file_bimbingan) }}', '{{ pathinfo($logbook->file_bimbingan, PATHINFO_EXTENSION) }}')"
                                        class="text-blue-600 hover:underline">
                                        Lihat File
                                    </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <!-- Modal -->
                    <div id="fileModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 items-center justify-center">
                        <div class="bg-white rounded-lg shadow-lg w-11/12 md:w-3/4 lg:w-1/2 h-[90%] flex flex-col">
                            <div class="flex justify-between items-center p-4 border-b">
                                <h3 class="text-lg font-semibold">Preview File</h3>
                                <button onclick="closeModal()" class="text-red-500 hover:text-red-700">&times;</button>
                            </div>
                            <div class="flex-grow overflow-hidden">
                                <iframe id="fileFrame" class="w-full h-full" frameborder="0"></iframe>
                            </div>
                        </div>
                    </div>
                </table>
            </div>
        @endif


        @if($userRole === 'Koordinator Program Studi')
                <!-- Tabel untuk menampilkan logbook -->
                <div class="mt-6">
                    <h2 class="text-lg font-semibold">Daftar Logbook</h2>
                    <table class="min-w-full mt-2 border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">Tanggal</th>
                                <th class="border px-4 py-2">Permasalahan</th>
                                <th class="border px-4 py-2">File</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logbooks as $logbook)
                                <tr>
                                    <td class="border px-4 py-2">
                                        {{ \Carbon\Carbon::parse($logbook->pendaftaranBimbingan->jadwalBimbingan->tanggal)->format('d F Y') }}
                                    <td class="border px-4 py-2">{{ $logbook->permasalahan }}</td>
                                    <td class="border px-4 py-2">
                                        @if($logbook->file_bimbingan)
                                        <a href="#"
                                            onclick="openModal('{{ asset('storage/' . $logbook->file_bimbingan) }}', '{{ pathinfo($logbook->file_bimbingan, PATHINFO_EXTENSION) }}')"
                                            class="text-blue-600 hover:underline">
                                            Lihat File
                                        </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <!-- Modal -->
                        <div id="fileModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 items-center justify-center">
                            <div class="bg-white rounded-lg shadow-lg w-11/12 md:w-3/4 lg:w-1/2 h-[90%] flex flex-col">
                                <div class="flex justify-between items-center p-4 border-b">
                                    <h3 class="text-lg font-semibold">Preview File</h3>
                                    <button onclick="closeModal()" class="text-red-500 hover:text-red-700">&times;</button>
                                </div>
                                <div class="flex-grow overflow-hidden">
                                    <iframe id="fileFrame" class="w-full h-full" frameborder="0"></iframe>
                                </div>
                            </div>
                        </div>
                    </table>
                </div>
        @endif

    </div>
</div>

@if ($availablePendaftaranBimbingan->isNotEmpty())
<!-- Modal -->
<div id="modal" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
    <div class="bg-white rounded-lg p-6 w-96 z-50">
        <h2 class="text-lg font-bold mb-4">Isi Logbook Bimbingan</h2>
        <form action="{{ route('logbook_bimbingan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            {{-- <input type="hidden" name="mahasiswa_id" value="{{ $mahasiswa->id }}"> --}}
            <div class="mb-4">
                <label for="pendaftaran_bimbingan_id" class="block text-sm font-medium text-gray-700">Jadwal Bimbingan</label>
                <select id="pendaftaran_bimbingan_id" name="pendaftaran_bimbingan_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                    @foreach ($availablePendaftaranBimbingan as $pendaftaran)
                        <option value="{{ $pendaftaran->id }}">
                            {{ $pendaftaran->jadwalBimbingan->dosen->nama_dosen }} - [{{ $pendaftaran->jadwalBimbingan->tanggal }} - {{ $pendaftaran->jadwalBimbingan->waktu }}]
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="catatan" class="block text-sm font-medium text-gray-700">Permasalahan</label>
                <textarea id="catatan" name="permasalahan" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" rows="3"></textarea>
            </div>
            <div class="mb-4">
                <label for="file_bimbingan" class="block text-sm font-medium text-gray-700">Upload File</label>
                <input type="file" id="file_bimbingan" name="file_bimbingan">
            </div>
            <div class="flex justify-end">
                <button type="button" id="closeModal" class="text-white bg-gray-600 hover:bg-gray-800 font-medium rounded-lg text-sm px-5 py-2.5">
                    Batal
                </button>
                <button type="submit" class="text-white bg-blue-600 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 ml-2">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    // Script untuk membuka dan menutup modal
    document.getElementById('openModal').onclick = function() {
        document.getElementById('modal').classList.remove('hidden');
    };
    document.getElementById('closeModal').onclick = function() {
        document.getElementById('modal').classList.add('hidden');
    };
</script>

<script>
    function openModal(fileUrl, fileType) {
        const modal = document.getElementById('fileModal');
        const iframe = document.getElementById('fileFrame');

        // Cek jenis file untuk menampilkan dengan cara yang sesuai
        if (fileType === 'pdf') {
            iframe.src = fileUrl; // Tampilkan PDF langsung
        } else if (fileType === 'doc' || fileType === 'docx') {
            iframe.src = `https://docs.google.com/gview?url=${encodeURIComponent(fileUrl)}&embedded=true`;
        } else if (fileType === 'png' || fileType === 'jpg' || fileType === 'jpeg') {
            iframe.src = fileUrl; // Tampilkan gambar langsung
        } else {
            alert('Format file tidak didukung untuk preview.');
            return;
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        const modal = document.getElementById('fileModal');
        const iframe = document.getElementById('fileFrame');
        iframe.src = ''; // Reset isi
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
</script>

@endsection






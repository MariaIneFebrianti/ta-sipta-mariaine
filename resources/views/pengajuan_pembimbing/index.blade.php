@extends('layout')
    @section('breadcrumb-parent')
    <li class="inline-flex items-center">
         <a href="/data-master" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
            <svg class="w-4 h-4 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 22">
                <path fill-rule="evenodd" d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm3 11a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6H8v-6Zm1-7V5h6v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                <path fill-rule="evenodd" d="M14 17h-4v-2h4v2Z" clip-rule="evenodd"/>                 </svg>
            Pengajuan Pembimbing
        </a>
    </li>
@endsection

@section('content')
<div class="sm:ml-64">
    <div class="mt-8 flex flex-col md:flex-row items-center h-auto rounded-md bg-gray-50 border border-gray-200 p-5">
        <h1 class="text-2xl font-bold  text-left mb-4 md:mb-0 md:w-auto md:flex-1">Data Pengajuan Pembimbing</h1>
        @include('layouts.breadcrumb')
    </div>
    <div class="mt-3 p-5 max-h-[500px] overflow-y-auto rounded-md bg-gray-50 border border-gray-200">
        @if($userRole === 'Mahasiswa')
            <div class="flex justify-between mb-4 flex-wrap">
                @if($pengajuanPembimbing->isEmpty())
                    <!-- Tombol Tambah jika data belum ada -->
                    <button
                        data-modal-target="crud-modal"
                        data-modal-toggle="crud-modal"
                        class="focus:outline-none text-white font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-4 bg-green-600 hover:bg-green-800 focus:ring-4 focus:ring-green-300"
                    >
                        <svg class="w-7 h-7 inline-block" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                        </svg>
                        Tambah Pengajuan Pembimbing
                    </button>
                @else
                    <!-- Teks Informasi jika data sudah ada -->
                    <span class="text-gray-700 font-semibold text-xl me-2 mb-4">
                        Detail Informasi
                    </span>
                @endif

                <div class="flex justify-end mb-4 flex-grow">
                    <!-- Form pencarian di sini -->
                </div>
            </div>


            <!-- Main modal -->
            <div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
                <div class="relative p-4 w-full max-w-md max-h-full">
                    <!-- Modal content -->
                    <div class="relative bg-white rounded-lg shadow-sm">
                        <!-- Modal header -->
                        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Form Tambah Pengajuan Pembimbing
                            </h3>
                            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="crud-modal">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <form action="{{ route('pengajuan_pembimbing.store') }}" method="POST" class="p-4 md:p-5" onsubmit="return validatePembimbing();">
                            @csrf
                            <div class="grid gap-4 mb-4 grid-cols-2">
                                <div class="col-span-2">
                                    <label for='mahasiswa_id' class="block mb-2 text-sm font-medium text-gray-900">Mahasiswa</label>
                                    <input type="text" name="mahasiswa_id" id="mahasiswa_id" value="{{ auth()->user()->mahasiswa->nama_mahasiswa }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" readonly />
                                </div>
                                <div class="col-span-2">
                                    <label for="pembimbing_utama_id" class="block mb-2 text-sm font-medium text-gray-900">Pilih Dosen Pembimbing Utama</label>
                                    <select id="pembimbing_utama_id" name="pembimbing_utama_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                        <option selected disabled>Pilih Dosen Pembimbing Utama</option>
                                        @foreach ($dosen as $pembimbingUtama)
                                            <option value="{{ $pembimbingUtama->id }}">{{ $pembimbingUtama->nama_dosen }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label for="pembimbing_pendamping_id" class="block mb-2 text-sm font-medium text-gray-900">Pilih Dosen Pembimbing Pendamping</label>
                                    <select id="pembimbing_pendamping_id" name="pembimbing_pendamping_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                        <option selected disabled>Pilih Dosen Pembimbing Pendamping</option>
                                        @foreach ($dosen as $pembimbingPendamping)
                                            <option value="{{ $pembimbingPendamping->id }}">{{ $pembimbingPendamping->nama_dosen }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="flex justify-end space-x-2">
                                <button type="button" class="text-white inline-flex items-center bg-gray-600 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" data-modal-toggle="crud-modal">
                                    <svg class="me-2 w-2 h-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                    </svg>
                                    Batal
                                </button>
                                <button type="submit" class="text-white inline-flex items-center bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                    <svg class="me-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm3 11a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6H8v-6Zm1-7V5h6v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                                        <path fill-rule="evenodd" d="M14 17h-4v-2h4v2Z" clip-rule="evenodd"/>
                                    </svg>
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
            @if($pengajuanPembimbing->isEmpty())
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                    <strong class="font-bold">Perhatian!</strong>
                    @if($userRole === 'Mahasiswa')
                        <span class="block sm:inline">Anda belum menambahkan pengajuan dosen pembimbing Tugas Akhir Anda.</span>
                    @elseif($userRole === 'Dosen')
                        <span class="block sm:inline">Tidak ada data mahasiswa bimbingan Anda.</span>
                    @elseif($userRole === 'Koordinator Program Studi')
                        <span class="block sm:inline">Data pengajuan pembimbing tidak ada.</span>
                    @endif
                </div>
            @else

            {{-- Tampilan Mahasiswa --}}
            @if($userRole === 'Mahasiswa')
            {{-- <table class="mb-4 table-auto w-full border-collapse border border-gray-200">
                <thead class="bg-gray-100">
                    <tr class="text-center">
                        <th class="w-3/12 border border-gray-300 px-4 py-2">Tanggal Pengajuan</th>
                        <th class="w-3/12 border border-gray-300 px-4 py-2">Nama Mahasiswa</th>
                        <th class="w-3/12 border border-gray-300 px-4 py-2">Dosen Pembimbing Utama</th>
                        <th class="w-3/12 border border-gray-300 px-4 py-2">Dosen Pembimbing Pendamping</th>
                        @if($userRole === 'Koordinator Program Studi')
                            <th class="w-1/12 border border-gray-300 px-4 py-2">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengajuanPembimbing as $pembimbing)
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-300 px-4 py-2">{{ $pembimbing->created_at }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $pembimbing->mahasiswa->nama_mahasiswa }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $pembimbing->pembimbingUtama ? $pembimbing->pembimbingUtama->nama_dosen : 'Tidak ada' }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $pembimbing->pembimbingPendamping ? $pembimbing->pembimbingPendamping->nama_dosen : 'Tidak ada' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table> --}}
            {{-- <table class="w-full border-collapse border border-gray-300">
                <tbody>
                    @foreach($pengajuanPembimbing as $pembimbing)
                        <tr class="border-b">
                            <td class="p-2 font-semibold bg-gray-100 w-1/3 border border-gray-300">Tanggal Pengajuan</td>
                            <td class="p-2 border border-gray-300">{{ $pembimbing->created_at->format('d-m-Y') }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="p-2 font-semibold bg-gray-100 w-1/3 border border-gray-300">Nama Mahasiswa</td>
                            <td class="p-2 border border-gray-300">{{ $pembimbing->mahasiswa->nama_mahasiswa }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="p-2 font-semibold bg-gray-100 w-1/3 border border-gray-300">Dosen Pembimbing Utama</td>
                            <td class="p-2 border border-gray-300">
                                {{ $pembimbing->pembimbingUtama ? $pembimbing->pembimbingUtama->nama_dosen : 'Tidak ada' }}
                            </td>
                        </tr>
                        <tr class="border-b">
                            <td class="p-2 font-semibold bg-gray-100 w-1/3 border border-gray-300">Dosen Pembimbing Pendamping</td>
                            <td class="p-2 border border-gray-300">
                                {{ $pembimbing->pembimbingPendamping ? $pembimbing->pembimbingPendamping->nama_dosen : 'Tidak ada' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table> --}}
            <table class="w-full border border-gray-300 rounded-lg overflow-hidden shadow-md">
                {{-- <tbody>
                    @foreach($pengajuanPembimbing as $pembimbing)
                        <tr class="bg-gray-50 border-b hover:bg-gray-100">
                            <td class="p-3 font-semibold text-gray-700 w-1/3 border-r border-gray-300">Tanggal Pengajuan</td>
                            <td class="p-3 text-gray-800">{{ $pembimbing->created_at->format('d-m-Y') }}</td>
                        </tr>
                        <tr class="border-b hover:bg-gray-100">
                            <td class="p-3 font-semibold text-gray-700 w-1/3 border-r border-gray-300">Nama Mahasiswa</td>
                            <td class="p-3 text-gray-800">{{ $pembimbing->mahasiswa->nama_mahasiswa }}</td>
                        </tr>
                        <tr class="bg-gray-50 border-b hover:bg-gray-100">
                            <td class="p-3 font-semibold text-gray-700 w-1/3 border-r border-gray-300">Dosen Pembimbing Utama</td>
                            <td class="p-3 text-gray-800">
                                {{ $pembimbing->pembimbingUtama ? $pembimbing->pembimbingUtama->nama_dosen : 'Tidak ada' }}
                            </td>
                        </tr>
                        <tr class="border-b hover:bg-gray-100">
                            <td class="p-3 font-semibold text-gray-700 w-1/3 border-r border-gray-300">Dosen Pembimbing Pendamping</td>
                            <td class="p-3 text-gray-800">
                                {{ $pembimbing->pembimbingPendamping ? $pembimbing->pembimbingPendamping->nama_dosen : 'Tidak ada' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody> --}}
                @foreach($pengajuanPembimbing as $pembimbing)
                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Tanggal Pengajuan</label>
                        <input type="text" value="{{ $pembimbing->created_at->format('d-m-Y') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-600 cursor-not-allowed" disabled>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nama Mahasiswa</label>
                        <input type="text" value="{{ $pembimbing->mahasiswa->nama_mahasiswa }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-600 cursor-not-allowed" disabled>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Dosen Pembimbing Utama</label>
                        <input type="text" value="{{ $pembimbing->pembimbingUtama ? $pembimbing->pembimbingUtama->nama_dosen : 'Tidak ada' }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-600 cursor-not-allowed" disabled>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Dosen Pembimbing Pendamping</label>
                        <input type="text" value="{{ $pembimbing->pembimbingPendamping ? $pembimbing->pembimbingPendamping->nama_dosen : 'Tidak ada' }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-600 cursor-not-allowed" disabled>
                    </div>
                </div>
                @endforeach
            </table>



            {{-- Tampilan Dosen dan Koordinator Program Studi --}}
            @elseif ($userRole === 'Dosen' || $userRole === 'Koordinator Program Studi')
            <table class="mb-4 table-auto w-full border-collapse border border-gray-200">
                <thead class="bg-gray-100">
                    <tr class="text-center">
                        <th class="w-0.5/12 border border-gray-300 px-4 py-2">No.</th>
                        <th class="w-4/12 border border-gray-300 px-4 py-2">Nama Mahasiswa</th>
                        <th class="w-4/12 border border-gray-300 px-4 py-2">Dosen Pembimbing Utama</th>
                        <th class="w-4/12 border border-gray-300 px-4 py-2">Dosen Pembimbing Pendamping</th>
                        <th class="w-4/12 border border-gray-300 px-4 py-2">Logbook</th>
                        @if($userRole === 'Koordinator Program Studi')
                            <th class="w-1/12 border border-gray-300 px-4 py-2">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengajuanPembimbing as $pembimbing)
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $loop->iteration }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $pembimbing->mahasiswa->nama_mahasiswa }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $pembimbing->pembimbingUtama ? $pembimbing->pembimbingUtama->nama_dosen : 'Tidak ada' }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $pembimbing->pembimbingPendamping ? $pembimbing->pembimbingPendamping->nama_dosen : 'Tidak ada' }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                <a href="{{ route('logbook_bimbingan.show', ['dosenId' => Auth::user()->dosen->id, 'mahasiswaId' => $pembimbing->mahasiswa->id]) }}">
                                    <button class="bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg px-4 py-2 transition duration-200">
                                        Lihat Logbook
                                    </button>
                                </a>
                            </td>
                            @if($userRole === 'Koordinator Program Studi')
                                <td class="border border-gray-300 px-4 py-2">
                                    <div class="flex justify-center space-x-2">
                                        <button data-modal-target="editModal-{{ $pembimbing->id }}" data-modal-toggle="editModal-{{ $pembimbing->id }}" class="flex items-center justify-center w-full sm:w-20 md:w-20 px-3 py-1 bg-yellow-400 text-white rounded-lg hover:bg-yellow-600 transition duration-200 mb-2 sm:mb-0">Edit</button>
                                        <!-- Modal Edit -->
                                        <div id="editModal-{{ $pembimbing->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
                                            <div class="relative p-4 w-full max-w-md max-h-full">
                                                <!-- Modal content -->
                                                <div class="relative bg-white rounded-lg shadow-sm">
                                                    <!-- Modal header -->
                                                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                                                        <h3 class="text-lg font-semibold text-gray-900">
                                                            Form Edit Pengajuan Pembimbing
                                                        </h3>
                                                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="editModal-{{ $pembimbing->id }}">
                                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                            </svg>
                                                            <span class="sr-only">Close modal</span>
                                                        </button>
                                                    </div>
                                                    <!-- Modal body -->
                                                    <form action="{{ route('pengajuan_pembimbing.update', $pembimbing->id) }}" method="POST" class="p-4 md:p-5">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="grid gap-4 mb-4 grid-cols-2">
                                                            <div class="col-span-2">
                                                                <label for="pembimbing_utama_id_edit" class="block mb-2 text-sm font-medium text-gray-900">Dosen Pembimbing Utama</label>
                                                                <select id="pembimbing_utama_id_edit" name="pembimbing_utama_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                                                    <option selected disabled>Pilih Dosen Pembimbing Utama</option>
                                                                    @foreach ($dosen as $pembimbingUtama)
                                                                        <option value="{{ $pembimbingUtama->id }}" {{ $pembimbingUtama->id == $pembimbing->pembimbing_utama_id ? 'selected' : '' }}>{{ $pembimbingUtama->nama_dosen }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="grid gap-4 mb-4 grid-cols-2">
                                                            <div class="col-span-2">
                                                                <label for="pembimbing_pendamping_id_edit" class="block mb-2 text-sm font-medium text-gray-900">Dosen Pembimbing Pendamping</label>
                                                                <select id="pembimbing_pendamping_id_edit" name="pembimbing_pendamping_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                                                    <option selected disabled>Pilih Dosen Pembimbing Utama</option>
                                                                    @foreach ($dosen as $pembimbingPendamping)
                                                                        <option value="{{ $pembimbingPendamping->id }}" {{ $pembimbingPendamping->id == $pembimbing->pembimbing_pendamping_id ? 'selected' : '' }}>{{ $pembimbingPendamping->nama_dosen }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="flex justify-end space-x-2">
                                                            <button type="button" class="text-white inline-flex items-center bg-gray-600 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" data-modal-toggle="editModal-{{ $pembimbing->id }}">
                                                                <svg class="me-2 w-2 h-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                                </svg>
                                                                Batal
                                                            </button>
                                                            <button type="submit" class="text-white inline-flex items-center bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                                                <svg class="me-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm3 11a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6H8v-6Zm1-7V5h6v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                                                                    <path fill-rule="evenodd" d="M14 17h-4v-2h4v2Z" clip-rule="evenodd"/></svg>
                                                                Simpan
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <form id="delete-form-{{ $pembimbing->id }}" action="{{ route('pengajuan_pembimbing.destroy', $pembimbing->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button data-modal-target="popup-modal" data-modal-toggle="popup-modal" class="flex w-full sm:w-20 md:w-20 px-3 py-1 text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" type="button" onclick="openDeleteModal('{{ $pembimbing->id }}')">
                                                Hapus
                                            </button>
                                            <div id="popup-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
                                                <div class="relative p-4 w-full max-w-md max-h-full">
                                                    <div class="relative bg-white rounded-lg shadow-sm">
                                                        <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="popup-modal">
                                                            <svg class="w-3 h-3" aria-hidden="true" fill="none" viewBox="0 0 14 14">
                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                            </svg>
                                                            <span class="sr-only">Close modal</span>
                                                        </button>
                                                        <div class="p-4 md:p-5 text-center">
                                                            <svg class="mx-auto mb-4 text-gray-400 w-12 h-12" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                                            </svg>
                                                            <h3 class="mb-5 text-lg font-normal text-gray-500">Apakah anda yakin ingin menghapus Data Pengajuan Pembimbing ini?</h3>
                                                            <button id="confirm-delete" type="button" class="w-full sm:w-20 md:w-20 text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 justify-center" onclick="event.preventDefault(); this.closest('form').submit();">
                                                                Ya
                                                            </button>
                                                            <button data-modal-hide="popup-modal" type="button" class="w-full sm:w-20 md:w-20 py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-grey-200 rounded-lg border border-gray-200 hover:bg-gray-500 hover:text-white focus:z-10 focus:ring-4 focus:ring-gray-100 justify-center">Tidak</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            <nav aria-label="Page navigation example">
                {{ $pengajuanPembimbing->links() }} <!-- Ini akan menghasilkan pagination -->
            </nav>
        @endif
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi untuk memperbarui opsi dropdown
        function updateDropdownOptions(dropdownToUpdate, dropdownToExclude) {
            const selectedValue = dropdownToExclude.value;
            const options = dropdownToUpdate.options;

            for (const option of options) {
                if (option.value === selectedValue) {
                    option.hidden = true;
                } else {
                    option.hidden = false;
                }
            }
        }

        // Fungsi untuk mengatur event listener pada dropdown
        function setupDropdownListeners(dropdown1, dropdown2) {
            if (dropdown1 && dropdown2) {
                dropdown1.addEventListener('change', function() {
                    updateDropdownOptions(dropdown2, dropdown1);
                });
                dropdown2.addEventListener('change', function() {
                    updateDropdownOptions(dropdown1, dropdown2);
                });
            }
        }

        // Setup dropdown di modal tambah
        setupDropdownListeners(document.getElementById('pembimbing_utama_id'), document.getElementById('pembimbing_pendamping_id'));

        // Event listener untuk modal edit saat dibuka
        document.querySelectorAll('[data-modal-toggle^="editModal-"]').forEach(button => {
            button.addEventListener('click', function() {
                const modalId = this.getAttribute('data-modal-toggle');
                setTimeout(function() {
                    const modal = document.getElementById(modalId);
                    const pembimbingUtamaEdit = modal.querySelector('#pembimbing_utama_id_edit');
                    const pembimbingPendampingEdit = modal.querySelector('#pembimbing_pendamping_id_edit');

                    if (pembimbingUtamaEdit && pembimbingPendampingEdit) {
                        // Reset ke data asli sebelum diperbarui oleh event 'change'
                        pembimbingUtamaEdit.value = pembimbingUtamaEdit.getAttribute('data-original-value');
                        pembimbingPendampingEdit.value = pembimbingPendampingEdit.getAttribute('data-original-value');

                        updateDropdownOptions(pembimbingUtamaEdit, pembimbingPendampingEdit);
                        updateDropdownOptions(pembimbingPendampingEdit, pembimbingUtamaEdit);
                    }
                }, 100);
            });
        });

        // Setup dropdown di modal edit
        document.querySelectorAll('[id^="editModal-"]').forEach(modal => {
            const pembimbingUtamaEdit = modal.querySelector('#pembimbing_utama_id_edit');
            const pembimbingPendampingEdit = modal.querySelector('#pembimbing_pendamping_id_edit');

            if (pembimbingUtamaEdit && pembimbingPendampingEdit) {
                pembimbingUtamaEdit.setAttribute('data-original-value', pembimbingUtamaEdit.value);
                pembimbingPendampingEdit.setAttribute('data-original-value', pembimbingPendampingEdit.value);
            }

            setupDropdownListeners(pembimbingUtamaEdit, pembimbingPendampingEdit);
        });

        // Reset dropdown saat modal ditutup
        document.querySelectorAll('[data-modal-toggle^="editModal-"]').forEach(button => {
            button.addEventListener('click', function() {
                const modalId = this.getAttribute('data-modal-toggle');
                const modal = document.getElementById(modalId);

                modal.addEventListener('click', function(event) {
                    if (event.target === modal || event.target.closest('[data-modal-toggle^="editModal-"]')) {
                        const pembimbingUtamaEdit = modal.querySelector('#pembimbing_utama_id_edit');
                        const pembimbingPendampingEdit = modal.querySelector('#pembimbing_pendamping_id_edit');

                        if (pembimbingUtamaEdit && pembimbingPendampingEdit) {
                            pembimbingUtamaEdit.value = pembimbingUtamaEdit.getAttribute('data-original-value');
                            pembimbingPendampingEdit.value = pembimbingPendampingEdit.getAttribute('data-original-value');
                        }
                    }
                });
            });
        });
    });
</script>

@endsection




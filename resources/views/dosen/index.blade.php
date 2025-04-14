@extends('layout')
@section('breadcrumb-parent')
    <li>
        @section('breadcrumb-parent')
        <li class="inline-flex items-center">
            <a href="/data-master" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                <svg class="w-4 h-4 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 22">
                    <path fill-rule="evenodd" d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm3 11a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6H8v-6Zm1-7V5h6v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                    <path fill-rule="evenodd" d="M14 17h-4v-2h4v2Z" clip-rule="evenodd"/>                 </svg>
                Data Master
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
            <span class="text-sm font-medium text-gray-500">Dosen</span>
        </div>
    </li>
@endsection

@if ($errors->any())
    <div class="bg-red-100 border border-red-500 text-red-700 px-4 py-3 rounded relative" role="alert">
        <strong class="font-bold">Ada kesalahan!</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@section('content')
<div class="sm:ml-64">
    <div class="mt-8 flex flex-col md:flex-row items-center h-auto rounded-md bg-gray-50 border border-gray-200 p-5">
        <h1 class="text-2xl font-bold  text-left mb-4 md:mb-0 md:w-auto md:flex-1">Data Dosen</h1>
        @include('layouts.breadcrumb')
    </div>
    <div class="mt-3 p-5 rounded-md bg-gray-50 border border-gray-200">
        <!-- Modal toggle -->
        <div class="flex justify-between mb-4 flex-wrap">
            <button data-modal-target="crud-modal" data-modal-toggle="crud-modal" class="focus:outline-none text-white bg-green-600 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-4">
                <svg class="w-7 h-7 inline-block" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                </svg>
                Tambah Dosen
            </button>
            <div class="flex justify-end mb-4 flex-grow">
                <form action="{{ route('dosen.search') }}" method="GET" class="max-w-md  w-full" id="search-form">
                    <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                        <input type="search" id="search-input" name="search"
                            class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Cari data dosen disini"
                            required
                            value="{{ request('search') }}" style="min-width: 300px"
                            oninput="document.querySelector('#search-form').submit();" />
                        <button type="submit" class="text-white absolute end-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Search</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Main modal -->
        <div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
            <div class="relative p-4 w-full max-w-3xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Form Tambah Dosen
                        </h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="crud-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <form action="{{ route('dosen.store') }}" method="POST" class="p-4 md:p-5">
                        @csrf
                        <div class="grid gap-4 mb-4 grid-cols-2">
                            <div class="grid-cols-2">
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Nama Dosen</label>
                                <input type="text" name="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Maria Ine" required />
                            </div>
                            <div class="grid-cols-2">
                                <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                                <input type="text" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="mariaine@gmail.com" required />
                            </div>
                            <div class="grid-cols-2">
                                <label for="nip" class="block mb-2 text-sm font-medium text-gray-900">NIP</label>
                                <input type="number" name="nip" id="nip" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="22010" required />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <label for="jenis_kelamin" class="block text-sm font-medium text-gray-900">Jenis Kelamin</label>
                                <div class="flex col-span-2">
                                    <div class="flex items-center mb-6">
                                        <input id="Laki-laki" type="radio" name="jenis_kelamin" value="Laki-laki" class="w-4 h-4 border-gray-300 focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-600 dark:focus:bg-blue-600 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="Laki-laki" class="block ml-2 mr-6 text-sm font-medium text-gray-900 dark:text-gray-300">Laki-laki</label>
                                    </div>
                                    <div class="flex items-center mb-6">
                                        <input id="Perempuan" type="radio" name="jenis_kelamin" value="Perempuan" class="w-4 h-4 border-gray-300 focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-600 dark:focus:bg-blue-600 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="Perempuan" class="block ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Perempuan</label>
                                    </div>
                                </div>
                            </div>

                            <div class="grid-cols-2">
                                <label for="tempat_lahir" class="block mb-2 text-sm font-medium text-gray-900">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" id="tempat_lahir" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Kebumen" required />
                            </div>
                            <div class="grid-cols-2">
                                <label for="tanggal_lahir" class="block mb-2 text-sm font-medium text-gray-900">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="GKB 1.1" required />
                            </div>

                            <div class="grid-cols-2">
                                <label for="prodi_id" class="block mb-2 text-sm font-medium text-gray-900">Pilih Program Studi</label>
                                <select id="prodi_id" name="prodi_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option selected disabled>Pilih Program Studi</option>
                                    @foreach ($programStudi as $prodi)
                                        <option value="{{ $prodi->id }}">{{ $prodi->nama_prodi }}</option>
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

        @if($dosen->isEmpty())
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                <strong class="font-bold">Perhatian!</strong>
                <span class="block sm:inline">Tidak ada data dosen yang bisa ditampilkan. Silakan tambahkan data dosen.</span>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="mb-4 table-auto w-full border-collapse border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr class="text-center">
                                <th class="w-0.5/12 border border-gray-300 px-4 py-2">No.</th>
                                <th class="w-3/12 border border-gray-300 px-4 py-2">Nama Dosen</th>
                                <th class="w-7/12 border border-gray-300 px-4 py-2">NIP</th>
                                <th class="w-1/12 border border-gray-300 px-4 py-2">Tempat Lahir</th>
                                <th class="w-0.5/12 border border-gray-300 px-4 py-2">Tanggal Lahir</th>
                                <th class="w-3/12 border border-gray-300 px-4 py-2">Jenis Kelamin</th>
                                <th class="w-7/12 border border-gray-300 px-4 py-2">Program Studi</th>
                                <th class="w-1/12 border border-gray-300 px-4 py-2">Aksi</th>
                            </tr>
                    </thead>
                    <tbody>
                        @foreach($dosen as $dsn)
                            <tr class="hover:bg-gray-50">
                                <td class="border border-gray-300 px-4 py-2 text-center">{{ $loop->iteration + ($dosen->currentPage() - 1) * $dosen->perPage() }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $dsn->nama_dosen }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $dsn->nip }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $dsn->tempat_lahir }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $dsn->tanggal_lahir }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $dsn->jenis_kelamin }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $dsn->programStudi->nama_prodi }}</td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <div class="flex justify-center space-x-2">
                                        <button data-modal-target="editModal-{{ $dsn->id }}" data-modal-toggle="editModal-{{ $dsn->id }}" class="flex items-center justify-center w-full sm:w-20 md:w-20 px-3 py-1 bg-yellow-400 text-white rounded-lg hover:bg-yellow-600 transition duration-200 mb-2 sm:mb-0">Edit</button>
                                        <!-- Modal Edit -->
                                        <div id="editModal-{{ $dsn->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
                                            <div class="relative p-4 w-full max-w-3xl max-h-full">
                                                <!-- Modal content -->
                                                <div class="relative bg-white rounded-lg shadow-sm">
                                                    <!-- Modal header -->
                                                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                                                        <h3 class="text-lg font-semibold text-gray-900">
                                                            Form Edit Dosen
                                                        </h3>
                                                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="editModal-{{ $dsn->id }}">
                                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                            </svg>
                                                            <span class="sr-only">Close modal</span>
                                                        </button>
                                                    </div>
                                                    <!-- Modal body -->
                                                    <form action="{{ route('dosen.update',  $dsn->id) }}" method="POST" class="p-4 md:p-5">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="grid gap-4 mb-4 grid-cols-2">
                                                            <div class="grid-cols-2">
                                                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Nama Dosen</label>
                                                                <input type="text" name="name" id="name" value="{{ $dsn->user->name }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                                            </div>
                                                            <div class="grid-cols-2">
                                                                <label for="nip" class="block mb-2 text-sm font-medium text-gray-900">NIP</label>
                                                                <input type="text" name="nip" id="nip" value="{{ $dsn->nip }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                                            </div>
                                                            <div class="grid-cols-2">
                                                                <label for="tempat_lahir" class="block mb-2 text-sm font-medium text-gray-900">Tempat Lahir</label>
                                                                <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{ $dsn->tempat_lahir }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                                            </div>
                                                            <div class="grid-cols-2">
                                                                <label for="tanggal_lahir" class="block mb-2 text-sm font-medium text-gray-900">Tanggal Lahir</label>
                                                                <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ $dsn->tanggal_lahir }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                                            </div>
                                                            <div class="grid grid-cols-2 gap-4">
                                                                <label for="jenis_kelamin" class="block text-sm font-medium text-gray-900">Jenis Kelamin</label>
                                                                <div class="flex col-span-2">
                                                                    <div class="flex items-center mb-6">
                                                                        <input id="Laki-laki" type="radio" name="jenis_kelamin" value="Laki-laki" class="w-4 h-4 border-gray-300 focus:ring-2 focus:ring-blue-300" {{ $dsn->jenis_kelamin == 'Laki-laki' ? 'checked' : '' }}>
                                                                        <label for="Laki-laki" class="block ml-2 mr-6 text-sm font-medium text-gray-900">Laki-laki</label>
                                                                    </div>
                                                                    <div class="flex items-center mb-6">
                                                                        <input id="Perempuan" type="radio" name="jenis_kelamin" value="Perempuan" class="w-4 h-4 border-gray-300 focus:ring-2 focus:ring-blue-300" {{ $dsn->jenis_kelamin == 'Perempuan' ? 'checked' : '' }}>
                                                                        <label for="Perempuan" class="block ml-2 text-sm font-medium text-gray-900">Perempuan</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="grid-cols-2">
                                                                <label for="program_studi" class="block mb-2 text-sm font-medium text-gray-900">Program Studi</label>
                                                                <select id="program_studi" name="prodi_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                                                    <option selected disabled>Pilih Program Studi</option>
                                                                    @foreach ($programStudi as $prodi)
                                                                    <option value="{{ $prodi->id }}" {{ $prodi->id == $dsn->prodi_id ? 'selected' : '' }}>{{ $prodi->nama_prodi }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="flex justify-end space-x-2">
                                                            <button type="button" class="text-white inline-flex items-center bg-gray-600 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" data-modal-toggle="editModal-{{ $dsn->id }}">
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
                                        <form id="delete-form-{{ $dsn->id }}" action="{{ route('dosen.destroy', $dsn->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button data-modal-target="popup-modal" data-modal-toggle="popup-modal" class="flex w-full sm:w-20 md:w-20 px-3 py-1 text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" type="button" onclick="openDeleteModal('{{ $dsn->id }}')">
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
                                                            <h3 class="mb-5 text-lg font-normal text-gray-500">Apakah anda yakin ingin menghapus Data Dosen ini?</h3>
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <nav aria-label="Page navigation example">
                {{ $dosen->links() }}
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



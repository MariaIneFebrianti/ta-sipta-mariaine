@extends('layout')
@section('breadcrumb-parent')
    <li>
        @section('breadcrumb-parent')
        <li class="inline-flex items-center">
            <a href="/data-master" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
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
            <span class="text-sm font-medium text-gray-500">Program Studi</span>
        </div>
    </li>
@endsection

@section('content')
<div class="p-4 sm:ml-64">
    <div class="mt-8 flex flex-col md:flex-row items-center h-auto rounded-md bg-gray-50 border border-gray-200 dark:bg-gray-800 p-5">
        <h1 class="text-xl text-left mb-4 md:mb-0 md:w-auto md:flex-1">Data Program Studi</h1>
        @include('layouts.breadcrumb')
    </div>

    <div class="mt-3 p-5 max-h-[500px] overflow-y-auto rounded-md bg-gray-50 border border-gray-200 dark:bg-gray-800">
        <!-- Modal toggle -->
        <button data-modal-target="crud-modal" data-modal-toggle="crud-modal" class="focus:outline-none text-white bg-green-600 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-4 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
            <svg class="w-7 h-7 inline-block" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
            </svg>
            Tambah Program Studi
        </button>

        <!-- Main modal -->
        <div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Form Tambah Program Studi
                        </h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="crud-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <form action="{{ route('program_studi.store') }}" method="POST" class="p-4 md:p-5">
                        @csrf
                        <div class="grid gap-4 mb-4 grid-cols-2">
                            <div class="col-span-2">
                                <label for="kode_prodi" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kode Program Studi</label>
                                <input type="text" name="kode_prodi" id="kode_prodi" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="22001" required />
                            </div>
                            <div class="col-span-2">
                                <label for="nama_prodi" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Program Studi</label>
                                <input type="text" name="nama_prodi" id="nama_prodi" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="D3 Teknik Informatika" required />
                            </div>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" class="text-white inline-flex items-center bg-gray-600 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800" data-modal-toggle="crud-modal">
                                <svg class="me-2 w-2 h-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                Batal
                            </button>
                            <button type="submit" class="text-white inline-flex items-center bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                <svg class="me-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm3 11a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6H8v-6Zm1-7V5h6v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                                    <path fill-rule="evenodd" d="M14 17h-4v-2h4v2Z" clip-rule="evenodd"/>                                </svg>
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <table class="table-auto w-full border-collapse border border-gray-200">
        <thead class="bg-gray-100">
            <tr class="text-center">
                <th class="w-0.5/12 border border-gray-300 px-4 py-2">No.</th>
                <th class="w-3/12 border border-gray-300 px-4 py-2">Kode Program Studi</th>
                <th class="w-7/12 border border-gray-300 px-4 py-2">Nama Program Studi</th>
                <th class="w-1/12 border border-gray-300 px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($programStudi as $prodi)
                <tr class="hover:bg-gray-50">
                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $prodi->kode_prodi }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $prodi->nama_prodi }}</td>
                    <td class="border border-gray-300 px-4 py-2">
                        <div class="flex flex-col sm:flex-row sm:space-x-2">
                            {{-- <a href="{{ route('program_studi.edit', $prodi->id) }}" class="flex items-center justify-center w-full sm:w-20 md:w-20 px-3 py-1 bg-yellow-400 text-white rounded-lg hover:bg-yellow-600 transition duration-200 mb-2 sm:mb-0">
                                Edit
                            </a> --}}
                            <a href="#" data-modal-target="edit-modal" data-modal-toggle="edit-modal" class="flex items-center justify-center w-full sm:w-20 md:w-20 px-3 py-1 bg-yellow-400 text-white rounded-lg hover:bg-yellow-600 transition duration-200 mb-2 sm:mb-0" onclick="openEditModal('{{ $prodi->id }}', '{{ $prodi->kode_prodi }}', '{{ $prodi->nama_prodi }}')">
                                Edit
                            </a>
                            <!-- Modal Edit -->
                            <div id="edit-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
                                <div class="relative p-4 w-full max-w-md max-h-full">
                                    <!-- Modal content -->
                                    <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                                        <!-- Modal header -->
                                        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                Form Edit Program Studi
                                            </h3>
                                            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="edit-modal">
                                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                </svg>
                                                <span class="sr-only">Close modal</span>
                                            </button>
                                        </div>
                                        <!-- Modal body -->
                                        <form id="edit-form" action="{{ route('program_studi.update', ':id') }}" method="POST" class="p-4 md:p-5">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid gap-4 mb-4 grid-cols-2">
                                                <div class="col-span-2">
                                                    <label for="edit_kode_prodi" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kode Program Studi</label>
                                                    <input type="text" name="kode_prodi" id="edit_kode_prodi" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="22001" required />
                                                </div>
                                                <div class="col-span-2">
                                                    <label for="edit_nama_prodi" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Program Studi</label>
                                                    <input type="text" name="nama_prodi" id="edit_nama_prodi" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="D3 Teknik Informatika" required />
                                                </div>
                                            </div>
                                            <div class="flex justify-end space-x-2">
                                                <button type="button" class="text-white inline-flex items-center bg-gray-600 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800" data-modal-toggle="edit-modal">
                                                    <svg class="me-2 w-2 h-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                    </svg>
                                                    Batal
                                                </button>
                                                <button type="submit" class="text-white inline-flex items-center bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                                    <svg class="me-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm3 11a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6H8v-6Zm1-7V5h6v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                                                        <path fill-rule="evenodd" d="M14 17h-4v-2h4v2Z" clip-rule="evenodd"/>                                </svg>
                                                    Simpan
                                                </button>
                                            </div>
                                            {{-- <div class="flex justify-end space-x-2">
                                                <button type="button" class="text-white inline-flex items-center bg-gray-600 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800" data-modal-toggle="edit-modal">
                                                    Batal
                                                </button>
                                                <button type="submit" class="text-white inline-flex items-center bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                                    Simpan
                                                </button>
                                            </div> --}}
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <form action="{{ route('program_studi.destroy', $prodi->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                {{-- <button type="submit" class="flex items-center justify-center w-full sm:w-14 md:w-16 px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition duration-200">
                                    Hapus
                                </button> --}}
                                <button data-modal-target="popup-modal" data-modal-toggle="popup-modal" class="flex w-full sm:w-20 md:w-20 px-3 py-1 text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-blue-700 dark:focus:ring-red-800" type="button">
                                Hapus
                                </button>
                                <div id="popup-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45" >
                                    <div class="relative p-4 w-full max-w-md max-h-full">
                                        <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                                            <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="popup-modal">
                                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                </svg>
                                                <span class="sr-only">Close modal</span>
                                            </button>
                                            <div class="p-4 md:p-5 text-center">
                                                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                                </svg>
                                                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Apakah anda yakin ingin menghapus <br> Data Program Studi ini?</h3>
                                                <button data-modal-hide="popup-modal" type="button" class="w-full sm:w-20 md:w-20 text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 justify-center">
                                                    Ya
                                                </button>
                                                <button data-modal-hide="popup-modal" type="button" class="w-full sm:w-20 md:w-20 py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-grey-200 rounded-lg border border-gray-200 hover:bg-gray-500 hover:text-white focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 justify-center ">Tidak</button>
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

<script>
    function openEditModal(id, kodeProdi, namaProdi) {
        document.getElementById('edit_kode_prodi').value = kodeProdi;
        document.getElementById('edit_nama_prodi').value = namaProdi;
        document.getElementById('edit-form').action = document.getElementById('edit-form').action.replace(':id', id);
        document.getElementById('edit-modal').classList.remove('hidden');
    }
</script>

@endsection



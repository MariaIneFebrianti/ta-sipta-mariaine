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
            <h1 class="text-2xl font-bold  text-left mb-4 md:mb-0 md:w-auto md:flex-1">Data Jadwal Bimbingan</h1>
            @include('layouts.breadcrumb')
        </div>
        <div class="mt-3 p-5 rounded-md bg-gray-50 border border-gray-200">
            @if($userRole === 'Dosen')
                <!-- Modal toggle -->
                <div class="flex justify-between mb-4 flex-wrap">
                    <button data-modal-target="crud-modal" data-modal-toggle="crud-modal" class="focus:outline-none text-white bg-green-600 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-4">
                        <svg class="w-7 h-7 inline-block" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                        </svg>
                        Tambah Jadwal Bimbingan
                    </button>
                    {{-- <div class="flex justify-end mb-4 flex-grow ">
                        <form action="{{ route('mahasiswa.search') }}" method="GET" class="max-w-md  w-full" id="search-form">
                            <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                    </svg>
                                </div>
                                <input type="search" id="search-input" name="search"
                                    class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Cari data mahasiswa disini"
                                    required
                                    value="{{ request('search') }}" style="min-width: 300px"
                                    oninput="document.querySelector('#search-form').submit();" />
                                <button type="submit" class="text-white absolute end-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Search</button>
                            </div>
                        </form>
                    </div> --}}
                </div>

                <!-- Main modal -->
                <div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
                    <div class="relative p-4 w-full max-w-3xl max-h-full">
                        <!-- Modal content -->
                        <div class="relative bg-white rounded-lg shadow-sm">
                            <!-- Modal header -->
                            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Form Tambah Jadwal Bimbingan
                                </h3>
                                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="crud-modal">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <form action="{{ route('jadwal_bimbingan.store') }}" method="POST" class="p-4 md:p-5">
                                @csrf
                                <div class="grid gap-4 mb-4 grid-cols-2">
                                    <div class="col-cols-2">
                                        <label class="block mb-2 text-sm font-medium text-gray-900">Nama Dosen</label>
                                        <input type="text" name="dosen_id" id="dosen_id" value="{{ auth()->user()->dosen->nama_dosen }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" readonly />
                                    </div>
                                    <div class="grid-cols-2">
                                        <label for="tanggal" class="block mb-2 text-sm font-medium text-gray-900">Tanggal </label>
                                        <input type="date" name="tanggal" id="tanggal" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="GKB 1.1" required />
                                    </div>
                                    <div class="grid-cols-2">
                                        <label for="waktu" class="block mb-2 text-sm font-medium text-gray-900">Waktu</label>
                                        <input type="time" name="waktu" id="waktu" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                                    </div>
                                    <div class="grid-cols-2">
                                        <label for="kuota" class="block mb-2 text-sm font-medium text-gray-900">Kuota</label>
                                        <input type="number" name="kuota" id="kuota" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Masukkan kuota" min="1" required />
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

            @if($jadwalBimbingan->isEmpty())
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                    <strong class="font-bold">Perhatian!</strong>
                    @if($userRole === 'Mahasiswa')
                        <span class="block sm:inline">Belum ada jadwal bimbingan dari dosen pembimbing Anda!</span>
                    @elseif($userRole === 'Dosen')
                        <span class="block sm:inline">Tidak ada data jadwal bimbingan Anda.</span>
                    @elseif($userRole === 'Koordinator Program Studi')
                        <span class="block sm:inline">Data jadwal bimbingan tidak ada.</span>
                    @endif
                </div>
            @else
                @if($userRole === 'Mahasiswa')
                <div class="overflow-x-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($jadwalBimbingan as $jadwal)
                            <div class="bg-white rounded-lg shadow-lg p-6 {{ $jadwal->kuota == 0 ? 'bg-gray-200' : '' }}">
                                <h3 class="text-xl font-semibold text-gray-800">{{ $jadwal->dosen->nama_dosen }}</h3>
                                <p class="text-gray-600">Tanggal: <strong>{{ $jadwal->tanggal }}</strong></p>
                                <p class="text-gray-600">Waktu: <strong>{{ $jadwal->waktu }}</strong></p>
                                <p class="text-gray-600">Kuota Tersisa:
                                    <strong class="{{ $jadwal->kuota == 0 ? 'text-red-500' : 'text-red-600' }}">
                                        {{ $jadwal->kuota }}
                                    </strong>
                                </p>

                                @if ($jadwal->kuota > 0)
                                    @if (!$jadwal->sudahMendaftar)
                                        <form action="{{ route('jadwal_bimbingan.daftar', $jadwal->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="mt-4 w-full bg-green-600 text-white font-semibold py-2 rounded-lg hover:bg-green-600 transition">
                                                Daftar Bimbingan
                                            </button>
                                        </form>
                                    @else
                                    <button type="button" class="mt-4 w-full bg-yellow-300 text-black font-semibold py-2 rounded-lg pointer-events-none" disabled>
                                        Anda sudah Mendaftar!
                                        </button>
                                    @endif
                                @else
                                    <button type="button"  class="mt-4 w-full bg-red-600 text-white py-2 rounded-lg pointer-events-none" disabled>
                                        Maaf, Kuota Penuh!
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                        @if(session('success'))
                            <div id="popup" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-50">
                                <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                                <h2 class="text-lg font-semibold text-green-600">{{ session('success') }}</h2>
                                <p class="mt-2 text-gray-700">
                                    Nama Dosen: <strong>{{ session('dosen') }}</strong> <br>
                                    Tanggal: <strong>{{ session('tanggal') }}</strong> <br>
                                    Pukul: <strong>{{ session('waktu') }}</strong>
                                </p>
                                    <div class="mt-4 flex justify-center gap-4">
                                        <button onclick="document.getElementById('popup').style.display='none'"
                                            class="px-4 py-2 bg-gray-400 text-white rounded-lg">Kembali</button>
                                        <a href="{{ route('logbook_bimbingan.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                                            Lihat Logbook Bimbingan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                @if(in_array($userRole, ['Dosen', 'Koordinator Program Studi']))
                <div class="overflow-x-auto">
                        <table class="mb-4 table-auto w-full border-collapse border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr class="text-center">
                                        <th class="w-1/12 border border-gray-300 px-4 py-2">No.</th>
                                        @if($userRole === 'Koordinator Program Studi')
                                            <th class="w-2/12 border border-gray-300 px-4 py-2">Nama Dosen</th>
                                        @endif
                                        <th class="w-2/12 border border-gray-300 px-4 py-2">Tanggal</th>
                                        <th class="w-2/12 border border-gray-300 px-4 py-2">Waktu</th>
                                        <th class="w-2/12 border border-gray-300 px-4 py-2">Kuota</th>
                                        <th class="w-3/12 border border-gray-300 px-4 py-2">Status</th>
                                    </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwalBimbingan as $jadwal)
                                    <tr class="hover:bg-gray-50 text-center">
                                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $loop->iteration + ($jadwalBimbingan->currentPage() - 1) * $jadwalBimbingan->perPage() }}</td>
                                        @if($userRole === 'Koordinator Program Studi')
                                            <td class="border border-gray-300 px-4 py-2">{{ $jadwal->dosen->nama_dosen }}</td>
                                        @endif
                                        <td class="border border-gray-300 px-4 py-2">{{ $jadwal->tanggal }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $jadwal->waktu }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $jadwal->kuota }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $jadwal->status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

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




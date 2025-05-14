@extends('layout')

@section('breadcrumb-parent')
    <li class="inline-flex items-center">
        <a href="/data-master" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
            <svg class="w-4 h-4 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 22">
                <path fill-rule="evenodd" d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm3 11a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6H8v-6Zm1-7V5h6v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                <path fill-rule="evenodd" d="M14 17h-4v-2h4v2Z" clip-rule="evenodd"/>
            </svg>
            Data Master
        </a>
    </li>
@endsection

@section('breadcrumb-item')
    <li>
        <div class="flex items-center">
            <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
            </svg>
            <span class="text-sm font-medium text-gray-500">User</span>
        </div>
    </li>
@endsection

@section('content')
<div class="sm:ml-64">
    <div class="mt-8 flex flex-col md:flex-row items-center h-auto rounded-md bg-gray-50 border border-gray-200 p-5">
        <h1 class="text-2xl font-bold text-left mb-4 md:mb-0 md:w-auto md:flex-1">Data User</h1>
        @include('layouts.breadcrumb')
    </div>
    <div class="mt-3 p-5 rounded-md bg-gray-50 border border-gray-200">
        {{-- <div class="flex justify-end mb-4"> --}}
            <div class="flex justify-between items-center mb-4 flex-wrap">
            <form id="searchForm" action="{{ route('user.dropdown-search') }}" method="GET" class="mb-0">
                <div class="flex flex-col md:flex-row gap-4 w-full mb-4">
                    <!-- Dropdown Role User -->
                    <div class="flex-1 min-w-[200px]">
                        <label for="role" class="block text-sm font-medium text-gray-900 dark:text-white mb-2 mr-2">Role User</label>
                        <select name="role" id="role"
                            class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                            onchange="document.getElementById('searchForm').submit();">
                            <option value="">Semua Role</option>
                            <option value="Mahasiswa" {{ request()->get('role') == 'Mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="Dosen" {{ request()->get('role') == 'Dosen' ? 'selected' : '' }}>Dosen</option>
                            <option value="Koordinator Program Studi" {{ request()->get('role') == 'Koordinator Program Studi' ? 'selected' : '' }}>Koordinator Program Studi</option>
                            <option value="Super Admin" {{ request()->get('role') == 'Super Admin' ? 'selected' : '' }}>Super Admin</option>
                        </select>
                    </div>
                </div>
            </form>
            <form action="{{ route('user.search') }}" method="GET" class="w-full sm:max-w-xs mt-3" id="search-form">
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="search" id="search-input" name="search"
                        class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Cari data mahasiswa disini"
                        required value="{{ request('search') }}"
                        oninput="document.querySelector('#search-form').submit();"
                    />
                </div>
            </form>
            </div>
        {{-- </div> --}}
        @if($user->isEmpty())
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                <strong class="font-bold">Perhatian!</strong>
                <span class="block sm:inline">Tidak ada data user yang bisa ditampilkan!</span>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="mb-4 table-auto w-full border-collapse border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="w-0.5/12 border border-gray-300 px-4 py-2">No.</th>
                            <th class="w-3/12 border border-gray-300 px-4 py-2">Nama</th>
                            <th class="w-3/12 border border-gray-300 px-4 py-2">Email</th>
                            <th class="w-2/12 border border-gray-300 px-4 py-2">Role</th>
                            <th class="w-3/12 border border-gray-300 px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($user as $data)
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $loop->iteration + ($user->currentPage() - 1) * $user->perPage() }}</td>
                            <td class="border px-4 py-2">{{ $data->name }}</td>
                            <td class="border px-4 py-2">{{ $data->email }}</td>
                            <td class="border px-4 py-2">{{ $data->role }}</td>
                            <td class="border px-4 py-2 text-center">
                                <div class="flex justify-center space-x-2">
                                    <form action="{{ route('user.resetPassword', $data) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-red-500 text-white hover:bg-red-700 px-4 py-2 rounded">
                                            Reset Password
                                        </button>
                                    </form>
                                    <button data-modal-target="editModal-{{ $data->id }}" data-modal-toggle="editModal-{{ $data->id }}" class="flex items-center justify-center w-full sm:w-20 md:w-20 px-3 py-1 bg-yellow-400 text-white rounded-lg hover:bg-yellow-600 transition duration-200 mb-2 sm:mb-0">Edit</button>
                                        <!-- Modal Edit -->
                                        <div id="editModal-{{ $data->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
                                            <div class="relative p-4 w-full max-w-3xl max-h-full">
                                                <!-- Modal content -->
                                                <div class="relative bg-white rounded-lg shadow-sm">
                                                    <!-- Modal header -->
                                                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                                                        <h3 class="text-lg font-semibold text-gray-900">
                                                            Form Edit User
                                                        </h3>
                                                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="editModal-{{ $data->id }}">
                                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                            </svg>                                                                <span class="sr-only">Close modal</span>
                                                        </buton>
                                                    </div>
                                                    <!-- Modal body -->
                                                    <form action="{{ route('user.update', $data->id) }}" method="POST" class="p-4 md:p-5">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="grid gap-4 mb-4 grid-cols-2">
                                                            <div class="grid-cols-2">
                                                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Username</label>
                                                                <input type="text" name="name" id="name" value="{{ $data->name }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required disabled>
                                                            </div>
                                                            <div class="grid-cols-2">
                                                                <label for="role" class="block mb-2 text-sm font-medium text-gray-900">Role</label>
                                                                <select id="role" name="role"
                                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                                                    <option selected disabled>Pilih Role</option>
                                                                    <option value="Mahasiswa" {{ $data->role == 'Mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                                                    <option value="Dosen" {{ $data->role == 'Dosen' ? 'selected' : '' }}>Dosen</option>
                                                                    <option value="Koordinator Program Studi" {{ $data->role == 'Koordinator Program Studi' ? 'selected' : '' }}>Koordinator Program Studi</option>
                                                                    <option value="Super Admin" {{ $data->role == 'Super Admin' ? 'selected' : '' }}>Super Admin</option>
                                                                </select>
                                                            </div>
                                                       </div>
                                                        <div class="flex justify-end space-x-2">
                                                            <button type="button" class="text-white inline-flex items-center bg-gray-600 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" data-modal-toggle="editModal-{{ $data->id }}">
                                                                <svg class="me-2 w-2 h-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                                </svg>
                                                                Batal
                                                            </button>
                                                            <button type="submit" class="text-white inline-flex items-center bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
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
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <nav aria-label="Page navigation example">
                {{ $user->links() }} <!-- Ini akan menghasilkan pagination -->
            </nav>
        @endif
    </div>
</div>
@endsection

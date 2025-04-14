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
        <div class="flex justify-end mb-4">
            <form action="{{ route('user.search') }}" method="GET" class="max-w-md  w-full" id="search-form">
                <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="search" id="search-input" name="search"
                        class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Search by name or email..."
                        required
                        value="{{ request('search') }}" style="min-width: 300px"
                        oninput="document.querySelector('#search-form').submit();" />
                    <button type="submit" class="text-white absolute end-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Search</button>
                </div>
            </form>
        </div>
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
                                <form action="{{ route('user.resetPassword', $data) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-red-500 text-white hover:bg-red-700 px-4 py-2 rounded">
                                        Reset Password
                                    </button>
                                </form>
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

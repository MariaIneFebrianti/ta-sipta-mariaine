<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rekap Dosen Pembimbing</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
        }

        .container {
            padding-left: 40px;
            padding-right: 40px;
            background: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .data th, .data td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3 class="text-center" style="margin-top: 20px;">REKAP DOSEN PEMBIMBING MAHASISWA</h3>

        <table class="data" style="margin-top: 20px;">
            <thead style="background-color: #f3f4f6;">
                <tr>
                    <th>No</th>
                    <th>Nama Dosen</th>
                    <th>Peran</th>
                    <th>NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Total Per Peran</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($rekap as $item)
                    @php
                        $totalDetail = count($item['detail']);
                        $groupedByPeran = collect($item['detail'])->groupBy('peran');
                        $firstRow = true;
                    @endphp

                    @foreach ($groupedByPeran as $peran => $details)
                        @foreach ($details as $i => $mhs)
                            <tr>
                                {{-- No dan Nama Dosen hanya di baris pertama --}}
                                @if ($firstRow)
                                    <td rowspan="{{ $totalDetail }}">{{ $no++ }}</td>
                                    <td rowspan="{{ $totalDetail }}" class="text-left">{{ $item['nama_dosen'] }}</td>
                                    @php $firstRow = false; @endphp
                                @endif

                                {{-- Peran hanya di baris pertama per grup --}}
                                @if ($i === 0)
                                    <td rowspan="{{ count($details) }}">{{ $peran }}</td>
                                @endif

                                <td>{{ $mhs->nim }}</td>
                                <td class="text-left">{{ $mhs->nama_mahasiswa }}</td>

                                {{-- Total Per Peran hanya di baris pertama per grup --}}
                                @if ($i === 0)
                                    <td rowspan="{{ count($details) }}">{{ count($details) }}</td>
                                @endif

                                {{-- Total keseluruhan hanya di baris pertama --}}
                                @if ($loop->parent->first && $i === 0)
                                    <td rowspan="{{ $totalDetail }}">{{ $totalDetail }}</td>
                                @endif
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>

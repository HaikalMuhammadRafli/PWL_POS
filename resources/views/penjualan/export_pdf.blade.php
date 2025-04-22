<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            margin: 6px 20px 5px 20px;
            line-height: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 4px 3px;
        }

        th {
            text-align: left;
        }

        .d-block {
            display: block;
        }

        img.image {
            width: auto;
            height: 80px;
            max-width: 150px;
            max-height: 150px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .p-1 {
            padding: 5px 1px 5px 1px;
        }

        .font-10 {
            font-size: 10pt;
        }

        .font-11 {
            font-size: 11pt;
        }

        .font-12 {
            font-size: 12pt;
        }

        .font-13 {
            font-size: 13pt;
        }

        .border-bottom-header {
            border-bottom: 1px solid;
        }

        .border-all,
        .border-all th,
        .border-all td {
            border: 1px solid;
        }
    </style>
</head>

<body>
    <table class="border-bottom-header">
        <tr>
            <td class="text-center" style="width: 100px; height: 100px;"><img src="{{ public_path('polinema-bw.png') }}"
                    style="width: 80px; height: 80px;"></td>
            <td width="85%">
                <span class="text-center d-block font-11 font-bold mb-1">KEMENTERIAN
                    PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</span>
                <span class="text-center d-block font-13 font-bold mb-1">POLITEKNIK NEGERI
                    MALANG</span>
                <span class="text-center d-block font-10">Jl. Soekarno-Hatta No. 9 Malang
                    65141</span>
                <span class="text-center d-block font-10">Telepon (0341) 404424 Pes. 101
                    105, 0341-404420, Fax. (0341) 404420</span>
                <span class="text-center d-block font-10">Laman: www.polinema.ac.id</span>
            </td>
        </tr>
    </table>

    <h3 class="text-center">LAPORAN DATA PENJUALAN</h4>
        <table class="border-all">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">User</th>
                    <th class="text-center">Pembeli</th>
                    <th class="text-center">Kode</th>
                    <th class="text-center">Tanggal</th>
                    <th class="text-center">Barang</th>
                    <th class="text-center">Harga</th>
                    <th class="text-center">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $number = 1;
                @endphp
                @foreach ($penjualans as $penjualan)
                    @foreach ($penjualan->detail as $i => $detail)
                        <tr>
                            @if ($i == 0)
                                <td class="text-center" rowspan="{{ count($penjualan->detail) }}">{{ $number++ }}
                                </td>
                                <td class="text-center" rowspan="{{ count($penjualan->detail) }}">
                                    {{ $penjualan->user->nama }}</td>
                                <td class="text-center" rowspan="{{ count($penjualan->detail) }}">
                                    {{ $penjualan->pembeli }}</td>
                                <td class="text-center" rowspan="{{ count($penjualan->detail) }}">
                                    {{ $penjualan->penjualan_kode }}</td>
                                <td class="text-center" rowspan="{{ count($penjualan->detail) }}">
                                    {{ $penjualan->penjualan_tanggal }}</td>
                            @endif
                            <td class="text-center">{{ $detail->barang->barang_nama }}</td>
                            <td class="text-center">{{ $detail->harga }}</td>
                            <td class="text-center">{{ $detail->jumlah }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
</body>

</html>

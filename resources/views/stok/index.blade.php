@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('stok/import') }}')" class="btn btn-info">Import Stok</button>
                <a href="{{ url('stok/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export
                    Excel</a>
                <a href="{{ url('stok/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export
                    Pdf</a>
                <button onclick="modalAction('{{ url('stok/create_ajax') }}')" class="btn btn-success"><i
                        class="fa-solid fa-plus"></i> Tambah Ajax</button>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <table class="table table-bordered table-striped table-hover table-sm" id="table_stok">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Supplier</th>
                        <th>Barang</th>
                        <th>User</th>
                        <th>Tanggal</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
        data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('css')
@endpush

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $(this).modal('show');
            });
        }

        var dataStok;
        $(document).ready(function() {
            dataStok = $('#table_stok').DataTable({
                serverSide: true,
                ajax: {
                    "url": "{{ url('stok/list') }}",
                    "dataType": "json",
                    "type": "POST",
                },
                columns: [{
                        data: 'DT_RowIndex',
                        className: 'text-center',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "supplier.supplier_nama",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "barang.barang_nama",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "user.nama",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "stok_tanggal",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "stok_jumlah",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "aksi",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#table_stok').change(function() {
                dataStok.ajax.reload();
            });
        })
    </script>
@endpush

@empty($penjualan)
    <div id="myModal" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang anda cari tidak ditemukan
                </div>
                <a href="{{ url('/penjualan') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/penjualan/' . $penjualan->penjualan_id . '/update_ajax') }}" method="POST" id="form-edit">
        @csrf
        @method('PUT')
        <div id="myModal" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Data Penjualan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>User</label>
                        <select name="user_id" id="user_id" class="form-control" required>
                            <option value="">- Pilih User -</option>
                            @foreach ($users as $u)
                                <option {{ $u->user_id == $penjualan->user_id ? 'selected' : '' }}
                                    value="{{ $u->user_id }}">
                                    {{ $u->nama }}</option>
                            @endforeach
                        </select>
                        <small id="error-user_id" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Pembeli</label>
                        <input value="{{ $penjualan->pembeli }}" type="text" name="pembeli" id="pembeli"
                            class="form-control" required>
                        <small id="error-pembeli" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Kode</label>
                        <input value="{{ $penjualan->penjualan_kode }}" type="text" name="penjualan_kode"
                            id="penjualan_kode" class="form-control" required>
                        <small id="error-penjualan_kode" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input value="{{ date('Y-m-d', strtotime($penjualan->penjualan_tanggal)) }}" type="date"
                            name="penjualan_tanggal" id="penjualan_tanggal" class="form-control" required>
                        <small id="error-penjualan_tanggal" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary btn-sm" id="btn-add-item">
                                <i class="fa fa-plus"></i> Tambah Barang
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="detail-table" class="table table-bordered rounded overflow-hidden">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Barang</th>
                                    <th class="text-center">Harga Barang</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Harga</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="detail-items">
                                @foreach ($penjualan->detail as $i => $detail)
                                    <tr class="detail-row">
                                        <td>
                                            <span class="no">{{ $i + 1 }}</span>
                                        </td>
                                        <td>
                                            <select name="barang_id[]" class="form-control barang-select" required>
                                                <option value="">- Pilih Barang -</option>
                                                @foreach ($barangs as $barang)
                                                    <option {{ $barang->barang_id == $detail->barang_id ? 'selected' : '' }}
                                                        value="{{ $barang->barang_id }}"
                                                        data-harga="{{ $barang->harga_jual }}"
                                                        data-stok="{{ $barang->getStok() }}">
                                                        {{ $barang->barang_nama }}</option>
                                                @endforeach
                                            </select>
                                            <small class="error-text form-text text-danger "></small>
                                        </td>
                                        <td>
                                            <input value="{{ $detail->barang->harga_jual }}" type="text"
                                                name="harga_barang[]" class="form-control harga-barang-input" readonly>
                                            <small class="error-text form-text text-danger"></small>
                                        </td>
                                        <td>
                                            <input value="{{ $detail->jumlah }}" type="number" name="jumlah[]"
                                                class="form-control jumlah-input" min="1" value="1" required>
                                            <small class="stok-terinfo text-muted d-block">Stok:
                                                {{ $detail->barang->getStok() }}</small>
                                            <small class="error-text form-text text-danger"></small>
                                        </td>
                                        <td>
                                            <input value="{{ $detail->harga }}" type="text" name="harga[]"
                                                class="form-control harga-input" required readonly>
                                            <small class="error-text form-text text-danger"></small>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm btn-remove-item">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Total</th>
                                    <th id="total-amount" class="text-right">0</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </form>

    <template id="detail-row-template">
        <tr class="detail-row">
            <td>
                <span class="no"></span>
            </td>
            <td>
                <select name="barang_id[]" class="form-control barang-select" required>
                    <option value="">- Pilih Barang -</option>
                    @foreach ($barangs as $barang)
                        <option value="{{ $barang->barang_id }}" data-harga="{{ $barang->harga_jual }}"
                            data-stok="{{ $barang->getStok() }}">
                            {{ $barang->barang_nama }}</option>
                    @endforeach
                </select>
                <small class="error-text form-text text-danger "></small>
            </td>
            <td>
                <input type="text" name="harga_barang[]" class="form-control harga-barang-input" readonly>
                <small class="error-text form-text text-danger"></small>
            </td>
            <td>
                <input type="number" name="jumlah[]" class="form-control jumlah-input" min="1" value="1"
                    required>
                <small class="stok-terinfo text-muted d-block">Stok: -</small>
                <small class="error-text form-text text-danger"></small>
            </td>
            <td>
                <input type="text" name="harga[]" class="form-control harga-input" readonly>
                <small class="error-text form-text text-danger"></small>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm btn-remove-item">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    </template>

    <script>
        $(document).ready(function() {

            calculateTotal();

            $("#btn-add-item").click(function() {
                addDetailRow();
            });

            $(document).on('click', '.btn-remove-item', function() {
                if ($('.detail-row').length > 1) {
                    $(this).closest('tr').remove();
                    calculateTotal();
                    renumberRows();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Tidak dapat dihapus',
                        text: 'Minimal harus ada satu barang'
                    });
                }
            });

            $(document).on('change', '.barang-select', function() {
                var row = $(this).closest('tr');
                var selected = $(this).find(':selected');
                var harga = selected.data('harga') || 0;
                var stok = selected.data('stok') || 0;

                row.find('.harga-barang-input').val(harga);
                row.find('.jumlah-input').attr('max', stok).attr('title', 'Stok Tersedia: ' + stok);
                row.find(".stok-terinfo").text("Stok: " + stok);

                var currentJumlah = parseInt(row.find('.jumlah-input').val());
                if (currentJumlah > stok) {
                    row.find('.jumlah-input').val(stok);
                }

                if (currentJumlah < 1) {
                    row.find('.jumlah-input').val(1);
                }

                updateHarga(row);
            });

            $(document).on('input', '.jumlah-input', function() {
                updateHarga($(this).closest('tr'));
            });

            $("#form-edit").validate({
                rules: {
                    user_id: {
                        required: true,
                        number: true
                    },
                    pembeli: {
                        required: true,
                        minlength: 3,
                        maxlength: 100
                    },
                    penjualan_kode: {
                        required: true,
                        minlength: 3,
                        maxlength: 20
                    },
                    penjualan_tanggal: {
                        required: true,
                        date: true
                    },
                    "barang_id[]": {
                        required: true
                    },
                    "harga[]": {
                        required: true,
                        number: true
                    },
                    "jumlah[]": {
                        required: true,
                        number: true,
                        min: 1
                    }
                },
                submitHandler: function(form) {

                    if ($('.detail-row').length === 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            text: 'Minimal harus ada satu barang'
                        });
                        return false;
                    }

                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: $(form).serialize(),
                        success: function(response) {
                            if (response.status) {
                                $('#myModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                                dataPenjualan.ajax.reload();
                            } else {
                                $('.error-text').text('');
                                $.each(response.msgField, function(prefix, val) {
                                    $('#error-' + prefix).text(val[0]);
                                });
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: response.message
                                });
                            }
                        }
                    });
                    return false;
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
        });

        function renumberRows() {
            $('.detail-row').each(function(index) {
                $(this).find('.no').text(index + 1);
            });
        }

        function addDetailRow() {
            var template = document.getElementById('detail-row-template');
            var clone = document.importNode(template.content, true);
            $('#detail-items').append(clone);
            renumberRows();
        }

        function updateHarga(row) {
            var harga_barang = parseFloat(row.find('.harga-barang-input').val()) || 0;
            var jumlah = parseInt(row.find('.jumlah-input').val()) || 0;
            var harga = harga_barang * jumlah;

            row.find('.harga-input').val(harga);

            calculateTotal();
        }

        function calculateTotal() {
            var total = 0;
            $('.harga-input').each(function() {
                total += parseFloat($(this).val()) || 0;
            });

            var formattedTotal = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(total);

            $('#total-amount').text(formattedTotal);
        }
    </script>
@endempty

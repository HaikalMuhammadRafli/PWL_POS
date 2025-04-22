<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\PenjualanDetailModel;
use App\Models\PenjualanModel;
use App\Models\StokModel;
use App\Models\UserModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Validator;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\DataTables;

class PenjualanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Penjualan',
            'list' => [
                'Home',
                'Penjualan'
            ]
        ];

        $page = (object) [
            'title' => 'Daftar penjualan yang terdaftar dalam sistem'
        ];

        $activeMenu = 'penjualan';

        return view('penjualan.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }

    public function list(Request $request)
    {
        $penjualans = PenjualanModel::select('penjualan_id', 'user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal')->with('user');

        if ($request->user_id) {
            $penjualans->where('user_id', $request->user_id);
        }

        return DataTables::of($penjualans)->addIndexColumn()->addColumn('aksi', function ($penjualan) {
            $btn = '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
            $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
            $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/delete_ajax') . '\')"  class="btn btn-danger btn-sm">Hapus</button> ';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    public function create_ajax()
    {
        $users = UserModel::select('user_id', 'nama')->get();
        $barangs = BarangModel::select('barang_id', 'barang_nama', 'harga_jual')->get();

        return view('penjualan.create_ajax', ['users' => $users, 'barangs' => $barangs]);
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'pembeli' => 'required|string|max:50',
                'penjualan_kode' => 'required|string|max:20',
                'penjualan_tanggal' => 'required|date',
                'barang_id' => 'required|array',
                'barang_id.*' => 'required|exists:m_barang,barang_id',
                'harga' => 'required|array',
                'harga.*' => 'required|numeric',
                'jumlah' => 'required|array',
                'jumlah.*' => 'required|numeric',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            try {

                DB::beginTransaction();

                $penjualan = PenjualanModel::create([
                    'user_id' => auth()->user()->user_id,
                    'pembeli' => $request->pembeli,
                    'penjualan_kode' => $request->penjualan_kode,
                    'penjualan_tanggal' => $request->penjualan_tanggal,
                ]);

                foreach ($request->barang_id as $index => $barang_id) {
                    $barang = BarangModel::find($barang_id);
                    if ($barang->getStok() < $request->jumlah[$index]) {
                        throw new \Exception("Stok barang ID " . $barang->barang_nama . " tidak mencukupi. " . $barang->getStok());
                    }

                    PenjualanDetailModel::create([
                        'penjualan_id' => $penjualan->penjualan_id,
                        'barang_id' => $barang_id,
                        'harga' => $request->harga[$index],
                        'jumlah' => $request->jumlah[$index],
                    ]);

                    // $stok = StokModel::where('barang_id', $barang_id)->first();
                    // if ($stok) {
                    //     if ($stok->stok_jumlah < $request->jumlah[$index]) {
                    //         throw new \Exception("Stok barang ID $barang_id tidak mencukupi.");
                    //     }

                    //     $stok->stok_jumlah -= $request->jumlah[$index];
                    //     $stok->save();
                    // } else {
                    //     throw new \Exception("Stok barang ID $barang_id tidak ditemukan.");
                    // }
                }

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Data penjualan berhasil disimpan!',
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Data penjualan gagal disimpan!',
                ]);
            }
        }
        return redirect('/penjualan');
    }

    public function show_ajax($id)
    {
        $penjualan = PenjualanModel::with('detail')->find($id);
        return view('penjualan.show_ajax', ['penjualan' => $penjualan]);
    }

    public function edit_ajax($id)
    {
        $penjualan = PenjualanModel::with('detail')->find($id);
        $users = UserModel::select('user_id', 'nama')->get();
        $barangs = BarangModel::select('barang_id', 'barang_nama', 'harga_jual')->get();

        return view('penjualan.edit_ajax', ['penjualan' => $penjualan, 'users' => $users, 'barangs' => $barangs]);
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'user_id' => 'required|exists:m_user,user_id',
                'pembeli' => 'required|string|max:50',
                'penjualan_kode' => 'required|string|max:20',
                'penjualan_tanggal' => 'required|date',
                'barang_id' => 'required|array',
                'barang_id.*' => 'required|exists:m_barang,barang_id',
                'harga' => 'required|array',
                'harga.*' => 'required|numeric',
                'jumlah' => 'required|array',
                'jumlah.*' => 'required|numeric',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            try {
                DB::beginTransaction();

                $penjualan = PenjualanModel::findOrFail($id);

                // $oldDetails = PenjualanDetailModel::where('penjualan_id', $penjualan->penjualan_id)->get();
                // foreach ($oldDetails as $detail) {
                //     $stok = StokModel::where('barang_id', $detail->barang_id)->first();
                //     if ($stok) {
                //         $stok->stok_jumlah += $detail->jumlah;
                //         $stok->save();
                //     }
                // }

                PenjualanDetailModel::where('penjualan_id', $penjualan->penjualan_id)->delete();

                $penjualan->update([
                    'user_id' => $request->user_id,
                    'pembeli' => $request->pembeli,
                    'penjualan_kode' => $request->penjualan_kode,
                    'penjualan_tanggal' => $request->penjualan_tanggal,
                ]);

                foreach ($request->barang_id as $index => $barang_id) {
                    $barang = BarangModel::find($barang_id);
                    if ($barang->getStok() < $request->jumlah[$index]) {
                        throw new \Exception("Stok barang ID " . $barang->barang_nama . " tidak mencukupi. " . $barang->getStok());
                    }

                    $jumlah = $request->jumlah[$index];

                    PenjualanDetailModel::create([
                        'penjualan_id' => $penjualan->penjualan_id,
                        'barang_id' => $barang_id,
                        'harga' => $request->harga[$index],
                        'jumlah' => $jumlah,
                    ]);

                    // $stok = StokModel::where('barang_id', $barang_id)->first();
                    // if ($stok) {
                    //     if ($stok->stok_jumlah < $jumlah) {
                    //         throw new \Exception("Stok barang ID $barang_id tidak mencukupi.");
                    //     }

                    //     $stok->stok_jumlah -= $jumlah;
                    //     $stok->save();
                    // } else {
                    //     throw new \Exception("Stok barang ID $barang_id tidak ditemukan.");
                    // }
                }

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Data penjualan berhasil diperbarui!',
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ]);
            }
        }
        return redirect('/penjualan');
    }

    public function confirm_ajax(string $id)
    {
        $penjualan = PenjualanModel::find($id);

        return view('penjualan.confirm_ajax', ['penjualan' => $penjualan]);
    }

    public function delete_ajax(Request $request, string $id)
    {
        try {
            if ($request->ajax() || $request->wantsJson()) {
                $penjualan = PenjualanModel::find($id);

                if ($penjualan) {
                    foreach ($penjualan->detail as $detail) {
                        $detail->delete();
                    }

                    $penjualan->delete();
                    return response()->json([
                        'status' => true,
                        'message' => 'Data penjualan berhasil dihapus!',
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data penjualan tidak ditemukan!',
                    ]);
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data penjualan gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini'
            ]);
        }
        return redirect('/penjualan');
    }

    public function import(Request $request)
    {
        return view('penjualan.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_penjualan' => ['required', 'mimes:xlsx,xls,csv', 'max:1024'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('file_penjualan');

            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();

            $data = $sheet->toArray(null, false, true, true);

            $insert_penjualan = [];
            $insert_detail = [];
            if (count($data) > 1) {
                foreach ($data as $baris => $value) {
                    if ($baris > 1) {
                        $insert_penjualan[] = [
                            'pembeli' => $value['A'],
                            'penjualan_kode' => $value['B'],
                            'penjualan_tanggal' => $value['C'],
                            'user_id' => auth()->user()->user_id,
                        ];
                        $insert_detail[] = [
                            'penjualan_kode' => $value['B'],
                            'barang_nama' => $value['D'],
                            'harga' => $value['E'],
                            'jumlah' => $value['F']
                        ];
                    }
                }

                if (count($insert_penjualan) > 0) {
                    PenjualanModel::insertOrIgnore($insert_penjualan);
                }

                $penjualan_map = PenjualanModel::whereIn('penjualan_kode', array_column($insert_penjualan, 'penjualan_kode'))
                    ->pluck('penjualan_id', 'penjualan_kode')->toArray();

                $barang_map = BarangModel::whereIn('barang_nama', array_column($insert_detail, 'barang_nama'))
                    ->pluck('barang_id', 'barang_nama')->toArray();

                foreach ($insert_detail as $i => $detail) {
                    $insert_detail[$i]['penjualan_id'] = $penjualan_map[$detail['penjualan_kode']] ?? null;
                    $insert_detail[$i]['barang_id'] = $barang_map[$detail['barang_nama']] ?? null;

                    // Buang kolom bantu
                    unset($insert_detail[$i]['penjualan_kode']);
                    unset($insert_detail[$i]['barang_nama']);
                }

                if (count($insert_detail) > 0) {
                    PenjualanDetailModel::insertOrIgnore($insert_detail);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data penjualan berhasil diimport!',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada data stok yang diimport!',
                ]);
            }
        }
        return redirect('/penjualan');
    }

    public function export_excel()
    {
        $penjualans = PenjualanModel::with(['user', 'detail.barang'])->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'User');
        $sheet->setCellValue('C1', 'Pembeli');
        $sheet->setCellValue('D1', 'Penjualan Kode');
        $sheet->setCellValue('E1', 'Penjualan Tanggal');
        $sheet->setCellValue('F1', 'Barang');
        $sheet->setCellValue('G1', 'Harga');
        $sheet->setCellValue('H1', 'Jumlah');

        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;
        foreach ($penjualans as $penjualan) {
            $sheet->setCellValue('A' . $baris, $no++);
            $sheet->setCellValue('B' . $baris, $penjualan->user->nama);
            $sheet->setCellValue('C' . $baris, $penjualan->pembeli);
            $sheet->setCellValue('D' . $baris, $penjualan->penjualan_kode);
            $sheet->setCellValue('E' . $baris, $penjualan->penjualan_tanggal);
            foreach ($penjualan->detail as $detail) {
                $sheet->setCellValue('F' . $baris, $detail->barang->barang_nama);
                $sheet->setCellValue('G' . $baris, $detail->harga);
                $sheet->setCellValue('H' . $baris, $detail->jumlah);
                $baris++;
            }
        }

        foreach (range('A', 'H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Penjualan');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Date Penjualan ' . date('Y-m-d H:i:s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $penjualans = PenjualanModel::with(['user', 'detail.barang'])->get();

        $pdf = Pdf::loadView('penjualan.export_pdf', ['penjualans' => $penjualans]);
        $pdf->setPaper('A4', 'potrait');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();

        return $pdf->download('Data Penjualan ' . date('Y-m-d H:i:s') . '.pdf');
    }
}

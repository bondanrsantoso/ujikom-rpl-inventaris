<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Lib\UserTokenManager;

use App\Inventaris;
use App\Jenis;
use App\Ruang;
use App\DetailPinjam;
use App\Peminjaman;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        if($currentUser->id_level == 1 || $currentUser->id_level == 2 ){
            return view('pages.peminjaman.index')->with('user', $currentUser)->with('token', UserTokenManager::generateToken($currentUser));
        }
        return redirect("peminjaman/new");
    }

    public function new(Request $request)
    {
        return view('pages.peminjaman.filing');
    }

    public function returnInventaris(Request $request)
    {
        $currentUser = Auth::user();
        if($currentUser->id_level != 1 || $currentUser->id_level != 2 ){
            return response()->json([
                "message" => "Forbidden"
            ], 403);
        }

        $id = $request->input("id");
        $peminjaman = Peminjaman::find($id);
        $peminjaman->kembali = 1;
        $peminjaman->save();

        return response()->json([
            "message" => "updated"
        ], 200);
    }

    public function checkAvailableStock(Request $request)
    {
        $inventarisID = $request->input('id_inventaris');
        $takeawayDate = $request->input('takeaway_date');
        $returnDate = $request->input('return_date');
        
        // $stock = $this->calculateRemaining($inventarisID, $takeawayDate, $returnDate);
        $stock = Inventaris::find($inventarisID)->jumlah;

        return response()->json([
            "stock" => $stock
        ]);
    }

    private $lol = "A";

    public function calculateRemaining(int $inventarisID, string $takeawayDate, string $returnDate)
    {
        $stock = Inventaris::find($inventarisID)->jumlah;

        // $peminjaman = Peminjaman::whereRaw("(tanggal_pinjam <= '".$takeawayDate."' OR tanggal_kembali >= '".$takeawayDate."'")
        $Peminjaman = Peminjaman::whereBetween("tanggal_pinjam", [$takeawayDate, $returnDate])
            ->orWhereBetween("tanggal_kembali", [$takeawayDate, $returnDate])
            ->orWhere(function($query) use(&$takeawayDate, &$returnDate){
                $query->where("tanggal_pinjam", ">=", $takeawayDate)
                    ->where("tanggal_kembali", "<=", $returnDate);
            })->where("kembali", 0)->get();
        
        foreach($Peminjaman as $peminjaman){
            $peminjamanQuery = $peminjaman->detailPinjam->where("id_inventaris", "=", $inventarisID);
            if($peminjamanQuery->count() > 0){
                $peminjamanJumlah = $peminjamanQuery->sum("jumlah");
                $stock -= $peminjamanJumlah;
            }
        }

        return $stock;
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            "inventaris_id" => "required|array",
            "amount" => "required|array",
            "takeaway_date" => "required",
            "return_date" => "required",
        ]);
        // $currentUser = Auth::user();
        // if($currentUser->id_level != 1){
        //     return response()->json([
        //         'message' => "Akses terhadap resource ditolak!"
        //     ], 403);
        // }
        $errors = [];
        $takeawayDate = $request->input('takeaway_date');
        $returnDate = $request->input('return_date');
        $inventarisIDs = $request->input('inventaris_id');
        $takeawayAmounts = $request->input('amount');

        $i = 0;
        foreach($inventarisIDs as $inventarisID){
            $stock = $this->calculateRemaining($inventarisID, $takeawayDate, $returnDate);
            if($stock < $takeawayAmounts[$i]){
                $itemName = Inventaris::find($inventarisID)->nama;
                array_push($errors, "Stok barang $itemName mengalami perubahan dan tidak mencukupi ketika hendak dipinjam");
            }
            $i++;
        }

        if(sizeof($errors) > 0){
            return back()->with('submitError', $errors);
        }

        $peminjaman = new Peminjaman();
        $peminjaman->tanggal_pinjam = $takeawayDate;
        $peminjaman->tanggal_kembali = $returnDate;
        $peminjaman->id_pegawai = Auth::id();
        $peminjaman->save();

        $i = 0;
        foreach($inventarisIDs as $inventarisID){
            $detail = new DetailPinjam;
            $detail->id_inventaris = $inventarisID;
            $detail->jumlah = $takeawayAmounts[$i];
            $detail->id_peminjaman = $peminjaman->id_peminjaman;
            $detail->save();
            $i++;
        }

        return redirect("peminjaman");
    }

    public function get(Request $request)
    {
        $search = $request->search;
        $totalData = Peminjaman::all()->count();

        $peminjamanQuery = Peminjaman::orderBy('tanggal_pinjam')->orderBy('tanggal_kembali')->orderBy("kembali");
        if($search['value'] != null){
            $peminjamanQuery = $peminjamanQuery->where('tanggal_pinjam', '=', $search['value'])->orWhere('tanggal_kembali', '=', $search['value']);
        }
        $peminjamanFilteredCount = $peminjamanQuery->count();
        $Peminjaman = $peminjamanQuery->offset($request->start)->limit($request->length)->get();
        $responseJSON = [
            'draw' => $request->draw,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $peminjamanFilteredCount,
            'data' => []
        ];
        $i = 0;
        foreach($Peminjaman as $peminjaman){
            $today = new \DateTime();
            $takeawayDate = new \DateTime($peminjaman->tanggal_pinjam);
            $returnDate = new \DateTime($peminjaman->tanggal_kembali);
            if($takeawayDate == $returnDate){
                $returnDate->modify("+1 day");
            }
            $status = "Pending";
            if($peminjaman->kembali == 1){
                $status = "Dikembalikan";
            } else if($today >= $takeawayDate && $today <= $returnDate && $peminjaman->kembali == 0){
                $status = "Dipinjam";
            } else if($today >= $returnDate && $peminjaman->kembali == 0){
                $status = "Belum Dikembalikan (TERLAMBAT)";
            }
            array_push($responseJSON['data'], [
                $peminjaman->id_peminjaman,
                $peminjaman->id_pegawai,
                $request->start + ++$i,
                $peminjaman->pegawai->nama_pegawai ?? "Tidak diketahui",
                $takeawayDate->format("d F Y"),
                $returnDate->format("d F Y"),
                $status,
                '<button class="btn btn-primary btn-block return-btn" '.($peminjaman->kembali == 1 ? "disabled" : "").' data-id="'.$peminjaman->id_peminjaman.'">Barang kembali</button>'
            ]);
        }

        return response()->json($responseJSON);
    }

    public function apiAdd(Request $request)
    {
        $validated = $request->validate([
            "inventaris_id" => "required|array",
            "amount" => "required|array",
            "takeaway_date" => "required",
            "return_date" => "required",
        ]);
        // $currentUser = Auth::user();
        // if($currentUser->id_level != 1){
        //     return response()->json([
        //         'message' => "Akses terhadap resource ditolak!"
        //     ], 403);
        // }
        $errors = [];
        $takeawayDate = $request->input('takeaway_date');
        $returnDate = $request->input('return_date');
        $inventarisIDs = $request->input('inventaris_id');
        $takeawayAmounts = $request->input('amount');

        $i = 0;
        foreach($inventarisIDs as $inventarisID){
            $stock = $this->calculateRemaining($inventarisID, $takeawayDate, $returnDate);
            if($stock < $takeawayAmounts[$i]){
                $itemName = Inventaris::find($inventarisID)->nama;
                array_push($errors, "Stok barang $itemName mengalami perubahan dan tidak mencukupi ketika hendak dipinjam");
            }
            $i++;
        }

        if(sizeof($errors) > 0){
            return response()->json([
                "error" => $errors
            ], 400);
        }

        $peminjaman = new Peminjaman();
        $peminjaman->tanggal_pinjam = $takeawayDate;
        $peminjaman->tanggal_kembali = $returnDate;
        $peminjaman->id_pegawai = Auth::id();
        $peminjaman->save();

        $i = 0;
        foreach($inventarisIDs as $inventarisID){
            if($takeawayAmounts[$i] <= 0)
                continue;   

            $detail = new DetailPinjam;
            $detail->id_inventaris = $inventarisID;
            $detail->jumlah = $takeawayAmounts[$i];
            $detail->id_peminjaman = $peminjaman->id_peminjaman;
            $detail->save();
            $i++;
        }

        return response()->json([
            "message" => "Data telah ditambahkan"
        ], 201);
    }

    public function apiGet(Request $request)
    {
        $search = $request->search;
        $totalData = Peminjaman::all()->count();

        $peminjamanQuery = Peminjaman::orderBy('tanggal_pinjam')->orderBy('tanggal_kembali')->orderBy("kembali");

        if($request->user()->id_level == 3){
            $peminjamanQuery = $peminjamanQuery->where("id_pegawai", $request->user()->id);
        }
        if($search['value'] != null){
            $peminjamanQuery = $peminjamanQuery->where('tanggal_pinjam', '=', $search['value'])->orWhere('tanggal_kembali', '=', $search['value']);
        }
        $peminjamanFilteredCount = $peminjamanQuery->count();
        $Peminjaman = $peminjamanQuery->offset($request->offset ?? 0)->limit($request->limit ?? 100)->get();
        $responseJSON = [
            'data' => [],
            'additional_data' => [
                'recordsTotal' => $totalData,
                'recordsFiltered' => $peminjamanFilteredCount,
            ]
        ];
        $i = 0;
        foreach($Peminjaman as $peminjaman){
            $today = new \DateTime();
            $takeawayDate = new \DateTime($peminjaman->tanggal_pinjam);
            $returnDate = new \DateTime($peminjaman->tanggal_kembali);
            $status = "Pending";
            if($peminjaman->kembali == 1){
                $status = "Dikembalikan";
            } else if($today >= $takeawayDate && $today <= $returnDate && $peminjaman->kembali == 0){
                $status = "Dipinjam";
            } else if($today >= $returnDate && $peminjaman->kembali == 0){
                $status = "Belum Dikembalikan (TERLAMBAT)";
            }

            array_push($responseJSON['data'], [
                "id_peminjaman" => $peminjaman->id_peminjaman,
                "no" => $request->start + ++$i,
                "nama_pegawai" => $peminjaman->pegawai->nama_pegawai ?? "Tidak diketahui",
                "tanggal_pinjam" => $takeawayDate->getTimestamp(),
                "tanggal_kembali" => $returnDate->getTimestamp(),
                "status" => $status,
                "kembali" => $peminjaman->kembali == 1
            ]);
        }

        return response()->json($responseJSON);
    }
    public function apiDetail(Request $request)
    {
        $peminjaman = Peminjaman::find($request->id);
        $today = new \DateTime();
        $takeawayDate = new \DateTime($peminjaman->tanggal_pinjam);
        $returnDate = new \DateTime($peminjaman->tanggal_kembali);

        $status = "Pending";
            if($peminjaman->kembali == 1){
                $status = "Dikembalikan";
            } else if($today >= $takeawayDate && $today <= $returnDate && $peminjaman->kembali == 0){
                $status = "Dipinjam";
            } else if($today >= $returnDate && $peminjaman->kembali == 0){
                $status = "Belum Dikembalikan (TERLAMBAT)";
            }

        $peminjamanJSON = [
            "id_peminjaman" => $peminjaman->id_peminjaman,
            "nama_pegawai" => $peminjaman->pegawai->nama_pegawai ?? "Tidak diketahui",
            "tanggal_pinjam" => $takeawayDate->getTimestamp(),
            "tanggal_kembali" => $returnDate->getTimestamp(),
            "status" => $status,
            "kembali" => $peminjaman->kembali == 1,
            "peminjaman_details" => []
        ];

        $peminjamanDetails = $peminjaman->detailPinjam;
        foreach ($peminjamanDetails as $peminjamanDetail) {
            $dateTime =  new \DateTime($peminjamanDetail->inventaris->tanggal_register);
            $peminjamanDetailArr = [
                "id_inventaris" => $peminjamanDetail->id_inventaris,
                'id_jenis' => $peminjamanDetail->inventaris->id_jenis,
                'id_ruang' => $peminjamanDetail->inventaris->id_ruang,
                'kode_inventaris' => $peminjamanDetail->inventaris->kode_inventaris,
                'nama' => $peminjamanDetail->inventaris->nama,
                'kondisi' => $peminjamanDetail->inventaris->kondisi,
                'stok' => $peminjamanDetail->inventaris->jumlah,
                'url_gambar' => $peminjamanDetail->inventaris->url_photo ? asset("uploads/".$peminjamanDetail->inventaris->url_photo) : asset("image-404.jpg"),
                'kode_jenis' => $peminjamanDetail->inventaris->jenis->kode_jenis,
                'nama_jenis' => $peminjamanDetail->inventaris->jenis->nama_jenis,
                'kode_ruang' => $peminjamanDetail->inventaris->ruang->kode_ruang,
                'nama_ruang' => $peminjamanDetail->inventaris->ruang->nama_ruang,
                'kode_petugas' => $peminjamanDetail->inventaris->petugas->nama_petugas,
                'tanggal_register' => $dateTime->format('d F Y'),
                'keterangan' => $peminjamanDetail->inventaris->keterangan,
                'jumlah' => $peminjamanDetail->jumlah
            ];

            array_push($peminjamanJSON["peminjaman_details"], $peminjamanDetailArr);
        }

        return response()->json($peminjamanJSON);
    }
}

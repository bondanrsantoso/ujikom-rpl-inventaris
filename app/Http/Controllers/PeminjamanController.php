<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Inventaris;
use App\Jenis;
use App\Ruang;
use App\DetailPinjam;
use App\Peminjaman;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.peminjaman.filing');
    }

    public function checkAvailableStock(Request $request)
    {
        $inventarisID = $request->input('id_inventaris');
        $takeawayDate = $request->input('takeaway_date');
        $returnDate = $request->input('return_date');
        
        $stock = $this->calculateRemaining($inventarisID, $takeawayDate, $returnDate);

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
        $totalData = Ruang::all()->count();

        $ruangQuery = Ruang::orderBy('id_ruang');
        if($search['value'] != null){
            $ruangQuery = $ruangQuery->where('nama_ruang', 'like', '%'.$search['value'].'%')->orWhere('kode_ruang', 'like', $search['value']);
        }
        $ruangFilteredCount = $ruangQuery->count();
        $ruangan = $ruangQuery->offset($request->start)->limit($request->length)->get();
        $responseJSON = [
            'draw' => $request->draw,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $ruangFilteredCount,
            'data' => []
        ];
        $i = 0;
        foreach($ruangan as $ruang){
            array_push($responseJSON['data'], [
                $ruang->id_ruang,
                $request->start + ++$i,
                $ruang->kode_ruang,
                $ruang->nama_ruang,
                $ruang->keterangan
            ]);
        }

        return response()->json($responseJSON);
    }
}

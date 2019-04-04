<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Lib\UserTokenManager;

use App\Inventaris;
use App\Jenis;
use App\Ruang;

use App\Http\Controllers\PeminjamanController;

class InventarisController extends Controller
{
    public function index(Request $request )
    {
        $currentUser = Auth::user();
        if($currentUser->id_level != 1){
            return redirect("home");
        }

        $Jenis = Jenis::all();
        $Ruang = Ruang::all();

        return view('pages.inventaris.index')->with('user', $currentUser)->with('token', UserTokenManager::generateToken($currentUser))->with('Jenis', $Jenis)->with('Ruang', $Ruang);
    }

    public function generateKode(Request $request)
    {
        if(Jenis::all()->count() == 0 || Ruang::all()->count() == 0){
            return response()->json([
                "id" => ""
            ]);
        }
        $lengthOfCode = 4;
        $jenis = Jenis::find($request->jenis);
        $ruang = Ruang::find($request->ruang);
        if($request->has("id")){
            $inv = Inventaris::find($request->id);
            if($jenis->id_jenis == $inv->id_jenis && $ruang->id_ruang == $inv->id_ruang){
                return response()->json([
                    "id" => $inv->kode_inventaris
                ]);
            }
        }

        $id = Inventaris::where("id_jenis", $request->jenis)
                ->where("id_ruang", $request->ruang)->count();
        $id++;
        $id =(string) $id;
        for($i = \sizeof($id); $i < $lengthOfCode; $i++){
            $id = "0".$id;
        }
        
        return response()->json([
            "id" => $ruang->kode_ruang."-".$jenis->kode_jenis."-".$id
        ]);
    }

    public function add(Request $request)
    {
        $currentUser = Auth::user();
        if($currentUser->id_level != 1){
            return response()->json([
                'message' => "Akses terhadap resource ditolak!"
            ], 403);
        }
    
        $inventaris = new Inventaris();
        if($request->input('id') != 'new'){
            $inventaris = Inventaris::find($request->input('id'));
        }
        $inventaris->nama = $request->input("nama");
        $inventaris->kondisi = $request->input("kondisi");
        $inventaris->id_jenis = $request->input("jenis");
        $inventaris->id_ruang = $request->input("ruang");
        $inventaris->kode_inventaris = $request->input("kode");
        $inventaris->keterangan = $request->input("keterangan");
        $inventaris->kondisi = $request->input("kondisi");
        $inventaris->jumlah = $request->input("jumlah");

        if($request->input('id') == 'new'){
            $inventaris->tanggal_register = date("Y-m-d");
            $inventaris->id_petugas = Auth::id();
        }
        if($request->hasFile("photo")){
            if($request->file("photo")->isValid()){
                $newFileName = Str::random(16).".".$request->file("photo")->extension();
                $request->file("photo")->move(public_path("/uploads"), $newFileName);
                $inventaris->url_photo = $newFileName;
            }
        }
        $inventaris->save();

        return response()->json([
            'message' => 'Data Inventaris baru telah dibuat'
        ], 201);
    }

    public function get(Request $request)
    {
        $search = $request->search;
        $totalData = Inventaris::all()->count();

        $inventarisQuery = Inventaris::orderBy('id_inventaris');
        if($search['value'] != null){
            $inventarisQuery = $inventarisQuery->where('nama', 'like', '%'.$search['value'].'%')->orWhere('kode_inventaris', 'like', '%'.$search['value'].'%');
        }
        $inventarisFilteredCount = $inventarisQuery->count();
        $Inventaris = $inventarisQuery->offset($request->start)->limit($request->length)->get();
        $responseJSON = [
            'draw' => $request->draw,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $inventarisFilteredCount,
            'data' => []
        ];
        $i = 0;
        foreach($Inventaris as $inventaris){
            $dateTime =  new \DateTime($inventaris->tanggal_register);
            array_push($responseJSON['data'], [
                $inventaris->id_inventaris,
                $inventaris->id_jenis,
                $inventaris->id_ruang,
                $request->start + ++$i,
                $inventaris->kode_inventaris,
                $inventaris->nama,
                $inventaris->kondisi,
                $inventaris->jumlah,
                $inventaris->jenis->kode_jenis,
                $inventaris->ruang->kode_ruang,
                $inventaris->petugas->nama_petugas,
                $dateTime->format('d F Y'),
                $inventaris->keterangan,
                $inventaris->url_photo ? asset("uploads/".$inventaris->url_photo) : "NULL"
            ]);
        }

        return response()->json($responseJSON);
    }

    public function delete(Request $request)
    {
        $currentUser = Auth::user();
        if($currentUser->id_level != 1){
            return response()->json([
                'message' => "Akses terhadap resource ditolak!"
            ], 403);
        }
        $inventaris = Inventaris::find($request->input('id'));
        $inventaris->delete();

        return response()->json([
            'message' => 'Data Inventaris telah dihapus'
        ], 200);
    }

    public function apiGet(Request $request)
    {
        $search = $request->q;
        $totalData = Inventaris::all()->count();
        $peminjamanController = new PeminjamanController;

        $inventarisQuery = Inventaris::orderBy('id_inventaris');
        if($search != null){
            $inventarisQuery = $inventarisQuery->where('nama', 'like', '%'.$search.'%')->orWhere('kode_inventaris', 'like', '%'.$search.'%');
        }
        $startDate = null;
        $endDate = null;
        if($request->has("start_date") && $request->has("end_date")){
            $startDate = $request->start_date;
            $endDate = $request->end_date;
        }
        if($request->has("only")){
            $onlyID = explode(",", $request->only);
            $inventarisQuery->whereIn('id_inventaris', $onlyID);
        }
        $inventarisFilteredCount = $inventarisQuery->count();
        $Inventaris = $inventarisQuery->offset($request->offset ?? 0)->limit($request->limit ?? 10)->get();
        $responseJSON = [
            'data' => [],
            'additional_data' => [
                'total_rows' => $totalData,
                'total_rows_filtered' => $inventarisFilteredCount
            ]
        ];
        $i = 0;
        foreach($Inventaris as $inventaris){
            $dateTime =  new \DateTime($inventaris->tanggal_register);
            $data = [
                'id_inventaris' => $inventaris->id_inventaris,
                'id_jenis' => $inventaris->id_jenis,
                'id_ruang' => $inventaris->id_ruang,
                'kode_inventaris' => $inventaris->kode_inventaris,
                'nama' => $inventaris->nama,
                'kondisi' => $inventaris->kondisi,
                'jumlah' => $inventaris->jumlah,
                'url_gambar' => $inventaris->url_photo ? asset("uploads/".$inventaris->url_photo) : asset("image-404.jpg"),
                'kode_jenis' => $inventaris->jenis->kode_jenis,
                'nama_jenis' => $inventaris->jenis->nama_jenis,
                'kode_ruang' => $inventaris->ruang->kode_ruang,
                'nama_ruang' => $inventaris->ruang->nama_ruang,
                'kode_petugas' => $inventaris->petugas->nama_petugas,
                'tanggal_register' => $dateTime->format('d F Y'),
                'keterangan' => $inventaris->keterangan
            ];
            if($startDate != null && $endDate != null){
                $data['stok'] = $data["jumlah"];
            }

            array_push($responseJSON['data'], $data);
        }

        return response()->json($responseJSON);
    }
}

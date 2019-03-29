<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Lib\UserTokenManager;

use App\Inventaris;
use App\Jenis;
use App\Ruang;

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

        return view('pages.inventaris.index')->with('token', UserTokenManager::generateToken($currentUser))->with('Jenis', $Jenis)->with('Ruang', $Ruang);
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
                $inventaris->keterangan
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

        $inventarisQuery = Inventaris::orderBy('id_inventaris');
        if($search != null){
            $inventarisQuery = $inventarisQuery->where('nama', 'like', '%'.$search.'%')->orWhere('kode_inventaris', 'like', '%'.$search.'%');
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
            array_push($responseJSON['data'], [
                'id_inventaris' => $inventaris->id_inventaris,
                'id_jenis' => $inventaris->id_jenis,
                'id_ruang' => $inventaris->id_ruang,
                'kode_inventaris' => $inventaris->kode_inventaris,
                'nama' => $inventaris->nama,
                'kondisi' => $inventaris->kondisi,
                'jumlah' => $inventaris->jumlah,
                'url_gambar' => "https://images.pexels.com/photos/1166420/pexels-photo-1166420.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=650&w=940",
                'kode_jenis' => $inventaris->jenis->kode_jenis,
                'nama_jenis' => $inventaris->jenis->nama_jenis,
                'kode_ruang' => $inventaris->ruang->kode_ruang,
                'nama_ruang' => $inventaris->ruang->nama_ruang,
                'kode_petugas' => $inventaris->petugas->nama_petugas,
                'tanggal_register' => $dateTime->format('d F Y'),
                'keterangan' => $inventaris->keterangan
            ]);
        }

        return response()->json($responseJSON);
    }
}

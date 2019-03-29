<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Lib\UserTokenManager;

use App\Ruang;

class RuangController extends Controller
{
    public function __construct()
    {
        // $currentUser = Auth::user();
        // if($currentUser->id_level != 1){
        //     return redirect("home");
        // }
    }
    
    public function index(Request $request )
    {
        $currentUser = Auth::user();
        if($currentUser->id_level != 1){
            return redirect("home");
        }

        return view('pages.ruang.index')->with('token', UserTokenManager::generateToken($currentUser));
    }

    public function add(Request $request)
    {
        $currentUser = Auth::user();
        if($currentUser->id_level != 1){
            return response()->json([
                'message' => "Tidak semudah itu, Ferguso!"
            ], 403);
        }
    
        $ruang = new Ruang();
        if($request->input('id') != 'new'){
            $ruang = Ruang::find($request->input('id'));
        }
        $ruang->kode_ruang = $request->input("kode");
        $ruang->nama_ruang = $request->input("nama");
        $ruang->keterangan = $request->input("keterangan");
        $ruang->save();

        return response()->json([
            'message' => 'Data ruangan baru telah dibuat'
        ], 201);
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

    public function delete(Request $request)
    {
        $currentUser = Auth::user();
        if($currentUser->id_level != 1){
            return response()->json([
                'message' => "Tidak semudah itu, Ferguso!"
            ], 403);
        }
        $ruang = Ruang::find($request->input('id'));
        $ruang->delete();

        return response()->json([
            'message' => 'Data ruangan telah dihapus'
        ], 200);
    }

    // API Controllers

    public function apiGet(Request $request)
    {
        $search = $request->q ?? "";
        $totalData = Ruang::all()->count();

        $ruangQuery = Ruang::orderBy('id_ruang');
        if($search != null){
            $ruangQuery = $ruangQuery->where('nama_ruang', 'like', '%'.$search.'%')->orWhere('kode_ruang', 'like', '%'.$search.'%');
        }
        $ruangFilteredCount = $ruangQuery->count();
        $offset = $request->offset ?? 0;
        $limit = $request->length ?? 10;
        $Ruang = $ruangQuery->offset($offset)->limit($limit)->get();
        $responseJSON = [
            'data' => [],
            'additional_data' => [
                'total_rows' => $totalData,
                'total_rows_filtered' => $ruangFilteredCount
            ]
        ];
        $i = 0;
        foreach($Ruang as $ruang){
            array_push($responseJSON['data'], [
                'id_ruang' => $ruang->id_ruang,
                'no' => $request->start + ++$i,
                'kode_ruang' => $ruang->kode_ruang,
                'nama' => $ruang->nama_ruang,
                'keterangan' => $ruang->keterangan
            ]);
        }

        return response()->json($responseJSON);
    }
}

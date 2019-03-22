<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Lib\UserTokenManager;

use App\Jenis;

class JenisController extends Controller
{
    public function index(Request $request )
    {
        $currentUser = Auth::user();
        if($currentUser->id_level != 1){
            return redirect("home");
        }

        return view('pages.jenis.index')->with('token', UserTokenManager::generateToken($currentUser));
    }

    public function add(Request $request)
    {
        $currentUser = Auth::user();
        if($currentUser->id_level != 1){
            return response()->json([
                'message' => "Akses terhadap resource ditolak!"
            ], 403);
        }
    
        $jenis = new Jenis();
        if($request->input('id') != 'new'){
            $jenis = Jenis::find($request->input('id'));
        }
        $jenis->kode_jenis = $request->input("kode");
        $jenis->nama_jenis = $request->input("nama");
        $jenis->keterangan = $request->input("keterangan");
        $jenis->save();

        return response()->json([
            'message' => 'Data Jenis baru telah dibuat'
        ], 201);
    }

    public function get(Request $request)
    {
        $search = $request->search;
        $totalData = Jenis::all()->count();

        $jenisQuery = Jenis::orderBy('id_jenis');
        if($search['value'] != null){
            $jenisQuery = $jenisQuery->where('nama_jenis', 'like', '%'.$search['value'].'%')->orWhere('kode_jenis', 'like', '%'.$search['value'].'%');
        }
        $jenisFilteredCount = $jenisQuery->count();
        $Jenis = $jenisQuery->offset($request->start)->limit($request->length)->get();
        $responseJSON = [
            'draw' => $request->draw,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $jenisFilteredCount,
            'data' => []
        ];
        $i = 0;
        foreach($Jenis as $jenis){
            array_push($responseJSON['data'], [
                $jenis->id_jenis,
                $request->start + ++$i,
                $jenis->kode_jenis,
                $jenis->nama_jenis,
                $jenis->keterangan
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
        $jenis = Jenis::find($request->input('id'));
        $jenis->delete();

        return response()->json([
            'message' => 'Data Jenis telah dihapus'
        ], 200);
    }
}

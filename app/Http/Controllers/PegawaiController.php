<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\User;
use App\Pegawai;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        if($currentUser->id_level != 1){
            return redirect("home");
        }

        return view('pages.pegawai.index')->with('token', UserTokenManager::generateToken($currentUser));
    }
    
    public function add(Request $request)
    {
        if($request->input("id") == "new"){
            $newUser = new User;
        } else{
            $newUser = User::find($request->input("id"));
        }
        $newUser->name = $request->input("nama");
        if($request->has("email")){
            $newUser->email = $request->input('email');
        }
        $newUser->username = $request->input("nip");
        $newUser->password = Hash::make($request->input("password"));
        $newUser->save();

        $newPegawai = new Pegawai;
        $newPegawai->id_pegawai = $newUser->id;
        $newPegawai->nama_pegawai = $newUser->name;
        $newPegawai->nip = $newUser->username;
        $newPegawai->alamat = $request->input("alamat");
        $newPegawai->save();

        return response()->json([
            "message" => "Data pegawai berhasil ditambahkan"
        ]);
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

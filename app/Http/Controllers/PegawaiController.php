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
        
    }
    
    public function add(Request $request)
    {
        $newUser = new User;
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
}

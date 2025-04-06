<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\users\roles;

class rolesController extends Controller
{
    public function index()
    {
        $roles = roles::orderBy('nombre_rol', 'asc')->get();
        
        return response()->json($roles);
          
    }

    public function search(Request $request)
    {

        $searchTerm = $request->search;

        $rolUser = roles::query()
                        ->where('nombre_rol', 'like','%'.$searchTerm.'%')
                        ->orderBy('nombre_rol', 'asc')
                        ->get();
        return response()->json($rolUser);
    }

    public function show($id = 0)
    {

        if($id <= 0){
            return response()->json([
                'error' => 'debe enviar el id del rol'
            ],404);
        }

        $rolUser = roles::find($id);
        if(is_null($rolUser)){
            return response()->json([
                'error' => 'No se pudo realizar correctamente con este id '.$id.''
            ], 404);
        }

        return response()->json($rolUser);
    }
}
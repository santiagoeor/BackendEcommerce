<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Validation\ValidationException;
use App\Models\trabajadores\trabajadores;
use App\Models\users\roles;

class authController extends Controller
{

    public function pruebas()
    {
        return response()->json([
            "ok" => "todo funciona"
        ]);
    }

    public function login(Request $request)
    {

        try {
            $request->validate([
                'email' => 'required|email|min:6',
                'password' => 'required|min:6'
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $user = auth()->user();
        $user_id = $user->id;
        $estado_user = $user->estado_user;
        $pictureUser = $user->picture_user;
        $rol_user = $user->fk_cargo;

        $trabajador = trabajadores::query()
            ->where('fk_user', '=', $user_id)
            ->first();
        // $idTrabajador = $trabajador->pk_trabajador;

        if ($trabajador) {
            $idTrabajador = $trabajador->pk_trabajador;
            $nombTrabajador = $trabajador->nombre_trab;
        } else {
            $idTrabajador = 0;
            $nombTrabajador = '';
        }



        $datosRoles = roles::find($rol_user);
        $nombreRol = $datosRoles->nombre_rol;
        $tipo_rol = $datosRoles->tipo;
        $horaActual = date('H:i:s');
        $fechaActual = date('Y-m-d');

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 720,
            'horaActual' => $horaActual,
            'fechaActual' => $fechaActual,
            'user_id' => $user_id,
            'id_trabajador' => $idTrabajador,
            'nombTrabajador' => $nombTrabajador,
            'estado_user' => $estado_user,
            'picture' => $pictureUser,
            'nombreRol' => $nombreRol,
            'rol' => $tipo_rol,
            'tipo_rol' => $tipo_rol
        ]);
    }
}
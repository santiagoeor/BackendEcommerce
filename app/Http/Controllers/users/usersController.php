<?php

namespace App\Http\Controllers\users;

use App\Traits\SavePdfImageTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use App\Models\users\Users;
use App\Models\users\roles;
use Illuminate\Support\Facades\Hash;

class usersController extends Controller
{
    use SavePdfImageTrait;

    private function validateRolUser($rol_user)
    {

        $tableRoles = roles::find($rol_user);
        if (is_null($tableRoles)) {
            return false;
        }

        $rol_userV = $tableRoles->tipo;

        return $rol_userV == 1 || $rol_userV == 2 ? true : false;
    }

    public function index()
    {
        $users = Users::with('roles')
            ->orderBy('email', 'asc')
            ->get();

        return response()->json($users);
    }

    public function search(Request $request)
    {
        $searchTerm = $request->search;

        $users = Users::query()
            ->with('roles')
            ->where('email', 'like', '%' . $searchTerm . '%')
            ->orderBy('email', 'asc')
            ->get();

        return response()->json($users);
    }

    public function validarUniqueUser(Request $request)
    {
        $email = $request->email;

        $users = Users::query()->where('email', '=', '' . $email . '')->get();

        return response()->json($users);
    }

    public function show($id = 0)
    {
        if ($id <= 0) {
            return response()->json([
                'error' => 'debe enviar el id del user'
            ], 404);
        }

        $users = Users::with('roles')->find($id);
        if (is_null($users)) {
            return response()->json([
                'error' => 'no se pudo realizar correctamente con este id ' . $id . ''
            ], 404);
        }

        return response()->json($users);
    }

    public function create(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|unique:users,email|min:6',
                'password' => 'required|min:6',
                'picture_user' => 'required|image|max:10240',
                'address' => 'required|min:10',
                'estado_user' => 'required|integer',
                'fk_cargo' => 'required|integer',
                'rol_user' => 'required|integer',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $rol_user = $request->input('rol_user');
        $rol = $this->validateRolUser($rol_user);

        if ($rol) {

            $url = 'storage/users';
            $image = $request->file('picture_user');
            $imageUrl = $this->savePdfImage($url, $image);

            $user = new Users();
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->picture_user = $imageUrl;
            $user->address = $request->input('address');
            $user->estado_user = $request->input('estado_user');
            $user->fk_cargo = $request->input('fk_cargo');
            $user->save();

            return response()->json([
                'ok' => 'Usuario creado'
            ], 201);
        } else {
            return response()->json([
                'error' => 'Access prohibited'
            ], 403);
        }
    }

    public function update(Request $request, $id = 0)
    {
        try {
            $request->validate([
                'picture_user' => 'nullable|image|max:10240',
                'estado_user' => 'required|integer',
                'fk_cargo' => 'required|integer',
                'rol_user' => 'required|integer',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
        $rol_user = $request->input('rol_user');
        $rol = $this->validateRolUser($rol_user);

        if ($rol) {
            if ($id <= 0) {
                return response()->json([
                    'error' => 'debe enviar el id del user'
                ], 404);
            }
            $user = Users::find($id);
            if (is_null($user)) {
                return response()->json([
                    'error' => 'no se pudo realizar correctamente con este id ' . $id . ''
                ], 404);
            }

            if ($request->hasFile('picture_user')) {
                $urlImagenDelete = $user->picture_user;
                $this->deleteImage($urlImagenDelete);
                $url = 'storage/users';
                $image = $request->file('picture_user');
                $imageUrl = $this->savePdfImage($url, $image);

                $user->picture_user = $imageUrl;
                $user->estado_user = $request->input('estado_user');
                $user->fk_cargo = $request->input('fk_cargo');
                $user->save();
                return response()->json([
                    'ok' => 'Usuario actualizado',
                    'url' => $imageUrl
                ], 201);
            } else {
                $user->estado_user = $request->input('estado_user');
                $user->fk_cargo = $request->input('fk_cargo');
                $user->save();
                return response()->json([
                    'ok' => 'Usuario actualizado'
                ], 201);
            }
        } else {
            return response()->json([
                'error' => 'Access prohibited'
            ], 403);
        }
    }

    public function updateCredentialsAcces(Request $request, $id = 0)
    {
        try {
            $request->validate([
                'email' => 'required|email|min:6|unique:users,email,' . $id,
                'password' => 'required|min:6',
                'rol_user' => 'required|integer'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $rol_user = $request->rol_user;
        $rol = $this->validateRolUser($rol_user);

        if ($rol) {

            if ($id <= 0) {
                return response()->json([
                    'error' => 'debe enviar el id del user'
                ], 404);
            }
            $user = Users::find($id);
            if (is_null($user)) {
                return response()->json([
                    'error' => 'no se pudo realizar correctamente con este id ' . $id . ''
                ], 404);
            }

            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json([
                'ok' => 'Credenciales actualizadas'
            ], 201);
        } else {
            return response()->json([
                'error' => 'Access prohibited'
            ], 403);
        }
    }

    public function updateEmailUser(Request $request, $id = 0)
    {
        try {
            $request->validate([
                'email' => 'required|email|min:6|unique:users,email,' . $id,
                'rol_user' => 'required|integer'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $rol_user = $request->rol_user;
        $rol = $this->validateRolUser($rol_user);

        if ($rol) {

            if ($id <= 0) {
                return response()->json([
                    'error' => 'debe enviar el id del user'
                ], 404);
            }
            $user = Users::find($id);
            if (is_null($user)) {
                return response()->json([
                    'error' => 'no se pudo realizar correctamente con este id ' . $id . ''
                ], 404);
            }

            $user->email = $request->email;
            $user->save();
            return response()->json([
                'ok' => 'Email actualizado'
            ], 201);
        } else {
            return response()->json([
                'error' => 'Access prohibited'
            ], 403);
        }
    }


    public function updatePassword(Request $request, $id = 0)
    {
        try {
            $request->validate([
                'password' => 'required|min:6',
                'rol_user' => 'required|integer'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $rol_user = $request->rol_user;
        $rol = $this->validateRolUser($rol_user);

        if ($rol) {

            if ($id <= 0) {
                return response()->json([
                    'error' => 'debe enviar el id del user'
                ], 404);
            }
            $user = Users::find($id);
            if (is_null($user)) {
                return response()->json([
                    'error' => 'no se pudo realizar correctamente con este id ' . $id . ''
                ], 404);
            }

            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json([
                'ok' => 'Password actualizadas'
            ], 201);
        } else {
            return response()->json([
                'error' => 'Access prohibited'
            ], 403);
        }
    }

    public function destroy(Request $request, int $id = 0)
    {
        try {
            $request->validate([
                'rol_user' => 'required|integer',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $rol_user = $request->rol_user;
        $rol = $this->validateRolUser($rol_user);

        if ($rol) {
            if ($id <= 0) {
                return response()->json([
                    'error' => 'debe enviar el id del user'
                ], 404);
            }

            $user = Users::find($id);
            if (is_null($user)) {
                return response()->json([
                    'error' => 'No se pudo realizar correctamente'
                ], 404);
            }

            $urlImagenDelete = $user->picture_user;
            $this->deleteImage($urlImagenDelete);

            $user->delete();
            return response()->json([
                'ok' => 'registro eliminado'
            ], 204);
        } else {
            return response()->json([
                'error' => 'Access prohibited'
            ], 403);
        }
    }
}
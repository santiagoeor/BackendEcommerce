<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class UserTest extends TestCase
{
    use RefreshDatabase; // Agrega la trait RefreshDatabase para realizar migraciones y deshacer cambios en la base de datos después de cada prueba.

    public function test_login()
    {
        Artisan::call('migrate');

        // Registro incorrecto
        $response = $this->postJson('v1/roles', [
            "tipo" => "sdf",
            "nombre_rol" => "gsdf"
        ]);

        $response->assertStatus(404); // Verifica que el código de estado de la respuesta sea 422 (Unprocessable Entity).
    }
}

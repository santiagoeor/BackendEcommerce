<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\authController;
use App\Http\Controllers\encuestas\personasController;
use App\Http\Controllers\encuestas\encuestasController;
use App\Http\Controllers\encuestas\preguntasController;
use App\Http\Controllers\encuestas\opcionesController;
use App\Http\Controllers\encuestas\respuestasController;

Route::post('v1/login', [authController::class, 'login']);
Route::get('v1/pruebas', [authController::class, 'pruebas']);

Route::get('v1/personas', [personasController::class, 'index']);
Route::post('v1/persona', [personasController::class, 'crearPersona']);
Route::delete('v1/persona/{id}', [personasController::class, 'deletePerson']);

Route::get('v1/encuestas', [encuestasController::class, 'index']);
Route::post('v1/encuesta', [encuestasController::class, 'crearEncuesta']);
Route::delete('v1/encuesta/{id}', [encuestasController::class, 'deleteEncuesta']);

Route::get('v1/preguntas', [preguntasController::class, 'index']);
Route::post('v1/pregunta', [preguntasController::class, 'crearPregunta']);
Route::delete('v1/pregunta/{id}', [preguntasController::class, 'deletePregunta']);

Route::get('v1/opciones', [opcionesController::class, 'index']);
Route::post('v1/opciones', [opcionesController::class, 'crearOpcionesPregunta']);
Route::post('v1/opcion', [opcionesController::class, 'crearOpcionPregunta']);
Route::delete('v1/opcion/{id}', [opcionesController::class, 'deleteOpcionPregunta']);

Route::get('v1/respuestas', [respuestasController::class, 'index']);
Route::post('v1/respuesta', [respuestasController::class, 'crearRespuesta']);
Route::delete('v1/respuesta/{id}', [respuestasController::class, 'deleteRespuesta']);


Route::group(['middleware' => ['jwt.verify']], function () {

    Route::post('v1/logout', [authController::class, 'logout']);

    // Route::prefix('v1')->group(function () {
    //     require __DIR__ . '/auditorias/api_auditorias.php';
    // });
});
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Importando os Controllers que iremos usar
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CoopController;

//Rota para testar a conexão com o Banco de Dados
Route::get('/ping', function() {
    return ['pong' => true];
});

//Rota de Login não autenticado
Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');

//Rota para gerar Cooperativas Aleatórias
/*Route::get('random', [CoopController::class, 'createRandom']); */

//Rotas de login
Route::post('/auth/login', [AuthController::class, 'login']); //Entrar
Route::post('/auth/logout', [AuthController::class, 'logout']); //Sair
Route::post('/auth/refresh', [AuthController::class, 'refresh']); //Atualizar o Token

//Rotas do Usuário
Route::post('/user', [AuthController::class, 'create']); //Criação de Usuário
Route::get('/user', [UserController::class, 'read']); //Lendo informações do Usuário
Route::put('/user', [UserController::class, 'update']); //Atualiza informações do perfil
Route::post('user/avatar', [UserController::class, 'updateAvatar']); //Atualiza o avatar
Route::get('user/favorites', [UserController::class, 'getFavorites']); //Pegando os favoritos
Route::post('user/favorite', [UserController::class, 'toggleFavorite']); //Adicionando favorito
Route::get('user/appointments', [UserController::class, 'getAppointments']); //Pegando os agendamentos do usuário

//Rotas da Cooperativa
Route::get('/coops', [CoopController::class, 'list']); //Lista com as Cooperativas
Route::get('/coop/{id}', [CoopController::class, 'one']); //Informações de uma Cooperativa específica
Route::post('/coop/{id}/appointment', [CoopController::class, 'setAppointment']); //Faz o agendamento

//Processo de Busca
Route::get('search', [CoopController::class, 'search']); //Buscar Cooperativas
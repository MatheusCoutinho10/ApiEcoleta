<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private $loggedUser;

    public function __construct(){
        $this->middleware('auth:api'); //Tem que estar logado para realizar o processo
        $this->loggedUser = auth()->user(); //Informações do usuário que está logado
    }


    //Método que pega as informações do usuário
    public function read(){  
        $array = ['error' => ''];

        $info = $this->loggedUser; //Pega as informações
        $info['avatar'] = url('media/avatars/'.$info['avatar']); //Corrigindo
        $array['data'] = $info; //Mandando pro data

        return $array;
    }
}
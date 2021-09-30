<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\UserFavorite;
use App\Models\Coop;

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

    //Função para favoritar uma Cooperativa
    public function toggleFavorite(Request $request){
        $array = ['error'=>''];

        $id_coop = $request->input('coop'); //Pegando a Cooperativa

        //Verificando se a Cooperativa existe
        $coop = Coop::find($id_coop);

        //Se ele pegou uma Cooperativa
        if($coop){
            //Verificar se existe
            $fav = UserFavorite::select()
                                  ->where('id_user', $this->loggedUser->id)
                                  ->where('id_coop', $id_coop)
                                  ->first();
            
            //Verificando se tem algum registro
            if($fav){
                //Remover
                $fav->delete();
                $array['have'] = false;
            }else{
                //Adicionar
                $newFav = new UserFavorite();
                $newFav->id_user = $this->loggedUser->id;
                $newFav->id_coop = $id_coop;
                $newFav->save();
                $array['have'] = true;
            }
        }else{
            $array['error'] = 'Cooperativa inexistente!';
        }

        return $array;
    }
}
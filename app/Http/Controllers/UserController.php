<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator; //Importando o validador

use Intervention\Image\Facades\Image; //Importando a biblioteca usada para atualizar os avatares

use App\Models\User;
use App\Models\UserAppointment;
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

    //Função para listar os favoritos
    public function getFavorites(){
        $array = ['error'=>'','list'=>[]];

        //Pegando a lista
        $favs = UserFavorite::select()
                            ->where('id_user', $this->loggedUser->id)
                            ->get();

        //Verificando se achou algum
        if($favs){
            //Looping para pegas as informações de cada uma das cooperativas
            foreach($favs as $fav){
                $coop = Coop::find($fav['id_coop']); //Pegando os dados
                $coop['avatar'] = url('media/avatars/'.$coop['avatar']); //Corrigindo o avatar
                $array['list'][] = $coop; //Adicionando ao array
            }
        }

        return $array;
    }

    //Função para listar os agendamentos do usuário
    public function getAppointments(){
        $array = ['error'=>'','list'=>[]];

        //Pegando os agendamentos do usuário
        $apps = UserAppointment::select()
                               ->where('id_user', $this->loggedUser->id)
                               ->orderBy('ap_datetime', 'DESC')
                               ->get();

        //Se ele achou algo
        if($apps){
            //Loop para pegar as informações da Cooperativa
            foreach($apps as $app){
                $coop = Coop::find($app['id_coop']); //Pegando a cooperativa
                $coop['avatar'] = url('media/avatars/'.$coop['avatar']); // Arrumando o avatar

                //Adicionando os dados a lista
                $array['list'][] = [
                    'id' => $app['id'],
                    'datetime' => $app['ap_datetime'],
                    'coop' => $coop
                ];
            }
        }

        return $array;
    }

    //Função para atualizar informações de usuário
    public function update(Request $request){
        $array = ['error'=>''];

        //Criando as regra para validar os dados
        $rules = [
            'name' => 'min:2',
            'email' => 'email|unique:users',
            'password' => 'same:password_confirm',
            'password_confirm' => 'same:password'
        ];

        $validator = Validator::make($request->all(), $rules); //Validando os dados

        //Verificando se falhou
        if($validator->fails()){
            $array['error'] = $validator->messages();
            return $array;
        }

        //Se deu certo
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $password_confirm = $request->input('password_confirm');

        //Atualizando os campos no banco
        $user = User::find($this->loggedUser->id); //Selecionando o usuário logado

        //Alterando somente o que o usuário enviar
        if($name){
            $user->name = $name;
        }

        if($email){
            $user->email = $email;
        }

        if($password){
            $user->password = password_hash($password, PASSWORD_DEFAULT);
        }

        $user->save(); //Salvando

        return $array;
    }

    //Função para atualizar o avatar do usuário
    public function updateAvatar(Request $request){
        $array = ['error'=>''];

        //Regras das imagens
        $rules = [
            'avatar' => 'required|image|mimes:png,jpg,jpeg'
        ];

        $validator = Validator::make($request->all(), $rules); //Aplicando as regras

        //Verificando se deu erro
        if($validator->fails()){
            $array['error'] = $validator->messages();
            return $array;
        }

        //Se não deu erro, pego o arquivo
        $avatar = $request->file('avatar');

        $dest = public_path('/media/avatars'); //Pasta de destino
        $avatarName = md5(time().rand(0,9999)).'.jpg'; //Gerando um nome

        //Mexendo na imagem
        $img = Image::make($avatar->getRealPath()); //Pegando o arquivo para classe Image
        $img->fit(300, 300)->save($dest.'/'.$avatarName); //Mudando o tamanho da imagem e salvando no destino

        //Salvando a imagem no usuário
        $user = User::find($this->loggedUser->id);
        $user->avatar = $avatarName;
        $user->save();

        return $array;
    }
}
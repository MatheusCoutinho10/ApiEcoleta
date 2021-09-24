<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Coop;
use App\Models\CoopPhotos;
use App\Models\CoopTestimonial;
use App\Models\CoopAvailability;

class CoopController extends Controller
{
    private $loggedUser;

    public function __construct(){
        $this->middleware('auth:api'); //Tem que estar logado para realizar o processo
        $this->loggedUser = auth()->user(); //Informações do usuário que está logado
    }

    public function createRandom(){
        $array = ['error'=>''];

        //Loop para criar 15 cooperativas
        for($q=0; $q<15; $q++) {
            $names = ['Boniek', 'Paulo', 'Pedro', 'Amanda', 'Leticia', 'Gabriel', 'Gabriela', 'Thais', 'Luiz', 'Diogo', 'José', 'Jeremias', 'Francisco', 'Dirce', 'Marcelo' ];
            $lastnames = ['Santos', 'Silva', 'Santos', 'Silva', 'Alvaro', 'Sousa', 'Diniz', 'Josefa', 'Luiz', 'Diogo', 'Limoeiro', 'Santos', 'Limiro', 'Nazare', 'Mimoza' ];
            
            $depos = [
                'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate consequatur tenetur facere voluptatibus iusto accusantium vero sunt, itaque nisi esse ad temporibus a rerum aperiam cum quaerat quae quasi unde.',
                'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate consequatur tenetur facere voluptatibus iusto accusantium vero sunt, itaque nisi esse ad temporibus a rerum aperiam cum quaerat quae quasi unde.',
                'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate consequatur tenetur facere voluptatibus iusto accusantium vero sunt, itaque nisi esse ad temporibus a rerum aperiam cum quaerat quae quasi unde.',
                'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate consequatur tenetur facere voluptatibus iusto accusantium vero sunt, itaque nisi esse ad temporibus a rerum aperiam cum quaerat quae quasi unde.',
                'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate consequatur tenetur facere voluptatibus iusto accusantium vero sunt, itaque nisi esse ad temporibus a rerum aperiam cum quaerat quae quasi unde.'
            ];

            $newCoop = new Coop(); //Iniciou um novo barbeiro
            $newCoop->name = $names[rand(0, count($names)-1)].' '.$lastnames[rand(0, count($lastnames)-1)]; //Pegando nome e sobrenome aleatório
            $newCoop->avatar = rand(1, 4).'.png'; //Pegando avatar de 1 ao 4 aleatório
            $newCoop->stars = rand(2, 4).'.'.rand(0, 9); //Nota aleatória entre 2 e 4.9
            $newCoop->latitude = '-23.5'.rand(0, 9).'30907'; //Pegando latitude em SP
            $newCoop->longitude = '-46.6'.rand(0,9).'82759'; //Pegando longitude em SP
            $newCoop->save();

            //Gerando fotos aleatórias
            for($w=0;$w<4;$w++) {
                $newCoopPhoto = new CoopPhotos();
                $newCoopPhoto->id_coop = $newCoop->id; //Pegando o id
                $newCoopPhoto->url = rand(1, 5).'.png';
                $newCoopPhoto->save();
            }

            //Gerando depoimentos aleatórios
            for($w=0;$w<3;$w++) {
                $newCoopTestimonial = new CoopTestimonial();
                $newCoopTestimonial->id_coop = $newCoop->id;
                $newCoopTestimonial->name = $names[rand(0, count($names)-1)]; //Nome aleatório
                $newCoopTestimonial->rate = rand(2, 4).'.'.rand(0, 9); //Nota aleatória
                $newCoopTestimonial->body = $depos[rand(0, count($depos)-1)]; //Depoimento aleatório
                $newCoopTestimonial->save();
            }

            //Gerando disponibilidade distintas de horários distintos para a Cooperativa
            for($e=0;$e<4;$e++){
                $rAdd = rand(7, 10);
                $hours = [];

                //Pegando horas aleatórias
                for($r=0;$r<8;$r++) {
                    $time = $r + $rAdd;

                    if($time < 10) {
                        $time = '0'.$time;
                    }

                    $hours[] = $time.':00';
                }

                $newCoopAvail = new CoopAvailability();
                $newCoopAvail->id_coop = $newCoop->id;
                $newCoopAvail->weekday = $e;
                $newCoopAvail->hours = implode(',', $hours);
                $newCoopAvail->save();
            }
        }

        return $array;
    }
}
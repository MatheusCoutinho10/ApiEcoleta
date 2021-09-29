<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\UserAppointment;
use App\Models\UserFavorite;
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

    /*
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
    */

    //Função para localização
    private function searchGeo($address){
        //Pegando a chave
        $key = env('MAPS_KEY', null);

        $address = urlencode($address);

        //Fazendo a requisição
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$address.'&key='.$key;

        //Usando a Curl para iniciar a requisição
        $ch = curl_init(); // Iniciando a conexão
        curl_setopt($ch, CURLOPT_URL, $url); //URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Recebendo a resposta
        $res = curl_exec($ch);
        curl_close($ch); //Fechando a conexão

        return json_decode($res, true); //Pegando a string em json
    }

    //Função que lista as Cooperativas
    public function list(Request $request){
        $array = ['error' => ''];

        //Recebendo os dados de localização
        $lat = $request->input('lat'); //Latitude
        $lng = $request->input('lng'); //Longitude
        $city = $request->input('city'); //Cidade
        $offset = $request->input('offset');

        //Se o usuário não mandou o offset
        if(!$offset){
            $offset = 0; //Não pula ninguém, começa do zero
        }

        //Se o usuário mandou o nome da cidade
        if(!empty($city)){
            $res = $this->searchGeo($city);

            //Se teve resultado
            if(count($res['results']) > 0){
                $lat = $res['results'][0]['geometry']['location']['lat'];
                $lng = $res['results'][0]['geometry']['location']['lng'];
            }
        }elseif(!empty($lat) && !empty($lng)){
            $res = $this->searchGeo($lat.','.$lng);

            //Verificando se teve resultado
            if(count($res['results']) > 0){
                $city = $res['results'][0]['formatted_address'];
            }
        }else{
            $lat = '-23.5630907';
            $lng = '-46.6682795';
            $city = 'São Paulo';
        }

        //Pegando as cooperativas proximas
        $coops = Coop::select(Coop::raw('*, SQRT(
            POW(69.1 * (latitude - '.$lat.'),2) +
            POW(69.1 * ('.$lng.' - longitude) * COS(latitude / 57.3), 2)) AS distance'))
            ->havingRaw('distance < ?', [15])
            ->orderBy('distance', 'ASC')
            ->offset($offset) //Paginação
            ->limit(5) //De 5 em 5 Cooperativas
            ->get();

        //Loop para trocar o avatar para url
        foreach($coops as $ckey => $cvalue){
            $coops[$ckey]['avatar'] = url('media/avatars/'.$coops[$ckey]['avatar']);
        }

        $array['data'] = $coops; //Pegando os dados
        $array['loc'] = 'São Paulo'; //Localização atual

        return $array;
    }

    //Pegando um barbeiro
    public function one($id){
        $array = ['error' => ''];

        $coop = Coop::find($id);

        //Se achou um barbeiro
        if($coop){
            $coop['avatar'] = url('media/avatars/'.$coop['avatar']); //Corrigindo o avatar
            $coop['favorited'] = false;
            $coop['photos'] = [];
            $coop['testimonials'] = [];
            $coop['available'] = [];

            //Verificando um favorito
            $cFavorite = UserFavorite::where('id_user', $this->loggedUser->id)
                                     ->where('id_coop', $coop->id)
                                     ->count();
            
            //Se Favorite é maior do que 0
            if($cFavorite > 0){
                $coop['favorited'] = true;
            }

            //Pegando as fotos da Cooperativa
            $coop['photos'] = CoopPhotos::select(['id', 'url'])->where('id_coop', $coop->id)->get();
            //Corrigindo a url das fotos
            foreach($coop['photos'] as $bpkey => $bpvalue){
                $coop['photos'][$bpkey]['url'] = url('media/uploads/'.$coop['photos'][$bpkey]['url']);
            }

            //Pegando os depoimentos da Cooperativa
            $coop['testimonials'] = CoopTestimonial::select(['id', 'name', 'rate', 'body'])->where('id_coop', $coop->id)->get();

            //Pegando a disponibilidade da Cooperativa
            $availability = [];

            //Pegando a disponibilidade crua
            $avails = CoopAvailability::where('id_coop', $coop->id)->get();
            $availWeekdays = []; //Array com os dias da semana
            foreach($avails as $item){
                $availWeekdays[$item['weekday']] = explode(',', $item['hours']);
            }

            //Pegando os agendamentos dos proximos 20 dias (incluindo hoje)
            $appointments = [];
            $appQuery = UserAppointment::where('id_coop', $coop->id)
                                         ->whereBetween('ap_datetime', [
                                             date('Y-m-d').' 00:00:00',
                                             date('Y-m-d', strtotime('+20 days')).' 23:59:59'
                                         ])
                                         ->get();
            
            foreach($appQuery as $appItem){
                $appointments[] = $appItem['ap_datetime']; //Todos os agendamentos que a Cooperativa já tem
            }

            //Gerar disponibilidade real
            for($q=0;$q<20;$q++){
                $timeItem = strtotime('+'.$q.' days');
                $weekday = date('w', $timeItem); //Dia da semana
                
                //Se o dia está na lista dos dias disponiveis
                if(in_array($weekday, array_keys($availWeekdays))){
                    //Verificando as horas disposiveis
                    $hours = [];

                    $dayItem = date('Y-m-d', $timeItem);

                    //Indo de hora em hora vendo se tem disponibilidade
                    foreach($availWeekdays[$weekday] as $hourItem){
                        $dayFormated = $dayItem.' '.$hourItem.':00';

                        //Verificando se a datetime está disponível
                        if(!in_array($dayFormated, $appointments)){
                            //Adicionando as horas disponíveis do dia
                            $hours[] = $hourItem;
                        }
                    }
                    
                    //Se eu tenho horários disponíveis naquele dia da semana
                    if(count($hours) > 0){
                        $availability[] = [
                            'date' => $dayItem,
                            'hours' => $hours
                        ];
                    }
                }
            }

            $coop['available'] = $availability; //Preenchendo o array

            $array['data'] = $coop;
        }else{
            $array['error'] = 'Cooperativa não existe!';
            return $array;
        }

        return $array;
    }
}
<?php

namespace App\Http\Controllers;

use Goutte\Client;
use Illuminate\Http\Request;
use App\Models\Narva;
use App\Models\Koidula;
use App\Models\Luhamaa;
use App\Models\Car;
use App\Models\Number;
use App\Models\Teluser;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class ParseController extends Controller
{
    private $row_results = Array();
    private $results = Array();

    public function addBorderId($arr, $border_id, $col) {
        foreach ($arr as $k=>$v) {
            $ret[$k] = $v;
            $ret[$k][$col] = $border_id;
        }
        return $ret;
    }

    public function uptoOrNum($row) {
        if ((trim($row) == 'up to 1 hour') OR (trim($row) == 'On time') OR (trim($row) == 'N/A')) {
            $ret = 0;
        }
        else {
            $ret = trim(preg_replace("/[^0-9]/", '', $row));
        }
        return $ret;
    }

    public function doParse () {
   
        $client = new Client();
        $url_table = 'https://www.eestipiir.ee/yphis/borderQueueInfo.action';
        $url_cars_ab = 'https://www.eestipiir.ee/yphis/showCalledVehicles.action?borderCrossingQueue.id=1';
        $url_cars_c = 'https://www.eestipiir.ee/yphis/showCalledVehicles.action?borderCrossingQueue.id=2';
        
        //Narva main table
        $m_page = $client->request(method:'GET', uri:$url_table);
        $narva_tbl = $m_page->filter('table.maintable')->eq(0);

        $narva_tbl->filter('td')->each(function($item){
            $this->row_results['narva'][$item->attr('id')] =  $item->text();
        });

        $this->results['narva']['lql_ab'] = $this->uptoOrNum($this->row_results['narva']['lql-1']);
        $this->results['narva']['nvl_ab'] = $this->uptoOrNum($this->row_results['narva']['nvl-1']);
        $this->results['narva']['nvf_ab'] = $this->uptoOrNum($this->row_results['narva']['nvf-1']);
        //$this->results['narva']['frt_ab'] = Carbon::parse($this->row_results['narva']['frt-1']);
        $this->results['narva']['frt_ab'] = '2025-01-01 00:00:01';
        $this->results['narva']['lagh_ab'] = $this->uptoOrNum($this->row_results['narva']['lagh-1']);
        /*
        $this->results['narva']['nvl_c'] = $this->row_results['narva']['nvl-2'];
        $this->results['narva']['nvf_c'] = $this->row_results['narva']['nvf-2'];
        $this->results['narva']['frt_c'] = Carbon::parse($this->row_results['narva']['frt-2']);
        $this->results['narva']['lagh_c'] = $this->uptoOrNum($this->row_results['narva']['lagh-2']);
        */
        $validator = Validator::make($this->results['narva'], [
            'lql_ab' => 'integer',
            'nvl_ab' => 'integer',
            'nvf_ab' => 'integer',
            'frt_ab' => 'date',
            'lagh_ab' => 'integer'
        ]);
        /*
            'nvl_c' => 'integer',
            'nvf_c' => 'integer',
            'frt_c' => 'date',
            'lagh_c' => 'integer',
            */
        

        if ($validator->fails()) {
           \Log::debug('Narva parse validation error', $this->results['narva']);
        }
 
        $narva = new Narva();
        
        $narva->lql_ab = $this->results['narva']['lql_ab'];
        $narva->nvl_ab = $this->results['narva']['nvl_ab'];
        $narva->nvf_ab = $this->results['narva']['nvf_ab'];
        $narva->frt_ab = $this->results['narva']['frt_ab'];
        $narva->lagh_ab = $this->results['narva']['lagh_ab'];

        $narva->nvl_c = 0;
        $narva->nvf_c = 0;
        $narva->frt_c = '2022-12:31 15:00:00';
        $narva->lagh_c = 0;
/*
        $narva->nvl_c = $this->results['narva']['nvl_c'];
        $narva->nvf_c = $this->results['narva']['nvf_c'];
        $narva->frt_c = $this->results['narva']['frt_c'];
        $narva->lagh_c = $this->results['narva']['lagh_c'];
        */
        $narva->save();
        $last_id = $narva->id;
/*
        $car = new Car();
        //Cars AB
        $page = $client->request(method:'GET', uri:$url_cars_ab);
        $list_tbl = $page->filter('table.maintable')->filter('td');
        if ($list_tbl->count()) {
            $list_tbl->filter('td')->each(function($item, $i){
                $this->row_results['cars_ab_n'][$i]['number'] =  $item->text();
                $this->row_results['cars_ab_n'][$i]['car_type'] =  'ab';
            });
            $this->results['cars_ab_n'] = $this->addBorderId($this->row_results['cars_ab_n'], $last_id, 'narva_id');
            $car->insert($this->results['cars_ab_n']);
        }
        //Cars C
        $page = $client->request(method:'GET', uri:$url_cars_c);
        $list_tbl = $page->filter('table.maintable')->filter('td');
        if ($list_tbl->count()) {
            $list_tbl->each(function($item, $i){
                $this->row_results['cars_c_n'][$i]['number'] = $item->text();
                $this->row_results['cars_c_n'][$i]['car_type'] = 'c';
            });
            $this->results['cars_c_n'] = $this->addBorderId($this->row_results['cars_c_n'], $last_id, 'narva_id');
            $car->insert($this->results['cars_c_n']);
        }
        */

        echo 'Parse... Narva. Done!';
        // ************************************ End Narva **********************************

        $url_cars_ab = 'https://www.eestipiir.ee/yphis/showCalledVehicles.action?borderCrossingQueue.id=4';
        $url_cars_c = 'https://www.eestipiir.ee/yphis/showCalledVehicles.action?borderCrossingQueue.id=5';
        
        //Koidula main table
        $koidula_tbl = $m_page->filter('table.maintable')->eq(1);

        $koidula_tbl->filter('td')->each(function($item){
            $this->row_results['koidula'][$item->attr('id')] =  $item->text();
        });

        $this->results['koidula']['lql_ab'] = $this->uptoOrNum($this->row_results['koidula']['lql-4']);
        $this->results['koidula']['nvl_ab'] = $this->row_results['koidula']['nvl-4'];
        $this->results['koidula']['nvf_ab'] = $this->uptoOrNum($this->row_results['koidula']['nvf-4']);
        $this->results['koidula']['frt_ab'] = Carbon::parse($this->row_results['koidula']['frt-4']);
        $this->results['koidula']['lagh_ab'] = $this->uptoOrNum($this->row_results['koidula']['lagh-4']);

        $validator = Validator::make($this->results['koidula'], [
            'lql_ab' => 'integer',
            'nvl_ab' => 'integer',
            'nvf_ab' => 'integer',
            'frt_ab' => 'date',
            'lagh_ab' => 'integer'
        ]);

        if ($validator->fails()) {
           \Log::debug('Koidula parse validation error', $this->results['koidula']);
        }
 
        $koidula = new Koidula();
        
        $koidula->lql_ab = $this->results['koidula']['lql_ab'];
        $koidula->nvl_ab = $this->results['koidula']['nvl_ab'];
        $koidula->nvf_ab = $this->results['koidula']['nvf_ab'];
        $koidula->frt_ab = $this->results['koidula']['frt_ab'];
        $koidula->lagh_ab = $this->results['koidula']['lagh_ab'];
    
        $koidula->save();
        $last_id = $koidula->id;

        $car = new Car();
        //Cars AB
        $page = $client->request(method:'GET', uri:$url_cars_ab);
        $list_tbl = $page->filter('table.maintable')->filter('td');
        if ($list_tbl->count()) {
            $list_tbl->filter('td')->each(function($item, $i){
                $this->row_results['cars_ab_k'][$i]['number'] =  $item->text();
                $this->row_results['cars_ab_k'][$i]['car_type'] =  'ab';
            });
            $this->results['cars_ab_k'] = $this->addBorderId($this->row_results['cars_ab_k'], $last_id, 'koidula_id'); //!!!!!!!
            $car->insert($this->results['cars_ab_k']);
        }
        //Cars C
        $page = $client->request(method:'GET', uri:$url_cars_c);
        $list_tbl = $page->filter('table.maintable')->filter('td');
        if ($list_tbl->count()) {
            $list_tbl->each(function($item, $i){
                $this->row_results['cars_c_k'][$i]['number'] = $item->text();
                $this->row_results['cars_c_k'][$i]['car_type'] = 'c';
            });
            $this->results['cars_c_k'] = $this->addBorderId($this->row_results['cars_c_k'], $last_id, 'koidula_id');   //!!!!!!
            $car->insert($this->results['cars_c_k']);
        }

        echo 'Parse... Koidula. Done!';       

        // ************************************ End Koidula **********************************
        
        $url_cars_ab = 'https://www.eestipiir.ee/yphis/showCalledVehicles.action?borderCrossingQueue.id=7';
        $url_cars_c = 'https://www.eestipiir.ee/yphis/showCalledVehicles.action?borderCrossingQueue.id=8';
        
        //luhamaa main table
        $luhamaa_tbl = $m_page->filter('table.maintable')->eq(2);

        $luhamaa_tbl->filter('td')->each(function($item){
            $this->row_results['luhamaa'][$item->attr('id')] =  $item->text();
        });

        $this->results['luhamaa']['lql_ab'] = $this->uptoOrNum($this->row_results['luhamaa']['lql-7']);
        $this->results['luhamaa']['nvl_ab'] = $this->row_results['luhamaa']['nvl-7'];
        $this->results['luhamaa']['nvf_ab'] = $this->uptoOrNum($this->row_results['luhamaa']['nvf-7']);
        $this->results['luhamaa']['frt_ab'] = Carbon::parse($this->row_results['luhamaa']['frt-7']);
        $this->results['luhamaa']['lagh_ab'] = $this->uptoOrNum($this->row_results['luhamaa']['lagh-7']);

        $validator = Validator::make($this->results['luhamaa'], [
            'lql_ab' => 'integer',
            'nvl_ab' => 'integer',
            'nvf_ab' => 'integer',
            'frt_ab' => 'date',
            'lagh_ab' => 'integer'
        ]);

        if ($validator->fails()) {
           \Log::debug('Luhamaa parse validation error', $this->results['luhamaa']);
        }
 
        $luhamaa = new Luhamaa();
        
        $luhamaa->lql_ab = $this->results['luhamaa']['lql_ab'];
        $luhamaa->nvl_ab = $this->results['luhamaa']['nvl_ab'];
        $luhamaa->nvf_ab = $this->results['luhamaa']['nvf_ab'];
        $luhamaa->frt_ab = $this->results['luhamaa']['frt_ab'];
        $luhamaa->lagh_ab = $this->results['luhamaa']['lagh_ab'];
    
        $luhamaa->save();
        $last_id = $luhamaa->id;

        $car = new Car();
        //Cars AB
        $page = $client->request(method:'GET', uri:$url_cars_ab);
        $list_tbl = $page->filter('table.maintable')->filter('td');
        if ($list_tbl->count()) {
            $list_tbl->filter('td')->each(function($item, $i){
                $this->row_results['cars_ab_l'][$i]['number'] =  $item->text();
                $this->row_results['cars_ab_l'][$i]['car_type'] =  'ab';
            });
            $this->results['cars_ab_l'] = $this->addBorderId($this->row_results['cars_ab_l'], $last_id, 'luhamaa_id'); //!!!!!!!
            $car->insert($this->results['cars_ab_l']);
        }
        //Cars C
        $page = $client->request(method:'GET', uri:$url_cars_c);
        $list_tbl = $page->filter('table.maintable')->filter('td');
        if ($list_tbl->count()) {
            $list_tbl->each(function($item, $i){
                $this->row_results['cars_c_l'][$i]['number'] = $item->text();
                $this->row_results['cars_c_l'][$i]['car_type'] = 'c';
            });
            $this->results['cars_c_l'] = $this->addBorderId($this->row_results['cars_c_l'], $last_id, 'luhamaa_id');   //!!!!!!
            $car->insert($this->results['cars_c_l']);
        }

        echo 'Parse... Luhamaa. Done!';   
    }
    public function doCheck() {
        //check numbers
        $telUsers = new Teluser();
        $Numbers = new Number();
        $cars = new Car();
        $max_id= $cars->where('car_type','=','ab')->max('narva_id');
        $rows = $Numbers->select('number', 'tuid', 'att', 'id as num_id')->where('status','=','on')->where('att','<',3)->get();
        foreach ($rows as $row) {       

            $cars_nums = $cars->select('id')
            ->where('narva_id','=',$max_id)
            ->where('number','=',$row->number)
            ->get()->first();
            if (isset($cars_nums->id)) {
                //send message to telegram
                $telnum = $telUsers->select('user_id')->where('id','=',$row->tuid)->get()->first();
                echo 'Send to Telegram! to id='.$telnum->user_id.'<br>';
                $this->tHello($telnum->user_id, 'Номер '.$row->number.', вас вызывают на границу!');
                $Numbers->where('id','=',$row->num_id)->increment('att');
                if ($row->att == 2) {
                    $Numbers->where('id','=',$row->num_id)->update(['status' => 'off']);
                }
            }
        }
        echo 'Check... Done!';
    }

    public function tHello($id, $message) {
        $http = Http::post('https://api.telegram.org/bot'.config('custom.telegram_token').'/sendMessage', [
            'chat_id' => $id,
            'text'=> $message
        ]);
    }
}

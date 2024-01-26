<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Narva;
use App\Models\Koidula;
use App\Models\Luhamaa;
use App\Models\Car;
use Illuminate\Support\Facades\DB;

class EeController extends Controller
{
    public function index($border='') {
        $data['border_tag'] = $border;
        if ($data['border_tag'] == 'narva') {
            $data['border_id'] = 1;
            $data['border_title'] = 'Нарва - Ивангород';
        } elseif ($data['border_tag'] == 'koidula') {
            $data['border_id'] = 2;
            $data['border_title'] = 'Койдула - Кунична Гора';
        } elseif ($data['border_tag'] == 'luhamaa') {
            $data['border_id'] = 3;
            $data['border_title'] = 'Лухамаа - Шумилкино';
        }
        else {
            $data['border_tag'] == 'narva';
            $data['border_id'] = 1;
            $data['border_title'] = 'Нарва - Ивангород';
        }
       return view('ee', $data);
    }

    public function progressData($border_id=0) {
        if ($border_id==1) $narva = new Narva();
        if ($border_id==2) $narva = new Koidula();
        if ($border_id==3) $narva = new Luhamaa();

       // \Log::debug('Our Class', Array($class));
        $cur_date = \Carbon\Carbon::now()->addDay(-0)->addHour(-0)->toDateTimeString();
        $cur_date2 = \Carbon\Carbon::now()->addDay(-0)->addHour(-1)->toDateTimeString();
        $lags= $narva->select(DB::raw('id, lagh_ab , created_at'))
        ->where('created_at','<=', \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $cur_date))
        ->where('created_at','>', \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $cur_date2))
        ->orderByDesc('id')
        ->get();
     
        $first_el = -1;
        $cnt=0;
        $prog2=0;
        foreach($lags as $lag) {
            if($first_el == -1) {
                $first_el = $lag->lagh_ab;
            }
            else {
                if($lag->lagh_ab>$first_el) $cnt++;
            }
            if ($cnt>0) $prog2 = $cnt* round(100/(count($lags)-1));
            else  $prog2 = 0;
        }
            $ret = array(
                'progress2'=>$prog2, 'frt'=>$first_el, 'cnt'=>$cnt
               // 'progress2'=>40, 'frt'=>0, 'cnt'=>$cnt
            );
            //sleep(5);
            return $ret;
     }
    public function chartData($border_id, $delta=0) {
        if ($border_id==1) $narva = new Narva();
        if ($border_id==2) $narva = new Koidula();
        if ($border_id==3) $narva = new Luhamaa();
    
        $cur_date = \Carbon\Carbon::now()->addDay(-$delta)->toDateString();
    
        if ($delta!=0) {
            $format = 'Y-m-d H:i';
            $text = $cur_date;
            $cur_date = $cur_date.' 23:59';
        }
        else { 
            $format = 'Y-m-d';
            $text = 'last_24';
        }

       //DATE_FORMAT(created_at, "%Y-%m-%d %H") as hr,
        $lags= $narva->select(
            DB::raw('
            AVG(nvf_ab) as avg_nvf_ab, AVG(nvl_ab) as avg_nvl_ab,
            MAX(lagh_ab) as max_lagh_ab, 
            DATE_FORMAT(created_at, "%Y-%m-%d %H") as hr
            ')
            )
            ->where('created_at','<=', \Carbon\Carbon::createFromFormat($format, $cur_date))
            ->where('created_at','>=', \Carbon\Carbon::createFromFormat($format, $cur_date)->addHour(-24))
            ->groupBy('hr')
            ->get();
           // dd(DB::getQueryLog());
           //DATE_FORMAT(created_at, "%Y-%m-%d %H") as hrh
           //DATE_FORMAT(created_at, "%H") as hrh
           $days = Array();
           $days2 = Array();
        foreach($lags as $lag) {
            $days[] = $lag->hr;
            $cars[] = $lag->max_lagh_ab;
            $nvf[] = round($lag->avg_nvf_ab);
            $nvl[] = $lag->avg_nvl_ab;
        }
        if ($days) {
        $days2 = array_map(function($num){
            $delta = 3;
            $ret = substr($num, -2);
            $ret = $ret + $delta;
            if($ret>=24) $ret = 0 + $ret - 24;
            return $ret;
        }, $days);
        }
        //sleep(1);
        return $ret = array('graf' => [
            'labels'=>$days2,
            'datasets'=>array([
                'label'=>'Delay',
                'borderWidth'=> '3',
                'borderColor'=> 'yellow',
                'backgroundColor'=>'rgba(54, 162, 235, 0.5)',
                'data'=>$cars
            ])
            ], 
            'bar' => [
                'labels'=>$days2,
                'datasets'=>array([
                    
                    'label'=>'Amount of Cars',
                    'backgroundColor'=>'blue',
                    'data'=>$nvf
                ]
                //,
                /*
                [
                    
                    'label'=>'In Live queue',
                    'backgroundColor'=>'red',
                    'data'=>$nvl
                ]
                */
                )
                ],
            'delta'=>$delta, 'text'=>$text
        );
    }
    public function getCars($border_id) {
        if ($border_id==1) $border = 'narva';
        if ($border_id==2) $border = 'koidula';
        if ($border_id==3) $border = 'luhamaa';
        $cars = new Car();
        $max_id= $cars->where('car_type','=','ab')->max($border.'_id');
        $cars_nums = $cars->select('number')->where($border.'_id','=',$max_id)->where($border.'_id','!=',0)->orderby('id')->get();
        foreach($cars_nums as $cars_num) {
            $nums[] = $cars_num->number;
        }
        return $ret=array('cars'=>$nums);
    }

}

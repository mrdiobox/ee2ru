<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Narva;
use App\Models\Car;
use Illuminate\Support\Facades\DB;

class EeController extends Controller
{
    public function index() {
       return view('ee')
    }

    public function progressData() {
        $narva = new Narva();
        $cur_date = \Carbon\Carbon::now()->addDay(-0)->addHour(-0)->toDateTimeString();
        $cur_date2 = \Carbon\Carbon::now()->addDay(-0)->addHour(-1)->toDateTimeString();
        $lags= $narva->select(DB::raw('id, lagh_ab , created_at'))
        ->where('created_at','<=', \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $cur_date))
        ->where('created_at','>', \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $cur_date2))
        ->orderByDesc('id')
        ->get();
     
        $first_el = -1;
        $cnt=0;
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
    public function chartData($delta=0) {
        
        $narva = new Narva();
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
        foreach($lags as $lag) {
            $days[] = $lag->hr;
            $cars[] = $lag->max_lagh_ab;
            $nvf[] = round($lag->avg_nvf_ab);
            $nvl[] = $lag->avg_nvl_ab;
        }
        
        $days2 = array_map(function($num){
            $delta = 2;
            $ret = substr($num, -2);
            $ret = $ret + $delta;
            if($ret>=24) $ret = 0 + $ret - 24;
            return $ret;
        }, $days);
        
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
    public function getCars() {
        $cars = new Car();
        $max_id= $cars->where('car_type','=','ab')->max('narva_id');
        $cars_nums = $cars->select('number')->where('narva_id','=',$max_id)->orderby('id')->get();
        foreach($cars_nums as $cars_num) {
            $nums[] = $cars_num->number;
        }
        return $ret=array('cars'=>$nums);
    }

}

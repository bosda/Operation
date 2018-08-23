<?php

namespace App\Utils;

use Exception;
use Log;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Throwable;

class ReportHelper
{
    protected static $projectToken = '';

    protected static $api = 'http://operation.world/api/log';
    protected static $errorType = 1;
    protected static $infoType = 2;

    public static function setConfig($projectTonek)
    {
        self::$projectToken = $projectToken;
    }

    public static function exception(Throwable $e, $logFile = true)
    {
        
        if (!self::checkConfig()) {
            throw new Exception('the project token is not set');
        }

        if (!config('operation.report')) {
            return;
        }

        if (!$e instanceof Exception) {
            $e = new FatalThrowableError($e);
        }

        try {
            $data = [
                'timestamp' => time(),
                'project_token' => self::$projectToken,
                'type' => self::$errorType,
                'message' => json_encode(self::exceptionToArray($e)),
            ];

            if ($logFile && null !== $log = self::getLogFile()) {
                $data['files'][] = $log;
            }
            $data['sign'] = self::getSign($data);

            $result = self::request($data);
            self::pareResponse($result);
        } catch (Throwable $e) {
            Log::error($e);
        }
    }

    public static function logAndReport(Throwable $e)
    {
        if (!$e instanceof Exception) {
            $e = new FatalThrowableError($e);
        }

        Log::warning($e);
        self::exception($e);
    }

    public static function info($info, $files = [])
    {
        if (!self::checkConfig()) {
            throw new Exception('the project token is not set');
        }

        if (!config('operation.report')) {
            return;
        }

        try {
            $data = [
                'timestamp' => time(),
                'project_token' => self::$projectToken,
                'type' => self::$infoType,
                'message' => $info,
            ];
            if (!is_array($files)) {
                $files = [$files];
            }
            foreach ($files as $file) {
                $data['files'][] = $file;
            }

            $data['sign'] = self::getSign($data);

            $result = self::request($data);
            self::pareResponse($result);
        } catch (Throwable $e) {
            Log::error($e);
        }
    }

    protected static function checkConfig()
    {
        if(self::$projectToken == '') {
            self::$projectToken = config('operation.token');
        }

        return self::$projectToken != '';
    }

    protected static function exceptionToArray($e, &$array = [], $i = 0)
    {
        if ($e !== null) {
            $array[$i]['key'] = $i + 1;
            $array[$i]['type'] = get_class($e);
            $array[$i]['message'] = $e->getMessage();
            $array[$i]['code'] = $e->getCode();
            $array[$i]['file'] = $e->getFile();
            $array[$i]['line'] = $e->getLine();
            $array[$i]['trace'] = explode("\n", $e->getTraceAsString());

            self::exceptionToArray($e->getPrevious(), $array, $i + 1);
        }
        return $array;
    }

    protected static function getLogFile()
    {
        $config = config('app.log');
        switch ($config) {
            case 'single':
                return storage_path('logs/laravel.log');
            case 'daily':
                $log = 'laravel' . date('-Y-m-d') . '.log';
                if (!file_exists(storage_path("logs/{$log}"))) {
                    $log = 'laravel' . date('-Y-m-d', strtotime("-1 day")) . '.log';
                }
                return storage_path("logs/{$log}");
            default :
                return null;
        }
    }

    protected static function getSign($data)
    {
        return sha1(md5($data['project_token']));
    }

    protected static function ksort(&$array)
    {
        if (is_array($array)) {
            ksort($array);

            foreach ($array as &$value) {
                $value = self::ksort($value);
            }
        }
        return $array;
    }

    protected static function arrayToString($array)
    {
        $string = '';
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $v = self::arrayToString($v);
            }
            $string .= $k . $v;
        }
        return $string;
    }

    protected static function request($data)
    {
        if (isset($data['files'])) {
            foreach ($data['files'] as &$v) {
                $v = curl_file_create(realpath($v));
            }
            foreach ($data['files'] as $key => $value) {
                $data["files[{$key}]"] = $value;
            }
            unset($data['files']);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$api);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Requested-With:XMLHttpRequest', 'Expect:']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    protected static function pareResponse($result)
    {
        echo "<pre>";echo $result;exit;
        $result = json_decode($result, true);
        if ($result['status'] != 10000) {
            throw new Exception("report error: " . json_encode($result, 256));
        }
    }
}

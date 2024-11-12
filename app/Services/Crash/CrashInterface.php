<?php

namespace App\Services\Crash;
use \Exception;

interface CrashInterface
{
    public static function handler(Exception $data) :void;
    public static function logger($data) :array;
    public static function assignMessage($exception) :string;
    public static function sendErrors(array $data, $file) :void;
    public static function resendErrors() :void;
    public static function call($data, $file) :void;
    public static function collectSessions() :array;

}

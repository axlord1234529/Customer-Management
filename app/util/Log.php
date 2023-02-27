<?php

namespace App\util;

use App\util\Config;

class Log
{
    public static function write($type, $message)
    {
        $date = new \DateTime();
        $logDir = Config::get('roots/log directory');
        $log = $logDir.$date->format('Y-m-d').".txt";

        if(is_dir($logDir))
        {
            $logContent = $type.": ".$date->format('H:i:s').": ". $message ."\n";
            if(!file_exists($log))
            {
                $fh = fopen($log,'a+') or die("Unable to open file!");
                fwrite($fh,$logContent);
                fclose($fh);
            }
            else
            {
                $logContent = $logContent . file_get_contents($log);
                file_put_contents($log,$logContent);
            }
        }
        elseif(mkdir($logDir, 0777, true) === true)
        {
            self::write($type,$message);
        }
    }
}
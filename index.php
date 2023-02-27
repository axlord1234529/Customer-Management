<?php
require_once realpath("vendor/autoload.php");
require_once realpath('app/include/init.php');
use App\util\Log;
Log::write('info','First log succesfully created');

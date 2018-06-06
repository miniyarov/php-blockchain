<?php

require 'vendor/autoload.php';

use \Blockchain\CLI;

$cli = new CLI();
$cli->run($argv);

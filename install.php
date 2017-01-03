<?php
if (php_sapi_name() !== 'cli') {
    exit('It\'s no cli!');
}

//Configurations - you can change...
$name = 'Database';
$file = 'Database.php';
$configPath = defined('_CONFIG') ? _CONFIG : dirname(dirname(dirname(__DIR__))).'/Config/';

//Checkin
if (is_file($configPath.$file)) {
    return "\n--- $name configuration file already exists!";
}
if (!is_dir($configPath)) {
    return "\n\n--- Configuration file for $name not instaled!\n\n";
}

//Gravando o arquivo de configuração no CONFIG da aplicação
file_put_contents($configPath.$file,
    file_get_contents(__DIR__.'/config.php'));

//Return to application installer
return "\n--- $name instaled!";

<?php

// Import additionnal class into the global namespace
use LaswitchTech\coreEncryption\Encryption;

//Load Composer's autoloader
require 'vendor/autoload.php';

//Initiate Auth
$Encryption = new Encryption();

//Set Private Key
echo PHP_EOL . "[setPrivateKey]" . PHP_EOL;
echo json_encode($Encryption->setPrivateKey("Private Key"), JSON_PRETTY_PRINT) . PHP_EOL;

//Set Public Key
echo PHP_EOL . "[setPublicKey]" . PHP_EOL;
echo json_encode($Encryption->setPublicKey("Public Key"), JSON_PRETTY_PRINT) . PHP_EOL;

//Output Encrypted Data
echo PHP_EOL . "[encrypt]" . PHP_EOL;
$Encrypted = $Encryption->encrypt('Hello Wolrd!');
echo json_encode($Encrypted, JSON_PRETTY_PRINT) . PHP_EOL;

//Output Decrypted Data
echo PHP_EOL . "[decrypt]" . PHP_EOL;
echo json_encode($Encryption->decrypt($Encrypted), JSON_PRETTY_PRINT) . PHP_EOL;

//Output Encrypted Data
echo PHP_EOL . "[encrypt]" . PHP_EOL;
$Encrypted = $Encryption->encrypt('tmp/test.txt', 'tmp/test.txt.'.time().'.encrypted');
echo json_encode($Encrypted, JSON_PRETTY_PRINT) . PHP_EOL;

//Output Decrypted Data
echo PHP_EOL . "[decrypt]" . PHP_EOL;
echo json_encode($Encryption->decrypt($Encrypted, 'tmp/test.txt.'.time().'.decrypted'), JSON_PRETTY_PRINT) . PHP_EOL;

//Output Decrypted Data
echo PHP_EOL . "[decrypt]" . PHP_EOL;
echo json_encode($Encryption->decrypt('tmp/test.txt.1667515281.encrypted', 'tmp/test.txt.1667515281.encrypted.'.time().'.decrypted'), JSON_PRETTY_PRINT) . PHP_EOL;

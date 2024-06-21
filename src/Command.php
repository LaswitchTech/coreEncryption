<?php

// Declaring namespace
namespace LaswitchTech\coreEncryption;

// Import additionnal class into the global namespace
use LaswitchTech\coreBase\BaseCommand;
use LaswitchTech\coreEncryption\Encryption;

class Command extends BaseCommand {

    // Properties
    protected $Encryption;

    /**
     * Constructor
     * @param object $Auth
     */
	public function __construct($Auth){

        // Namespace: /encryption

        // Initialize Encryption
        $this->Encryption = new Encryption();

		// Call the parent constructor
		parent::__construct($Auth);
	}

    /**
     * Encrypt a string
     * @param array $argv
     * @return string|boolean
     */
    public function encryptAction($argv){

        // Namespace /encryption/encrypt $string, $filename = null

        // Retrieve parameters
        $string = $argv[0] ?? null;
        $filename = $argv[1] ?? null;

        // Debug information
        $this->Logger->debug('$string: ' . json_encode($string, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->Logger->debug('$filename: ' . json_encode($filename, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // Check for required parameters
        if(count($argv) < 1 || empty($string)){

            // Log error and debug information
            $this->Logger->error('Missing required parameters');

            // Send the output
            $this->error('Missing required parameters');

            return;
        }

        // Set a configuration
        if($result = $this->Encryption->encrypt($string, $filename)){

            // Return success
            $this->success($string . ' encrypted to ' . $result);
        } else {

            // Return error
            $this->error('Unable to encrypt ' . $string);
        }
    }

    /**
     * Decrypt a string
     * @param array $argv
     * @return string|boolean
     */
    public function decryptAction($argv){

        // Namespace /encryption/decrypt $string, $filename = null

        // Retrieve parameters
        $string = $argv[0] ?? null;
        $filename = $argv[1] ?? null;

        // Debug information
        $this->Logger->debug('$string: ' . json_encode($string, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->Logger->debug('$filename: ' . json_encode($filename, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // Check for required parameters
        if(count($argv) < 1 || empty($string)){

            // Log error and debug information
            $this->Logger->error('Missing required parameters');

            // Send the output
            $this->error('Missing required parameters');

            return;
        }

        // Set a configuration
        if($result = $this->Encryption->decrypt($string, $filename)){

            // Return success
            $this->success($string . ' decrypted to ' . $result);
        } else {

            // Return error
            $this->error('Unable to decrypt ' . $string);
        }
    }
}

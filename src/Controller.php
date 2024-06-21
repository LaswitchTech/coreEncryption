<?php

// Declaring namespace
namespace LaswitchTech\coreEncryption;

// Import additionnal class into the global namespace
use LaswitchTech\coreBase\BaseController;
use LaswitchTech\coreEncryption\Encryption;

class Controller extends BaseController {

    // Properties
    protected $Encryption;

    /**
     * Constructor
     * @param object $Auth
     */
	public function __construct($Auth){

        // Namespace: /configurator

		// Set the controller Authentication Policy
		$this->Public = true; // Set to false to require authentication

		// Set the controller Authorization Policy
		$this->Permission = false; // Set to true to require a permission for the namespace used.
		$this->Level = 1; // Set the permission level required

        // Initialize Encryption
        $this->Encryption = new Encryption();

		// Call the parent constructor
		parent::__construct($Auth);
	}

    /**
     * Encrypt a string
     */
    public function encryptAction($argv){

        // Namespace /encryption/encrypt $string, $filename = null

        // Debug information
        $this->Logger->debug('Namespace: /encryption/encrypt $string, $filename = null');
        $this->Logger->debug('Extending: coreAPI');

        // Retrieve parameters
        $string = $this->getParams('REQUEST', 'string');
        $filename = $this->getParams('REQUEST', 'filename');

        // Debug information
        $this->Logger->debug('$string: ' . json_encode($string, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->Logger->debug('$filename: ' . json_encode($filename, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // Check for required parameters
        if(empty($string)){

            // Log error and debug information
            $this->Logger->error('Missing required parameters');

            // Send the output
            $this->error('Missing required parameters');

            return;
        }

        // Set a configuration
        if($result = $this->Encryption->encrypt($string, $filename)){

            // Send the output
            $this->output(
                $result,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {

            // Send the output
            $this->output(
                'Unable to encrypt ' . $string,
                array('Content-Type: application/json', 'HTTP/1.1 500 Internal Server Error')
            );
        }
    }

    /**
     * Decrypt a string
     */
    public function decryptAction($argv){

        // Namespace /encryption/decrypt $string, $filename = null

        // Debug information
        $this->Logger->debug('Namespace: /encryption/decrypt $string, $filename = null');
        $this->Logger->debug('Extending: coreAPI');

        // Retrieve parameters
        $string = $this->getParams('REQUEST', 'string');
        $filename = $this->getParams('REQUEST', 'filename');

        // Debug information
        $this->Logger->debug('$string: ' . json_encode($string, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->Logger->debug('$filename: ' . json_encode($filename, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // Check for required parameters
        if(empty($string)){

            // Log error and debug information
            $this->Logger->error('Missing required parameters');

            // Send the output
            $this->error('Missing required parameters');

            return;
        }

        // Set a configuration
        if($result = $this->Encryption->decrypt($string, $filename)){

            // Send the output
            $this->output(
                $result,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {

            // Send the output
            $this->output(
                'Unable to decrypt ' . $string,
                array('Content-Type: application/json', 'HTTP/1.1 500 Internal Server Error')
            );
        }
    }
}

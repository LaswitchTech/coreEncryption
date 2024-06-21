<?php

// Declaring namespace
namespace LaswitchTech\coreEncryption;

// Import additionnal class into the global namespace
use LaswitchTech\coreConfigurator\Configurator;
use LaswitchTech\coreLogger\Logger;
use Exception;

class Encryption {

    // Constants
    const CIPHER = 'AES-256-CBC';

    // Properties
    protected $Cipher;
    protected $PrivateKey;
    protected $PrivateHash;
    protected $PublicKey;
    protected $PublicHash;

    // Logger
    protected $Logger;

    // Configurator
    protected $Configurator;

    /**
     * Constructor
     */
    public function __construct(){

        // Initialize Configurator
        $this->Configurator = new Configurator(['encryption']);

        // Initiate Logger
        $this->Logger = new Logger('encryption');

        // Basic Configuration
        $this->setCipher();
        $this->setPublicKey();
        $this->setPrivateKey();
    }

    /**
     * Helpers
     */

    /**
     * Generate a random string
     * @param int $length
     * @return string
     */
    protected function hex($length = 16){

        // Generate a random string
        $string = bin2hex(openssl_random_pseudo_bytes($length = 16));

        // Log the string
        $this->Logger->debug('Generated a random string [' . $string . '](' . $length . ')');

        // Return the string
        return $string;
    }

    /**
     * Hash a string
     * @param string $string
     * @return string
     */
    protected function hash($string){

        // Hash the string
        $hash = hash( 'sha256', $string );

        // Log the string
        $this->Logger->debug('Hashed a string [' . $string . '](' . $hash . ')');

        // Return the hash
        return $hash;
    }

    /**
     * Configure
     */

    /**
     * Configure Library.
     *
     * @param  string  $option
     * @param  bool|int  $value
     * @return void
     * @throws Exception
     */
    public function config($option, $value){
        if(is_string($option)){
            switch($option){
                case"key":
                case"cipher":
                    if(is_string($value)){

                        // Set the value
                        switch($option){
                            case"key":
                                $this->setPrivateKey($value);
                                break;
                            case"cipher":
                                $this->setCipher($value);
                                break;
                        }

                        // Save to Configurator
                        $this->Configurator->set('encryption',$option, $value);
                    } else{
                        throw new Exception("2nd argument must be a boolean.");
                    }
                    break;
                default:
                    throw new Exception("unable to configure $option.");
                    break;
            }
        } else{
            throw new Exception("1st argument must be as string.");
        }

        return $this;
    }

    /**
     * Set the cipher
     * @param string $cipher
     * @return void
     */
    public function setCipher($cipher = null){

        // Check if the cipher is defined
        if($cipher == null || !is_string($cipher)){
            if($this->Configurator->get('encryption','cipher')){
                $cipher = $this->Configurator->get('encryption','cipher');
            } else {
                $cipher = self::CIPHER;
            }
        }

        // Check if the cipher is valid and set it
        if(in_array($cipher,openssl_get_cipher_methods())){ $this->Cipher = $cipher; }

        // Log the cipher
        $this->Logger->debug('Cipher set to [' . $this->Cipher . ']');
    }

    /**
     * Set the public key
     * @param string $key
     * @return void
     */
    public function setPublicKey($key = null){

        // Check if the key is defined
        if($key == null || !is_string($key)){ $key = $this->hex(); }

        // Set the key
        $this->PublicKey = $key;
        $this->PublicHash = $this->hash($this->PublicKey);

        // Log the key
        $this->Logger->debug('Public Key set to [' . $this->PublicKey . ']');
    }

    /**
     * Set the private key
     * @param string $key
     * @return void
     */
    public function setPrivateKey($key = null){

        // Check if the key is defined
        if($key == null || !is_string($key)){
            if($this->Configurator->get('encryption','key')){
                $key = $this->Configurator->get('encryption','key');
            } else {
                $key = $this->hex();
            }
        }

        // Set the key
        $this->PrivateKey = $key;
        $this->PrivateHash = $this->hash($this->PrivateKey);
        $this->PrivateHash = substr($this->PrivateHash, 0, openssl_cipher_iv_length($this->Cipher));

        // Log the key
        $this->Logger->debug('Private Key set to [' . $this->PrivateKey . ']');
    }

    /**
     * Methods
     */

    /**
     * Encrypt a string or a file
     * @param string $string
     * @param string $filename
     * @return string
     */
    public function encrypt($string, $filename = null){

        // Check if the string is a file
        if(is_file($string)){

            // Encrypt the file and return it
            if($filename == null){ $filename = $string.'.'.$this->Cipher; }
            $blob = base64_encode( openssl_encrypt( file_get_contents($string), $this->Cipher, $this->PublicHash, 0, $this->PrivateHash ) );
            $file = fopen($filename, 'w');
            fwrite($file, $blob);
            fclose($file);
            return $filename;
        } elseif(is_string($string)){

            // Encrypt the string and return it
            return base64_encode( openssl_encrypt( $string, $this->Cipher, $this->PublicHash, 0, $this->PrivateHash ) );
        } else {

            // Log the error
            $this->Logger->error('Failed to encrypt the string [' . $string . ']');

            // Return false
            return false;
        }
    }

    /**
     * Decrypt a string or a file
     * @param string $string
     * @param string $filename
     * @return string
     */
    public function decrypt($string, $filename = null){

        // Check if the string is a file
        if(is_file($string)){

            // Decrypt the file and return it
            if($filename == null){ str_replace($this->Cipher,'',$string); }
            $blob = openssl_decrypt( base64_decode( file_get_contents($string) ), $this->Cipher, $this->PublicHash, 0, $this->PrivateHash );
            $file = fopen($filename, 'w');
            fwrite($file, $blob);
            fclose($file);
            return $filename;
        } elseif(is_string($string)) {

            // Decrypt the string and return it
            return openssl_decrypt( base64_decode( $string ), $this->Cipher, $this->PublicHash, 0, $this->PrivateHash );
        } else {

            // Log the error
            $this->Logger->error('Failed to decrypt the string [' . $string . ']');

            // Return false
            return false;
        }
    }

    /**
     * Check if the library is installed.
     *
     * @return bool
     */
    public function isInstalled(){

        // Retrieve the path of this class
        $reflector = new ReflectionClass($this);
        $path = $reflector->getFileName();

        // Modify the path to point to the config directory
        $path = str_replace('src/Logger.php', 'config/', $path);

        // Add the requirements to the Configurator
        $this->Configurator->add('requirements', $path . 'requirements.cfg');

        // Retrieve the list of required modules
        $modules = $this->Configurator->get('requirements','modules');

        // Check if the required modules are installed
        foreach($modules as $module){

            // Check if the class exists
            if (!class_exists($module)) {
                return false;
            }

            // Initialize the class
            $class = new $module();

            // Check if the method exists
            if(method_exists($class, isInstalled)){

                // Check if the class is installed
                if(!$class->isInstalled()){
                    return false;
                }
            }
        }

        // Return true
        return true;
    }
}

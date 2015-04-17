<?php
namespace ParagonIE\Pharaoh;

class Pharaoh
{
    public $phar;
    public $tmp;
    public static $stubfile;
    
    public function __construct($file, $alias = null)
    {
        if (!\is_readable($file)) {
            throw new PharError($file.' cannot be read');
        }
        if (\ini_get('phar.readonly') == '1') {
            throw new PharError("Pharaoh cannot be used if phar.readonly is enabled in php.ini\n");
        }
        
        // Set the static variable here
        if (empty(self::$stubfile)) {
            self::$stubfile = \bin2hex(
                \mcrypt_create_iv(9, MCRYPT_DEV_URANDOM)
            ).'.pharstub';
        }
        
        $this->phar = new \Phar($file);
        
        if (empty($alias)) {
            // We have to give every one a different alias, or it pukes.
            $alias = \bin2hex(
                \mcrypt_create_iv(8, MCRYPT_DEV_URANDOM)
            ).'.phar';
        }
        $this->phar->setAlias($alias);
        
        // Make a random folder in /tmp
        $this->tmp = \tempnam(\sys_get_temp_dir(), 'phr_');
        \unlink($this->tmp);
        \mkdir($this->tmp, 0777, true);
        
        // Let's extract to our temporary directory
        $this->phar->extractTo($this->tmp);
        
        // Also extract the stub
        \file_put_contents(
            $this->tmp.'/'.self::$stubfile,
            $this->phar->getStub()
        );
    }
}

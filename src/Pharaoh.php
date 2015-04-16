<?php
namespace ParagonIE\Pharaoh;

class Pharaoh
{
    public $phar;
    public $tmp;
    
    public function __construct($file, $alias)
    {
        if (!\is_readable($file)) {
            throw new PharError($file.' cannot be read');
        }
        $this->phar = new \Phar($file);
        $this->phar->setAlias($alias);
        
        // Make a random folder in /tmp
        $this->tmp = \tempnam(\sys_get_temp_dir(), 'phr_');
        \unlink($this->tmp);
        \mkdir($this->tmp, 0777, true);
        
        // Let's extract to our temporary directory
        $this->phar->extractTo($this->tmp);
    }
}

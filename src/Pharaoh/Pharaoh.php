<?php
declare(strict_types=1);
namespace ParagonIE\Pharaoh;

use ParagonIE\ConstantTime\Hex;

/**
 * Class Pharaoh
 * @package ParagonIE\Pharaoh
 */
class Pharaoh
{
    /**
     * @var \Phar
     */
    public $phar;

    /**
     * @var string
     */
    public $tmp;

    /**
     * @var string
     */
    public static $stubfile;

    /**
     * Pharaoh constructor.
     * @param string $file
     * @param string $alias
     * @throws PharError
     * @throws \Error
     * @throws \Exception
     */
    public function __construct(string $file, $alias = null)
    {
        if (!\is_readable($file)) {
            throw new PharError($file.' cannot be read');
        }
        if (\ini_get('phar.readonly') == '1') {
            throw new PharError("Pharaoh cannot be used if phar.readonly is enabled in php.ini\n");
        }
        
        // Set the static variable here
        if (empty(self::$stubfile)) {
            self::$stubfile = Hex::encode(\random_bytes(12)).'.pharstub';
        }
        
        $this->phar = new \Phar($file);
        
        if (empty($alias)) {
            // We have to give every one a different alias, or it pukes.
            $alias = Hex::encode(\random_bytes(16)).'.phar';
        }
        $this->phar->setAlias($alias);
        
        // Make a random folder in /tmp
        /** @var string|bool $tmp */
        $tmp = \tempnam(\sys_get_temp_dir(), 'phr_');
        if (!\is_string($tmp)) {
            throw new \Error('Could not create temporary file');
        }
        $this->tmp = $tmp;
        \unlink($this->tmp);
        \mkdir($this->tmp, 0755, true);
        
        // Let's extract to our temporary directory
        $this->phar->extractTo($this->tmp);
        
        // Also extract the stub
        \file_put_contents(
            $this->tmp . '/' . self::$stubfile,
            $this->phar->getStub()
        );
    }
}

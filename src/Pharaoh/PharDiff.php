<?php
declare(strict_types=1);
namespace ParagonIE\Pharaoh;
use ParagonIE\ConstantTime\Hex;

/**
 * Class PharDiff
 * @package ParagonIE\Pharaoh
 */
class PharDiff
{
    /**
     * @var array<string, string>
     */
    protected $c = [
        '' => "\033[0;39m",
        'red'       => "\033[0;31m",
        'green'     => "\033[0;32m",
        'blue'      => "\033[1;34m",
        'cyan'      => "\033[1;36m",
        'silver'    => "\033[0;37m",
        'yellow'    => "\033[0;93m"
    ];

    /** @var array<int, Pharaoh> */
    private $phars = [];

    /** @var bool $verbose */
    private $verbose = false;
    
    /**
     * Constructor uses dependency injection.
     * 
     * @param \ParagonIE\Pharaoh\Pharaoh $pharA
     * @param \ParagonIE\Pharaoh\Pharaoh $pharB
     */
    public function __construct(Pharaoh $pharA, Pharaoh $pharB)
    {
        $this->phars = [$pharA, $pharB];
    }
    
    /**
     * Prints a git-formatted diff of the two phars.
     *
     * @psalm-suppress ForbiddenCode
     * @return int
     */
    public function printGitDiff(): int
    {
        // Lazy way; requires git. Will replace with custom implementaiton later.
        
        $argA = \escapeshellarg($this->phars[0]->tmp);
        $argB = \escapeshellarg($this->phars[1]->tmp);
        /** @var string $diff */
        $diff = `git diff --no-index $argA $argB`;
        echo $diff;
        if (empty($diff) && $this->verbose) {
            echo 'No differences encountered.', PHP_EOL;
            return 0;
        }
        return 1;
    }
    
    /**
     * Prints a GNU diff of the two phars.
     *
     * @psalm-suppress ForbiddenCode
     * @return int
     */
    public function printGnuDiff(): int
    {
        // Lazy way. Will replace with custom implementaiton later.
        $argA = \escapeshellarg($this->phars[0]->tmp);
        $argB = \escapeshellarg($this->phars[1]->tmp);
        /** @var string $diff */
        $diff = `diff $argA $argB`;
        echo $diff;
        if (empty($diff) && $this->verbose) {
            echo 'No differences encountered.', PHP_EOL;
            return 0;
        }
        return 1;
    }
    
    /**
     * Get hashes of all of the files in the two arrays.
     * 
     * @param string $algo
     * @param string $dirA
     * @param string $dirB
     * @return array<int, array<mixed, string>>
     * @throws \SodiumException
     */
    public function hashChildren(string $algo,string  $dirA, string $dirB)
    {
        /**
         * @var string $a
         * @var string $b
         */
        $a = $b = '';
        $filesA = $this->listAllFiles($dirA);
        $filesB = $this->listAllFiles($dirB);
        $numFiles = \max(\count($filesA), \count($filesB));
        
        // Array of two empty arrays
        $hashes = [[], []];
        for ($i = 0; $i < $numFiles; ++$i) {
            $thisFileA = (string) $filesA[$i];
            $thisFileB = (string) $filesB[$i];
            if (isset($filesA[$i])) {
                $a = \preg_replace('#^'.\preg_quote($dirA, '#').'#', '', $thisFileA);
                if (isset($filesB[$i])) {
                    $b = \preg_replace('#^'.\preg_quote($dirB, '#').'#', '', $thisFileB);
                } else {
                    $b = $a;
                }
            } elseif (isset($filesB[$i])) {
                $b = \preg_replace('#^'.\preg_quote($dirB, '#').'#', '', $thisFileB);
                $a = $b;
            }
            
            if (isset($filesA[$i])) {
                // A exists
                if (\strtolower($algo) === 'blake2b') {
                    $hashes[0][$a] = Hex::encode(\ParagonIE_Sodium_File::generichash($thisFileA));
                } else {
                    $hashes[0][$a] = \hash_file($algo, $thisFileA);
                }
            } elseif (isset($filesB[$i])) {
                // A doesn't exist, B does
                $hashes[0][$a] = '';
            }
            
            if (isset($filesB[$i])) {
                // B exists
                if (\strtolower($algo) === 'blake2b') {
                    $hashes[1][$b] = Hex::encode(\ParagonIE_Sodium_File::generichash($thisFileB));
                } else {
                    $hashes[1][$b] = \hash_file($algo, $thisFileB);
                }
            } elseif (isset($filesA[$i])) {
                // B doesn't exist, A does
                $hashes[1][$b] = '';
            }
        }
        return $hashes;
    }
    
    
    /**
     * List all the files in a directory (and subdirectories)
     *
     * @param string $folder - start searching here
     * @param string $extension - extensions to match
     * @return array
     */
    private function listAllFiles($folder, $extension = '*')
    {
        /**
         * @var array<mixed, string> $fileList
         * @var string $i
         * @var string $file
         * @var \RecursiveDirectoryIterator $dir
         * @var \RecursiveIteratorIterator $ite
         */
        $dir = new \RecursiveDirectoryIterator($folder);
        $ite = new \RecursiveIteratorIterator($dir);
        if ($extension === '*') {
            $pattern = '/.*/';
        } else {
            $pattern = '/.*\.' . $extension . '$/';
        }
        $files = new \RegexIterator($ite, $pattern, \RegexIterator::GET_MATCH);

        /** @var array<string, string> $fileList */
        $fileList = [];

        /**
         * @var string[] $fileSub
         */
        foreach($files as $fileSub) {
            $fileList = \array_merge($fileList, $fileSub);
        }

        /**
         * @var string $i
         * @var string $file
         */
        foreach ($fileList as $i => $file) {
            if (\preg_match('#/\.{1,2}$#', (string) $file)) {
                unset($fileList[$i]);
            }
        }
        return \array_values($fileList);
    }

    /**
     * Prints out all of the differences of checksums of the files contained
     * in both PHP archives.
     *
     * @param string $algo
     * @return int
     * @throws \SodiumException
     */
    public function listChecksums(string $algo = 'sha384'): int
    {
        list($pharA, $pharB) = $this->hashChildren(
            $algo,
            $this->phars[0]->tmp,
            $this->phars[1]->tmp
        );

        $diffs = 0;
        /** @var string $i */
        foreach (\array_keys($pharA) as $i) {
            if (isset($pharA[$i]) && isset($pharB[$i])) {
                // We are NOT concerned about local timing attacks.
                if ($pharA[$i] !== $pharB[$i]) {
                    ++$diffs;
                    echo "\t", (string) $i,
                    "\n\t\t", $this->c['red'], $pharA[$i], $this->c[''],
                    "\t", $this->c['green'], $pharB[$i], $this->c[''],
                    "\n";
                } elseif (!empty($pharA[$i]) && empty($pharB[$i])) {
                    ++$diffs;
                    echo "\t", (string) $i,
                    "\n\t\t", $this->c['red'], $pharA[$i], $this->c[''],
                    "\t", \str_repeat('-', \strlen($pharA[$i])),
                    "\n";
                } elseif (!empty($pharB[$i]) && empty($pharA[$i])) {
                    ++$diffs;
                    echo "\t", (string) $i,
                    "\n\t\t", \str_repeat('-', \strlen($pharB[$i])),
                    "\t", $this->c['green'], $pharB[$i], $this->c[''],
                    "\n";
                }
            }
        }
        if ($diffs === 0) {
            if ($this->verbose) {
                echo 'No differences encountered.', PHP_EOL;
            }
            return 0;
        }
        return 1;
    }

    /**
     * Verbose mode says something when there are no differences.
     * By default, you can just check the return value.
     *
     * @param bool $value
     * @return void
     */
    public function setVerbose(bool $value)
    {
        $this->verbose = $value;
    }
}

<?php
namespace ParagonIE\Pharaoh;

class PharDiff
{
    private $phars = [];
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
     * Prints a git-formatted diff of the two phars
     */
    public function printGitDiff()
    {
        // Lazy way; requires git. Will replace with custom implementaiton later.
        
        $argA = \escapeshellarg($this->phars[0]->tmp);
        $argB = \escapeshellarg($this->phars[1]->tmp);
        echo `git diff --no-index $argA $argB`;
        exit;
    }
    
    public function listChecksums($algo)
    {
        list($pharA, $pharB) = $this->hashChildren(
            $algo,
            $this->phars[0]->tmp,
            $this->phars[1]->tmp
        );
        
        $count = count($pharA);
        for ($i = 0; $i < $count; ++$i) {
            
        }
    }
    
    /**
     * Get hashes of all of the files in the two arrays.
     * 
     * @param type $algo
     * @param type $dirA
     * @param type $dirB
     * @return type
     */
    public function hashChildren($algo, $dirA, $dirB)
    {
        $filesA = $this->listAllFiles($dirA);
        $filesB = $this->listAllFiles($dirB);
        $numFiles = \max(\count($filesA), \count($filesB));
        
        // Array of two empty arrays
        $hashes = [[], []];
        for ($i = 0; $i < $numFiles; ++$i) {
            if (isset($filesA)) {
                $a = \preg_replace('#^'.\preg_quote($dirA, '#').'#', '', $filesA[$i]);
            }
            if (isset($filesB)) {
                $b = \preg_replace('#^'.\preg_quote($dirB, '#').'#', '', $filesB[$i]);
            }
            
            if (isset($filesA[$i])) {
                // A exists
                $hashes[0][$a] = \hash_file($algo, $filesA[$i]);
            } elseif (isset($filesB[$i])) {
                // A doesn't exist, B does
                $hashes[0][$b] = null;
            }
            
            if (isset($filesB[$i])) {
                // B exists
                $hashes[1][$b] = \hash_file($algo, $filesB[$i]);
            } elseif (isset($filesA[$i])) {
                // B doesn't exist, A does
                $hashes[1][$a] = null;
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
        $dir = new \RecursiveDirectoryIterator($folder);
        $ite = new \RecursiveIteratorIterator($dir);
        if ($extension === '*') {
            $pattern = '/.*/';
        } else {
            $pattern = '/.*\.' . $extension . '$/';
        }
        $files = new \RegexIterator($ite, $pattern, \RegexIterator::GET_MATCH);
        $fileList = [];
        foreach($files as $file) {
            $fileList = \array_merge($fileList, $file);
        }
        foreach ($fileList as $i => $file) {
            if (\preg_match('#/\.{1,2}$#', $file)) {
                unset($fileList[$i]);
            }
        }
        return \array_values($fileList);
    }
}

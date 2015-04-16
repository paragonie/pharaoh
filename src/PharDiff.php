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
}

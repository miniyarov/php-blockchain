<?php

namespace Blockchain;

use Blockchain\Exception\MiningException;

class ProofOfWork
{
    const TARGET_ZEROS = 4;

    const MAX_NONCE = PHP_INT_MAX;

    /**
     * @var Block
     */
    public $block;

    public function __construct(Block $block)
    {
        $this->block = $block;
        $this->block->targetZeros = self::TARGET_ZEROS;
    }

    private function prepare(int $nonce)
    {
        return $this->block->prevBlockHash .
            $this->block->hashTransactions() .
            dechex($this->block->timestamp) .
            dechex($this->block->targetZeros) .
            dechex($nonce);
    }

    public function run()
    {
        $nonce = 0;

        echo "Mining new block" . PHP_EOL;

        while ($nonce < self::MAX_NONCE) {
            $data = $this->prepare($nonce);

            $hash = hash('sha256', $data);

            if ($this->satisfiesTarget($hash)) {
                echo $hash . PHP_EOL;
                $this->block->nonce = $nonce;
                $this->block->hash = $hash;

                return $this->block;
            }

            $nonce++;
        }

        throw new MiningException("Could not mine the block");
    }

    private function satisfiesTarget($hash)
    {
        return substr($hash, 0, self::TARGET_ZEROS) === str_repeat('0', self::TARGET_ZEROS);
    }

    public function validate()
    {
        $data = $this->prepare($this->block->nonce);

        return $this->satisfiesTarget(hash('sha256', $data));
    }
}
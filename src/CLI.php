<?php

namespace Blockchain;

class CLI
{
    /** @var Blockchain */
    public $blockchain;

    public function __construct(Blockchain $blockchain)
    {
        $this->blockchain = $blockchain;
    }

    public function run($argv)
    {
        switch ($argv[1]) {
            case 'addblock':
                $this->addBlock($argv[2]);
                break;
            case 'printchain':
                $this->printChain();
                break;
            default:
                echo <<<USAGE
Usage:
~$ php blockchain.php addblock "Ulugbek'e 10 BTC gönder"
~$ php blockchain.php printchain

USAGE;
                exit(1);
        }
    }

    private function addBlock(string $data)
    {
        $this->blockchain->addBlock($data);
        echo "Successfully added!" . PHP_EOL;
    }

    private function printChain()
    {
        foreach ($this->blockchain->getBlocks() as $block) {
            echo "Prev. hash: {$block->prevBlockHash}\n";
            echo "Data: {$block->data}\n";
            echo "Hash: {$block->hash}\n";
            echo "PoW: " . var_export((new ProofOfWork($block))->validate(), true) . PHP_EOL . PHP_EOL;
        }
    }
}
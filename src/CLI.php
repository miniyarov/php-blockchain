<?php

namespace Blockchain;

class CLI
{
    /** @var Blockchain */
    public $blockchain;

    public function run($argv)
    {
        switch ($argv[1]) {
            case 'getbalance':
                $this->getBalance($argv[2]);
                break;
            case 'send':
                $this->send($argv[2], $argv[3], (int)$argv[4]);
            case 'createblockchain':
                $this->createBlockchain($argv[2]);
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

    private function getBalance(string $address)
    {
        $this->blockchain = Blockchain::getInstance($address);

        $balance = 0;

        foreach ($this->blockchain->findUTXO($address) as $output) {
            $balance += $output->value;
        }

        echo "Balance of {$address}: {$balance}" . PHP_EOL;
    }

    private function send(string $from, string $to, int $amount)
    {
        $this->blockchain = Blockchain::getInstance($from);

        $transaction = $this->blockchain->newUTXOTransaction($from, $to, $amount);
        $this->blockchain->mineBlock([$transaction]);

        echo "Success" . PHP_EOL;
    }

    private function createBlockchain(string $address)
    {
        $this->blockchain = Blockchain::getInstance($address);
    }

    private function printChain()
    {
        $this->blockchain = Blockchain::getInstance('');

        foreach ($this->blockchain->getBlocks() as $block) {
            $txn = $block->transactions[0]->vOut[0];

            echo "Prev. hash: {$block->prevBlockHash}\n";
            echo "Transaction: {$txn->scriptPubKey}: {$txn->value}\n";
            echo "Hash: {$block->hash}\n";
            echo "PoW: " . var_export((new ProofOfWork($block))->validate(), true) . PHP_EOL . PHP_EOL;
        }
    }
}
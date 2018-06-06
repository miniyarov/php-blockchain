<?php

namespace Blockchain;

class Blockchain
{
    const GENESIS_COINBASE_DATA = 'The Times 03/Jan/2009 Chancellor on brink of second bailout for banks';

    public $lastHash;

    private $db;

    private function __construct(string $address)
    {
        $this->db = new \PDO('sqlite:blockchain.db');
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $this->db->exec(<<<QUERY
CREATE TABLE IF NOT EXISTS blocks (
    id INTEGER PRIMARY KEY,
    hash VARCHAR(64),
    block TEXT
)
QUERY
        );

        $this->lastHash = $this->db
            ->query('SELECT hash FROM blocks ORDER BY id DESC LIMIT 1')
            ->fetchColumn();

        if (!$this->lastHash) {
            echo "Creating Genesis Block..." . PHP_EOL;
            $coinbase = Transaction::newCoinbaseTX($address, self::GENESIS_COINBASE_DATA);
            $genesis = Block::create([$coinbase], '');

            $query = $this->db->prepare('INSERT INTO blocks (hash, block) VALUES (?, ?)');
            $query->execute([$genesis->hash, json_encode($genesis)]);

            $this->lastHash = $genesis->hash;
        }
    }

    public static function getInstance(string $address)
    {
        return new self($address);
    }

    public function mineBlock(array $transactions)
    {
        $block = Block::create($transactions, $this->lastHash);

        $query = $this->db->prepare('INSERT INTO blocks (hash, block) VALUES (?, ?)');
        $query->execute([$block->hash, json_encode($block)]);

        $this->lastHash = $block->hash;
    }

    public function newUTXOTransaction(string $from, string $to, int $amount)
    {
        [$spendableAmount, $unspentOutputs] = $this->findSpendableOutputs($from, $amount);

        foreach ($unspentOutputs as $txId => $unspentOutputKeys) {
            foreach ($unspentOutputKeys as $unspentOutputKey) {
                $inputs[] = new TXInput($txId, $unspentOutputKey, $from);
            }
        }

        $outputs[] = new TXOutput($amount, $to);
        if ($spendableAmount > $amount) {
            $outputs[] = new TXOutput($spendableAmount - $amount, $from);
        }

        return (new Transaction(null, $inputs, $outputs))->setId();
    }

    public function findSpendableOutputs(string $address, int $amount)
    {
        $unspentTransactions = $this->findUnspentTransactions($address);
        $spendableAmount = 0;
        $unspentOutputs = [];

        foreach ($unspentTransactions as $transaction) {
            foreach ($transaction->vOut as $outputKey => $output) {
                if ($output->canBeUnlockedWith($address) && $spendableAmount < $amount) {
                    $spendableAmount += $output->value;
                    $unspentOutputs[$transaction->id][] = $outputKey;
                }

                if ($spendableAmount > $amount) {
                    break 2;
                }
            }
        }

        if ($spendableAmount < $amount) {
            throw new \RuntimeException("Not enough funds");
        }

        return [$spendableAmount, $unspentOutputs];
    }

    public function getBlocks()
    {
        $results = $this->db->query('SELECT block FROM blocks ORDER BY id DESC', \PDO::FETCH_ASSOC);

        foreach ($results as $result) {
            yield Block::jsonDeserialize(json_decode($result['block']));
        }
    }

    public function findUnspentTransactions(string $address)
    {
        $spentTXOs = [];
        $unspentTXs = [];

        foreach ($this->getBlocks() as $block) {
            foreach ($block->transactions as $transaction) {
                foreach ($transaction->vOut as $outputKey => $output) {
                    if (isset($spentTXOs[$transaction->id])) {
                        foreach ($spentTXOs[$transaction->id] as $outputId) {
                            if ($outputKey === $outputId) {
                                continue 2;
                            }
                        }
                    }

                    if ($output->canBeUnlockedWith($address)) {
                        $unspentTXs[] = $transaction;
                    }
                }

                if ($transaction->isCoinbase() === false) {
                    foreach ($transaction->vIn as $input) {
                        if ($input->canUnlockOutputWith($address)) {
                            $spentTXOs[$input->txId][] = $input->vOut;
                        }
                    }
                }
            }
        }

        return $unspentTXs;
    }

    public function findUTXO(string $address)
    {
        $UTXOs = [];
        $unspentTransactions = $this->findUnspentTransactions($address);

        foreach ($unspentTransactions as $transaction) {
            foreach ($transaction->vOut as $output) {
                if ($output->canBeUnlockedWith($address)) {
                    $UTXOs[] = $output;
                }
            }
        }

        return $UTXOs;
    }
}
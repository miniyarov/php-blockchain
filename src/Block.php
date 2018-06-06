<?php

namespace Blockchain;

class Block implements \JsonSerializable
{
    /** @var integer */
    public $timestamp;

    /** @var array|Transaction[] */
    public $transactions;

    /** @var string */
    public $prevBlockHash;

    /** @var string */
    public $hash;

    /** @var string */
    public $targetZeros;

    /** @var integer */
    public $nonce;

    public function __construct(string $timestamp, array $transactions, string $prevBlockHash)
    {
        $this->timestamp = $timestamp;
        $this->transactions = $transactions;
        $this->prevBlockHash = $prevBlockHash;
        $this->nonce = 0;
    }

    public static function create(array $transactions, string $prevBlockHash)
    {
        $block = (new self(time(), $transactions, $prevBlockHash));

        return (new ProofOfWork($block))->run();
    }

    public function jsonSerialize()
    {
        return [
            $this->timestamp,
            $this->transactions,
            $this->prevBlockHash,
            $this->hash,
            $this->targetZeros,
            $this->nonce
        ];
    }

    public static function jsonDeserialize(array $decoded)
    {
        [$timestamp, $transactions, $prevBlockHash, $hash, $targetZeros, $nonce] = $decoded;

        $transactions = array_map(function ($transaction) {
            return Transaction::jsonDeserialize($transaction);
        }, $transactions);

        $block = new self($timestamp, $transactions, $prevBlockHash);
        $block->hash = $hash;
        $block->targetZeros = $targetZeros;
        $block->nonce = $nonce;

        return $block;
    }

    public function hashTransactions()
    {
        $txHashes = '';
        foreach ($this->transactions as $transaction) {
            $txHashes .= $transaction->id;
        }

        return hash('sha256', $txHashes);
    }
}
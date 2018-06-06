<?php
/**
 * Created by PhpStorm.
 * User: ulugbek
 * Date: 5.06.2018
 * Time: 21:58
 */

namespace Blockchain;


class Transaction implements \JsonSerializable
{
    const SUBSIDY = 10;

    /** @var string */
    public $id;

    /** @var array|TXInput[] */
    public $vIn;

    /** @var array|TXOutput[] */
    public $vOut;

    public function __construct(?string $id, array $vIn, array $vOut)
    {
        $this->id = $id;
        $this->vIn = $vIn;
        $this->vOut = $vOut;
    }

    public static function newCoinbaseTX($to, string $data)
    {
        if ($data == '') {
            $data = "Reward to {$to}";
        }

        $txin = new TXInput(null, -1, $data);
        $txout = new TXOutput(self::SUBSIDY, $to);

        return (new self(null, [$txin], [$txout]))
            ->setId();
    }

    public function isCoinbase()
    {
        return count($this->vIn) === 1 && $this->vIn[0]->txId === null && $this->vIn[0]->vOut === -1;
    }

    public function setId()
    {
        $this->id = hash('sha256', serialize($this));

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'vIn' => $this->vIn,
            'vOut' => $this->vOut
        ];
    }

    public static function jsonDeserialize($transaction)
    {
        $vIn = array_map(function ($input) {
            return TXInput::jsonDeserialize($input);
        }, $transaction->vIn);

        $vOut = array_map(function ($output) {
            return TXOutput::jsonDeserialize($output);
        }, $transaction->vOut);

        return new self($transaction->id, $vIn, $vOut);
    }
}
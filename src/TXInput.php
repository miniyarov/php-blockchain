<?php
/**
 * Created by PhpStorm.
 * User: ulugbek
 * Date: 5.06.2018
 * Time: 22:03
 */

namespace Blockchain;


class TXInput implements \JsonSerializable
{
    /** @var string */
    public $txId;

    /** @var integer */
    public $vOut;

    /** @var string */
    public $scriptSig;

    public function __construct($txId, $vOut, $scriptSig)
    {
        $this->txId = $txId;
        $this->vOut = $vOut;
        $this->scriptSig = $scriptSig;
    }

    public function canUnlockOutputWith(string $unlockingData)
    {
        return $this->scriptSig === $unlockingData;
    }

    public function jsonSerialize()
    {
        return [
            'txId' => $this->txId,
            'vOut' => $this->vOut,
            'scriptSig' => $this->scriptSig
        ];
    }

    public static function jsonDeserialize($input)
    {
        return new self($input->txId, $input->vOut, $input->scriptSig);
    }
}
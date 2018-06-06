<?php
/**
 * Created by PhpStorm.
 * User: ulugbek
 * Date: 5.06.2018
 * Time: 22:03
 */

namespace Blockchain;


class TXOutput implements \JsonSerializable
{
    /** @var integer */
    public $value;

    /** @var string */
    public $scriptPubKey;

    public function __construct($value, $scriptPubKey)
    {
        $this->value = $value;
        $this->scriptPubKey = $scriptPubKey;
    }

    public function canBeUnlockedWith(string $unlockingData)
    {
        return $this->scriptPubKey === $unlockingData;
    }

    public function jsonSerialize()
    {
        return [
            'value' => $this->value,
            'scriptPubKey' => $this->scriptPubKey
        ];
    }

    public static function jsonDeserialize($output)
    {
        return new self($output->value, $output->scriptPubKey);
    }
}
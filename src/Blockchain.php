<?php

namespace Blockchain;

class Blockchain
{
    public $lastHash;

    private $db;

    public function __construct()
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
            echo "No block found. Creating genesis block..." . PHP_EOL;
            $genesis = newBlock('Genesis Block', '');

            $query = $this->db->prepare('INSERT INTO blocks (hash, block) VALUES (?, ?)');
            $query->execute([$genesis->hash, json_encode($genesis)]);

            $this->lastHash = $genesis->hash;
        }
    }

    public function addBlock(string $data)
    {
        $block = newBlock($data, $this->lastHash);

        $query = $this->db->prepare('INSERT INTO blocks (hash, block) VALUES (?, ?)');
        $query->execute([$block->hash, json_encode($block)]);

        $this->lastHash = $block->hash;
    }

    public function getBlocks()
    {
        $results = $this->db->query('SELECT block FROM blocks', \PDO::FETCH_ASSOC);

        foreach ($results as $result) {
            yield Block::jsonDeserialize(json_decode($result['block']));
        }
    }
}
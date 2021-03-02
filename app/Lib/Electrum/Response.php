<?php

namespace App\Lib\Electrum;

class Response {
    /**
     * @var int|string
     */
    private $id;

    /**
     * @var mixed
     */
    private $result;

    /**
     * @param int|string $id
     * @param mixed $result
     */
    public function __construct($id, $result)
    {
        $this->id = $id;
        $this->result = $result;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return string
     */
    public function write()
    {
        return json_encode([
                'id' => $this->id,
                'result' => $this->result
            ]) . "\n";
    }
}
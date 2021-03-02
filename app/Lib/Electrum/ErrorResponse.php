<?php


namespace App\Lib\Electrum;

class ErrorResponse extends \Exception {
    /**
     * @var string
     */
    private $id;

    /**
     * @param string $id
     * @param int $error
     */
    public function __construct($id, $error)
    {
        $this->id = $id;
        parent::__construct($error);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function write()
    {
        return json_encode([
                'id' => $this->id,
                'error' => $this->getMessage()
            ]) . "\n";
    }
}
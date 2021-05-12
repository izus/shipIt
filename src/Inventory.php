<?php namespace Kattatzu\ShipIt;

use Exception;

class Inventory
{
    /**
     * @var array atributos del inventory
     */
    private $data = [];
    private $shipIt;

    /**
     * Constructor
     *
     * @param null $response
     * @param ShipIt $shipItIntance
     */
    public function __construct($response = null, $shipItIntance)
    {
        if ($response) {
            $this->data = (array)$response;
        }

        $this->shipIt = $shipItIntance;

        if(empty($this->data['id'])){
            throw new Exception("Inventory not found");
        }
    }

    /**
     * Retorna los datos del inventory en un array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Retorna un atributo del inventory
     *
     * @param $varName
     * @return mixed|null
     */
    public function __get($varName)
    {
        return isset($this->data[$varName]) ? $this->data[$varName] : null;
    }
}
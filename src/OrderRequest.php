<?php namespace Kattatzu\ShipIt;

use Illuminate\Support\Facades\Log;
use Kattatzu\ShipIt\Exception\AttributeNotValidException;
use Exception;

class OrderRequest
{

    //ORDER SELLER
    const SELLER_SHOPIFY = 'shopify';
    const SELLER_WOOCOMMERCE = 'woocommerce';
    const SELLER_PRESTASHOP = 'prestashop';

    // Tamaños del envio
    const SIZE_SMALL = 'Pequeño (10x10x10cm)';
    const SIZE_MEDIUM = 'Mediano (30x30x30cm)';
    const SIZE_LARGE = 'Grande (50x50x50cm)';
    const SIZE_XLARGE = 'Muy Grande (>60x60x60cm)';
    // Empaque
    const PACKING_NONE = 'Sin empaque';
    const PACKING_PAPERBOARD = 'Caja de Cartón';
    const PACKING_PLASTIC = 'Film Plástico';
    const PACKING_BURBLE = 'Caja + Burbuja';
    const PACKING_KRAFT = 'Papel Kraft';

    // Destino del envio
    const DESTINY_HOME = 'Domicilio';
    const DESTINY_BRANCH = 'Sucursal';
    // Couriers
    const COURIER_NONE = '';
    const COURIER_CHILEXPRESS = 'Chilexpress';
    const COURIER_STARKEN = 'Starken';
    const COURIER_MUVSMART = 'Muvsmart';
    const COURIER_CHILEPARCELS = 'Chileparcels';
    const COURIER_MOTOPARTNER = 'Motopartner';


    private $validProperties = array(
        'order_seller',
        'reference',
        'full_name',
        'email',
        'items_count',
        'cellphone',
        'is_payable',
        'packing',
        //'shipping_type',
        'destiny',
        'courier_for_client',
        'approx_size',
        'address_commune_id',
        'address_street',
        'address_number',
        'address_complement',
        /*'width',
        'height',
        'length',
        'weight',*/

        'inventory_activity',
    );

    /**
     * @var array atributos de la solicitud
     */
    private $data = [

        'order_seller' => null,
        'reference'          => null, //max 12 chars
        'full_name'          => null,
        'email'              => null,
        'items_count'        => 0,
        'cellphone'          => null,
        'is_payable'         => false,
        'packing'            => null,
        'shipping_type'      => 'Normal', //opcion unica
        'destiny'            => null,
        'courier_for_client' => null,
        'approx_size'        => null,
        'address_commune_id' => null,
        'address_street'     => null,
        'address_number'     => null,
        'address_complement' => null,
        /*'width'              => null,
        'height'             => null,
        'length'             => null,
        'weight'             => null,*/

        'inventory_activity' => null,
    ];

    /**
     * Constructor
     *
     * @param array|null $data
     * @throws AttributeNotValidException
     */
    public function __construct(array $data = null)
    {
        if (is_array($data)) {
            foreach ($data as $varName => $value) {
                $this->__set($varName, $value);
            }
        }
    }

    /**
     * Retorna los datos de la solicitud en un array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Retorna los datos en el formato que pide ShipIt
     *
     * @return array
     */
    public function toShipItFormat($environment = 'production')
    {
        $data = $this->data;

        if ($environment === ShipIt::ENV_DEVELOPMENT) {
            //replace instead of concat to avoid going over character limit
            $prefix = 'TEST-';
            $data['reference'] = $prefix . substr(
                $data['reference'], strlen($prefix), strlen($data['reference'])
            );
        }

        $data['mongo_order_seller'] = $data['order_seller'];
        unset($data['order_seller']);

        $data['address_attributes']['commune_id'] = $data['address_commune_id'];
        $data['address_attributes']['street'] = $data['address_street'];
        $data['address_attributes']['number'] = $data['address_number'];
        $data['address_attributes']['complement'] = $data['address_complement'];

        unset($data['address_commune_id']);
        unset($data['address_street']);
        unset($data['address_number']);
        unset($data['address_complement']);

        if(empty($data['inventory_activity'])){
            unset($data['inventory_activity']);
        }

        return $data;
    }

    /**
     * Retorna un atributo de la solicitud
     *
     * @param $varName
     * @return mixed|null
     */
    public function __get($varName)
    {
        return isset($this->data[$varName]) ? $this->data[$varName] : null;
    }

    /**
     * Setea el valor de un atributo de la solicitud
     *
     * @param $varName
     * @param $value
     * @return void
     */
    public function __set($varName, $value)
    {
        if (!in_array($varName, $this->validProperties)) {
            throw new AttributeNotValidException($varName);
        }

        //validate reference length
        if($varName == 'reference' && strlen($value) >= 15){
            throw new Exception("Attribute 'reference' must be under 15 characters in length.");
        }

        $this->data[$varName] = $value;
    }
}

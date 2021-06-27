<?php
require_once 'dbCredentials.php';
require_once 'db/OrderModel.php';

/**
 * Class ShipmentModel handles everything in regards to shipments and the database.
 */
class ShipmentModel {
    protected $db;

    public function __construct()
    {
        $this->db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8',
            DB_USER, DB_PWD,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }

    /**
     * Creates a new shipment in the database
     * @param array $resource data/body with information about the shipment to be created.
     * @return array a message to returned to the client after the production plan is created
     */
    function createShipment(array $resource): array
    {
        $res = array();
        $this->db->beginTransaction();
        $rec = $this->verifyResource($resource, true);
        if($rec['error_code'] != RESTConstants::HTTP_OK){
            return $rec;
        }

        $query = "INSERT INTO `shipments` (order_nr, franchise_name, shipping_address, state) VALUES (:order_nr, :franchise_name, :shipping_address, 'ready')";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':order_nr', $resource['order_nr']);
        $stmt->bindValue(':franchise_name', $resource['franchise_name']);
        $stmt->bindValue(':shipping_address', $resource['shipping_address']);
        $stmt->execute();
        $this->db->commit();
        $res['message'] = 'shipment created for order with order number: ' . $resource['order_nr'];
        return $res;
    }

    /**
     * Creates a new shipment in the database
     * @param int $id is the id of the shipment to update.
     * @param array $resource an optional set of conditions that the retrieved
     * @return array a message to returned to the client after the shipment is picked up
     */
    function pickupShipment(int $id, array $resource): array
    {
        $res = array();
        $this->db->beginTransaction();
        $rec = $this->verifyResourcePickup($resource, true);
        if($rec['error_code'] != RESTConstants::HTTP_OK){
            return $rec;
        }

        $query = "UPDATE shipments SET transporter_name = :transporter_name, pickup_date = CURDATE(), driver_id = :driver_id WHERE order_nr = :order_nr";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':order_nr', $id);
        $stmt->bindValue(':transporter_name', $resource['transporter_name']);
        $stmt->bindValue(':driver_id', $resource['driver_id']);
        $stmt->execute();
        $this->db->commit();

        $model = new OrderModel();
        $res = $model->modifyOrder($id, $resource);
        $res['message'] = 'order ' . $id . ' has been set to picked up';
        return $res;
    }

    /**
     * verifyResource checks that every function which has body requirements has the correct fields.
     * @param array $resource is the data to verify
     * @param bool $ignoreId if id should be ignored (is not in use)
     * @return array returns an array of error_code, possibly an error message as well
     */
    function verifyResource(array $resource, bool $ignoreId = false): array
    {
        $res = array();
  
        if (!array_key_exists('order_nr', $resource)) {
            $res['error_message'] = 'key order_nr is missing from body';
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            return $res;
        }
        if (!array_key_exists('franchise_name', $resource)) {
            $res['error_message'] = 'key franchise_name is missing from body';
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            return $res;
        }
  
        if (!array_key_exists('shipping_address', $resource)) {
            $res['error_message'] = 'key shipping_address is missing from body';
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            return $res;
        }
  
        $res['error_code'] = RESTConstants::HTTP_OK;
        return $res;
    }


    /**
     * verifyResourcePickup checks that every function which has body requirements has the correct fields.
     * @param array $resource is the data to verify
     * @param bool $ignoreId if id should be ignored (is not in use)
     * @return array returns an array of error_code, possibly an error message as well
     */
    function verifyResourcePickup(array $resource, bool $ignoreId = false): array
    {
        $res = array();
  
        if (!array_key_exists('transporter_name', $resource)) {
            $res['error_message'] = 'key transporter_name is missing from body';
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            return $res;
        }
        if (!array_key_exists('driver_id', $resource)) {
            $res['error_message'] = 'key driver_id is missing from body';
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            return $res;
        }
  
        if (!array_key_exists('created_employee_nr', $resource)) {
            $res['error_message'] = 'key created_employee_nr is missing from body';
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            return $res;
        }
  
        $res['error_code'] = RESTConstants::HTTP_OK;
        return $res;
    }

}
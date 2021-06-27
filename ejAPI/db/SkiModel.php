<?php
require_once 'dbCredentials.php';

/**
 * Class SkiModel handles all ski related queries to the database.
 */
class SkiModel {
    protected $db;

    public function __construct()
    {
        $this->db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8',
            DB_USER, DB_PWD,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }


    /**
     * Gets all skies in the database with the optional model filter
     * @param string $model optional filter for skies
     * @return array an array of all skies found with the optional model filter
     */
    function getAllSkies(string $column = "", string $model = ""): array
    {
        $res = array();
        $query = "SELECT model, type_of_skiing, temperature, grip_system, size, weight_class, description, historical, photo_url, msrpp FROM `skiis`";
        if(strlen($model) > 0){ // if optional parameter, then filter on that parameter
            $query .= " WHERE " . $column . " = :model";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':model', $model);
        } else {
            $stmt = $this->db->prepare($query);
        }
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($res, $row);
        }
        return $res;
    }


    /**
     * Creates a new ski in the database.
     * @param array $resource the data/body for the ski to be created.
     * @return array a message to returned to the client after the ski is created
     */
    function createSki(array $resource): array
    {
        $res = array();
        $this->db->beginTransaction();
        $rec = $this->verifyResource($resource, true);
        if($rec['error_code'] != RESTConstants::HTTP_OK){
            return $rec;
        }

        $query = "INSERT INTO `skiis` (model, type_of_skiing, temperature, grip_system, size, weight_class, description, historical, photo_url, msrpp) VALUES (:model, :type_of_skiing, :temperature, :grip_system, :size, :weight_class, :description, :historical, :photo_url, :msrpp)";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':model', $resource['model']);
        $stmt->bindValue(':type_of_skiing', $resource['type_of_skiing']);
        $stmt->bindValue(':temperature', $resource['temperature']);
        $stmt->bindValue(':grip_system', $resource['grip_system']);
        $stmt->bindValue(':size', $resource['size']);
        $stmt->bindValue(':weight_class', $resource['weight_class']);
        $stmt->bindValue(':description', $resource['description']);
        $stmt->bindValue(':historical', $resource['historical']);
        $stmt->bindValue(':photo_url', $resource['photo_url']);
        $stmt->bindValue(':msrpp', $resource['msrpp']);
        $stmt->execute();
        $product_id = intval($this->db->lastInsertId());
        $this->db->commit();
        $res['message'] = 'New ski has been created with product id: ' . $product_id;
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
  
        if (!array_key_exists('model', $resource)) {
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            $res['error_message'] = 'key model is missing from body';
            return $res;
        }
        if (!array_key_exists('type_of_skiing', $resource)) {
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            $res['error_message'] = 'key type_of_skiing is missing from body';
            return $res;
        }
        if (!array_key_exists('temperature', $resource)) {
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            $res['error_message'] = 'key temperature is missing from body';
            return $res;
        }
        if (!array_key_exists('grip_system', $resource)) {
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            $res['error_message'] = 'key grip_system is missing from body';
            return $res;
        }
        if (!array_key_exists('size', $resource)) {
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            $res['error_message'] = 'key size is missing from body';
            return $res;
        }
        if (!array_key_exists('weight_class', $resource)) {
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            $res['error_message'] = 'key weight_class is missing from body';
            return $res;
        }
        if (!array_key_exists('description', $resource)) {
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            $res['error_message'] = 'key description is missing from body';
            return $res;
        }
        if (!array_key_exists('historical', $resource)) {
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            $res['error_message'] = 'key historical is missing from body';
            return $res;
        }
        if (!array_key_exists('photo_url', $resource)) {
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            $res['error_message'] = 'key photo_url is missing from body';
            return $res;
        }
        if (!array_key_exists('msrpp', $resource)) {
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            $res['error_message'] = 'key msrpp is missing from body';
            return $res;
        }

        if(intval($resource['size']) % 5 != 2) {
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            $res['error_message'] = 'size needs to be a number with interval 5 from the number 2';
            return $res;
        }

        $weightclass = explode("-", $resource['weight_class']);

        if(count($weightclass) != 2){
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            $res['error_message'] = 'weight_class has wrong format. Example of right format: 70-80';
            return $res;
        }
        if (!ctype_digit($weightclass[0]) || !ctype_digit($weightclass[1])) {
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            $res['error_message'] = 'weight_class must be number';
            return $res;
        }
        if ($weightclass[0] % 10 != 0 || $weightclass[1] - $weightclass[0] != 10) {
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            $res['error_message'] = 'weight_class must be in 10 intervals';
            return $res;
        }

  
        $res['error_code'] = RESTConstants::HTTP_OK;
        return $res;
    }


}

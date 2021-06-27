<?php
require_once 'dbCredentials.php';

/**
 * Class ProductionModel handles production related queries for the database.
 */
class ProductionModel {
    protected $db;

    public function __construct()
    {
        $this->db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8',
            DB_USER, DB_PWD,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }

    /**
     * Returns all prouduction plans that has a start date before today, and an end date after today
     * @return array an array of all production plans within today
     */
    function getProductionplan(): array
    {
        $res = array();
        $query = 'SELECT * FROM `production_plans` WHERE  start_date <= CURRENT_DATE AND CURRENT_DATE <= end_date';
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { // for each row in the result
            $query = 'SELECT * FROM `production_plans_on_skiis` WHERE plan_name = :plan_name';
            $stmt2 = $this->db->prepare($query);
            $stmt2->bindValue(':plan_name', $row['plan_name']);
            $stmt2->execute();

            $plansOnSkiis = array();
            while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) { // for each skii in the production plan
                $skiData = array();
                $skiData['product_id'] = $row2['product_id'];
                $skiData['number_of_skiis'] = $row2['number_of_skiis'];
                array_push($plansOnSkiis, $skiData);
            }

            $planData = array();
            $planData['skiis'] = $plansOnSkiis;
            $planData['plan_name'] = $row['plan_name'];
            array_push($res, $planData);
        }

        return $res;
    }


    /**
     * Creates a new production plan in the database.
     * @param array $resource data/body with information about the production plan to be created.
     * @return array a message to returned to the client after the production plan is created
     */
    function createProductionPlan(array $resource): array
    {
        $res = array();
        $this->db->beginTransaction();
        $rec = $this->verifyResource($resource, true); // if body is not correctly formatted
        if($rec['error_code'] != RESTConstants::HTTP_OK){
            return $rec;
        }

        $query = "INSERT INTO `production_plans` (plan_name, responsible_employee_nr, start_date, end_date) VALUES (:plan_name, :responsible_employee_nr, CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL 28 day))";


        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':plan_name', $resource['plan_name']);
        $stmt->bindValue(':responsible_employee_nr', $resource['responsible_employee_nr']);
        $stmt->execute();


        foreach ($resource['skiis'] as $skii) {  // for each ski type in the production plan
            $query = "INSERT INTO `production_plans_on_skiis` (plan_name, product_id, number_of_skiis) VALUES (:plan_name, :product_id, :number_of_skiis)";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':plan_name', $resource['plan_name']);
            $stmt->bindValue(':product_id', $skii['product_id']);
            $stmt->bindValue(':number_of_skiis', $skii['number_of_skiis']);
            $stmt->execute();
        }
        $this->db->commit();
        $res['message'] = 'production plan created with plan name:  ' . $resource['plan_name'];
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
        if (!array_key_exists('plan_name', $resource)) {
            $res['error_message'] = 'key plan_name is missing from body';
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            return $res;
        }
        if (!array_key_exists('responsible_employee_nr', $resource)) {
            $res['error_message'] = 'key responsible_employee_nr is missing from body';
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            return $res;
        }

        if (!array_key_exists('skiis', $resource)) {
            $res['error_message'] = 'key skiis is missing from body';
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            return $res;
        }

        foreach ($resource['skiis'] as $skii) {
            if (!array_key_exists('product_id', $skii)) {
                $res['error_message'] = 'key product_id is missing from skiis in body';
                $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
                return $res;
            }
            if (!array_key_exists('number_of_skiis', $skii)) {
                $res['error_message'] = 'key number_of_skiis is missing from skiis body';
                $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
                return $res;
            }
        }

        $res['error_code'] = RESTConstants::HTTP_OK;
        return $res;
    }


}

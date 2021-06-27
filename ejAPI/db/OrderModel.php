<?php
require_once 'dbCredentials.php';
require_once 'utils.php';

/**
 * Class OrderModel class for accessing order related data in database.
 */
class OrderModel {
    protected $db;

    public function __construct()
    {
        $this->db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8',
            DB_USER, DB_PWD,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }


   /**
   * Returns every order currently in the database.
   * @return array an array of orders, with all details related to the order. The
   *               array will be empty if there are no orders
   */
    function getAllOrders(): array
    {
        $res = array();
        $query = 'SELECT customer_id, orders.order_nr, product_id, skiis_ordered, skiis_ready, MAX(state.state_value) as state_value
        FROM orders
        LEFT JOIN skiorders
                ON orders.order_nr = skiorders.order_nr
        LEFT JOIN shipments 
                ON orders.order_nr = shipments.order_nr
        LEFT JOIN transition
             ON orders.order_nr = transition.order_nr 
        LEFT JOIN state
             ON transition.state_name = state.state_name
        LEFT JOIN history
             ON transition.transition_id = history.transition_id
        GROUP BY orders.order_nr, product_id';
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $currentOrderNr = -1;
        $orderPos = 0;
        $skiesPos = 0;
        $skies = array();
        $firstRun = true;
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { // for each row in result. (for each ski ordered for each order)
            $query = 'SELECT * from skiis WHERE product_id = :pid';
            $stmtSki = $this->db->prepare($query);
            $stmtSki->bindValue(':pid', $row['product_id']);
            $stmtSki->execute();
            if ($skiData = $stmtSki->fetch(PDO::FETCH_ASSOC)) { // skiData gets the skiis data 
                $skiData["skiis_ordered"] = $row["skiis_ordered"];
                $skiData["skiis_ready"] = $row["skiis_ready"];
                if ($firstRun) { // if first run from the while loop
                    $res[$orderPos] = $row;     
                    unset($res[$orderPos]['product_id']);
                    unset($res[$orderPos]['skiis_ordered']);
                    unset($res[$orderPos]['skiis_ready']);
                }
      
                if($row['order_nr'] != $currentOrderNr && !$firstRun) { // if last run in while loop is from a different order
                    $orderPos++;
                    $skiesPos = 0;
                    $res[$orderPos] = $row;
                    $res[$orderPos]['skiis'][$skiesPos] = $skiData;
                    unset($res[$orderPos]['product_id']);
                    unset($res[$orderPos]['skiis_ordered']);
                    unset($res[$orderPos]['skiis_ready']);
                    $skiesPos++;
                } else { // if last run in while loop is the same order as this loop
                    $res[$orderPos]['skiis'][$skiesPos] = $skiData;
                    $skiesPos++;
                }
                
                $currentOrderNr = $row['order_nr']; // saves the order for next iteration in the while loop
                $firstRun = false;
            }
        }

        foreach ($res as $key=>$val) { // for each order, get the state name
            $query = 'SELECT state_name from state WHERE state_value = :state_value';
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':state_value', $val['state_value']);
            $stmt->execute();
            if ($state = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $res[$key]['state_name'] = $state['state_name'];
            }
            
        }

        return $res;
    }


    /**
     * Returns order specified by it's order_nr in the database.
     * @param int $id the id of the order to be retrieved.
     * @return array of one single order if the order exist
     */
    function getOrdersById(int $id): array
    {
        $res = array();
        if(!$this->isOrderExisting($id)) { // if the order does not exist
            $res['error_message'] = 'cannot find order with order number: ' . $id;
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            return $res;
        }
        $query = 'SELECT *
        FROM orders 
            LEFT JOIN shipments 
                ON orders.order_nr = shipments.order_nr
            LEFT JOIN transition
                 ON orders.order_nr = transition.order_nr 
            LEFT JOIN history
                 ON transition.transition_id = history.transition_id
            WHERE orders.order_nr = :id';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $query2 = 'SELECT * FROM skiorders WHERE order_nr = :id';
        
        $stmt2 = $this->db->prepare($query2);
        $stmt2->bindValue(':id', $id);
        $stmt2->execute();


        $skies = array();
        $stmtSki = array();
        while($row = $stmt2->fetch(PDO::FETCH_ASSOC)) { // for each row in result 
            $query3 = 'SELECT * from skiis WHERE product_id = :pid';
            $stmtSki = $this->db->prepare($query3);
            $stmtSki->bindValue(':pid', $row['product_id']);
            $stmtSki->execute();
            if ($row2 = $stmtSki->fetch(PDO::FETCH_ASSOC)) {
                $pos = count($skies);
                $skies[$pos]= $row2; // addes the ski data to the ski array
            }
        }

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res = $row;
            $res['skiis'] = $skies; // adds the ski array to the result
        }
        
        return $res;
    }

     /**
     * Returns the order for a specific customer together with an since filter
     * @param int $id the id of the customer.
     * @param string $sinceDate date of the orders to get inside the since date.
     * @return array an array of orders related to the customer id - or an empty array if customer has none orders
     */
    function getOrdersByCustomerIdAndSince(int $id, string $sinceDate): array
    {
        $res = array();

        if (!strtotime($sinceDate) || strlen($sinceDate) != 10) { // if sinceDate is not correctly formatted. 
            $res['error_message'] = 'since filter does not follow the right convention. Needs to be: yyyy-mm-dd';
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            return $res; // returns the error
        }
        

        $query = 'SELECT customer_id, orders.order_nr, product_id, skiis_ordered, skiis_ready, MAX(state.state_value) as state_value
        FROM orders
        LEFT JOIN skiorders
                ON orders.order_nr = skiorders.order_nr
        LEFT JOIN shipments 
                ON orders.order_nr = shipments.order_nr
        LEFT JOIN transition
             ON orders.order_nr = transition.order_nr 
        LEFT JOIN state
             ON transition.state_name = state.state_name
        LEFT JOIN history
             ON transition.transition_id = history.transition_id
            WHERE customer_id = :id
        GROUP BY orders.order_nr, product_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $currentOrderNr = -1;
        $orderPos = 0;
        $skiesPos = 0;
        $skies = array();
        $firstRun = true;
        // here the code resuses the same code as in previous function as in uses the same functionality
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { // for each row in result
            $query = 'SELECT * from skiis WHERE product_id = :pid';
            $stmtSki = $this->db->prepare($query);
            $stmtSki->bindValue(':pid', $row['product_id']);
            $stmtSki->execute();
            if ($skiData = $stmtSki->fetch(PDO::FETCH_ASSOC)) {
                $skiData["skiis_ordered"] = $row["skiis_ordered"];
                $skiData["skiis_ready"] = $row["skiis_ready"];
                if ($firstRun) {
                    $res[$orderPos] = $row;
                    unset($res[$orderPos]['product_id']);
                    unset($res[$orderPos]['skiis_ordered']);
                    unset($res[$orderPos]['skiis_ready']);
                }
      
                if($row['order_nr'] != $currentOrderNr && !$firstRun) {
                    $orderPos++;
                    $skiesPos = 0;
                    $res[$orderPos] = $row;
                    $res[$orderPos]['skiis'][$skiesPos] = $skiData;
                    unset($res[$orderPos]['product_id']);
                    unset($res[$orderPos]['skiis_ordered']);
                    unset($res[$orderPos]['skiis_ready']);
                    $skiesPos++;
                } else {
                    $res[$orderPos]['skiis'][$skiesPos] = $skiData;
                    $skiesPos++;
                }
                
                $currentOrderNr = $row['order_nr'];
                $firstRun = false;
            }
        }

        foreach ($res as $key=>$val) {
            $query = 'SELECT state_name from state WHERE state_value = :state_value';
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':state_value', $val['state_value']);
            $stmt->execute();
            if ($state = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $res[$key]['state_name'] = $state['state_name'];
            }
            
        }

        $newRes = array();
        foreach ($res as $order) {  // for each order
            $query = 'SELECT transition_id FROM `transition` WHERE state_name = "new" AND order_nr = :order_nr';
            $transitionStmt = $this->db->prepare($query);
            $transitionStmt->bindValue(':order_nr', $order['order_nr']);
            $transitionStmt->execute();
            if ($transitionData = $transitionStmt->fetch(PDO::FETCH_ASSOC)) { 
                $query = 'SELECT start_date FROM `history` WHERE transition_id = :transition_id AND start_date > :start_date';
                $historyStmt = $this->db->prepare($query);
                $historyStmt->bindValue(':transition_id', $transitionData['transition_id']);
                $historyStmt->bindValue(':start_date', $sinceDate);
                $historyStmt->execute();
                if ($historyStmt->fetch(PDO::FETCH_ASSOC)) { // if the order is after $sinceDate, add it to the result.
                    array_push($newRes, $order);
                }

            }
        }

        $res = $newRes;

        return $res;
    }

    /**
     * Returns the order based on a specific customer id.
     * @param int $id the id of the customer.
     * @return array an array of orders - or an empty array if customer has none orders
     */
    function getOrdersByCustomerId(int $id): array
    {
        $res = array();
        $query = 'SELECT customer_id, orders.order_nr, product_id, skiis_ordered, skiis_ready, MAX(state.state_value) as state_value
        FROM orders
        LEFT JOIN skiorders
                ON orders.order_nr = skiorders.order_nr
        LEFT JOIN shipments 
                ON orders.order_nr = shipments.order_nr
        LEFT JOIN transition
             ON orders.order_nr = transition.order_nr 
        LEFT JOIN state
             ON transition.state_name = state.state_name
        LEFT JOIN history
             ON transition.transition_id = history.transition_id
            WHERE customer_id = :id
        GROUP BY orders.order_nr, product_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $currentOrderNr = -1;
        $orderPos = 0;
        $skiesPos = 0;
        $skies = array();
        $firstRun = true;
        // here the code resuses the same code as in previous function as in uses the same functionality
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { // for each row in result
            $query = 'SELECT * from skiis WHERE product_id = :pid';
            $stmtSki = $this->db->prepare($query);
            $stmtSki->bindValue(':pid', $row['product_id']);
            $stmtSki->execute();
            if ($skiData = $stmtSki->fetch(PDO::FETCH_ASSOC)) {
                $skiData["skiis_ordered"] = $row["skiis_ordered"];
                $skiData["skiis_ready"] = $row["skiis_ready"];
                if ($firstRun) {
                    $res[$orderPos] = $row;
                    unset($res[$orderPos]['product_id']);
                    unset($res[$orderPos]['skiis_ordered']);
                    unset($res[$orderPos]['skiis_ready']);
                }
      
                if($row['order_nr'] != $currentOrderNr && !$firstRun) {
                    $orderPos++;
                    $skiesPos = 0;
                    $res[$orderPos] = $row;
                    $res[$orderPos]['skiis'][$skiesPos] = $skiData;
                    unset($res[$orderPos]['product_id']);
                    unset($res[$orderPos]['skiis_ordered']);
                    unset($res[$orderPos]['skiis_ready']);
                    $skiesPos++;
                } else {
                    $res[$orderPos]['skiis'][$skiesPos] = $skiData;
                    $skiesPos++;
                }
                
                $currentOrderNr = $row['order_nr'];
                $firstRun = false;
            }
        }

        foreach ($res as $key=>$val) {
            $query = 'SELECT state_name from state WHERE state_value = :state_value';
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':state_value', $val['state_value']);
            $stmt->execute();
            if ($state = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $res[$key]['state_name'] = $state['state_name'];
            }
            
        }

        return $res;
    }

    /**
     * Returns the order based on the state sent in through the endpoint
     * @param string $string is the state which is passed through.
     * @return array an array of orders - or an empty array if customer has none orders
     */
    function getOrdersByState(string $state): array
    {
        $res = array();
        $statesNotIncluded = statesNotIncluded($state); // gets an array of states to not be included in the query
        $query = 'SELECT customer_id, orders.order_nr, product_id, skiis_ordered, skiis_ready, MAX(state.state_value) as state_value
        FROM orders
        LEFT JOIN skiorders
                ON orders.order_nr = skiorders.order_nr
        LEFT JOIN shipments 
                ON orders.order_nr = shipments.order_nr
        LEFT JOIN transition
             ON orders.order_nr = transition.order_nr 
        LEFT JOIN state
             ON transition.state_name = state.state_name
        LEFT JOIN history
             ON transition.transition_id = history.transition_id ';

        if (count($statesNotIncluded) > 0 ) { // if more than one state needs to not be included in the query
            $query .= 'WHERE transition.state_name = :state_name AND transition.order_nr NOT IN (SELECT order_nr from transition WHERE ';
            foreach ($statesNotIncluded as $key=>$val) { // for each state to not be included, at the following query
                $query .= 'transition.state_name = "' . $val . '"';
                if ($key < count($statesNotIncluded)-1) { // if not the last iteration in the foreach loop
                    $query .= ' OR '; // puts OR between the states query to not be included
                }
            }
            $query .= ')';
        } 

        $query .= 'GROUP BY orders.order_nr, product_id';


        $stmt = $this->db->prepare($query);
        $state = removeUnderscores($state);
        $stmt->bindValue(':state_name', $state);
        $stmt->execute();

        $currentOrderNr = -1;
        $orderPos = 0;
        $skiesPos = 0;
        $skies = array();
        $firstRun = true;
         // here the code resuses the same code as in previous function as in uses the same functionality
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { // for each row in result
            $query = 'SELECT * from skiis WHERE product_id = :pid';
            $stmtSki = $this->db->prepare($query);
            $stmtSki->bindValue(':pid', $row['product_id']);
            $stmtSki->execute();
            if ($skiData = $stmtSki->fetch(PDO::FETCH_ASSOC)) {
                $skiData["skiis_ordered"] = $row["skiis_ordered"];
                $skiData["skiis_ready"] = $row["skiis_ready"];
                if ($firstRun) {
                    $res[$orderPos] = $row;
                    unset($res[$orderPos]['product_id']);
                    unset($res[$orderPos]['skiis_ordered']);
                    unset($res[$orderPos]['skiis_ready']);
                }
      
                if($row['order_nr'] != $currentOrderNr && !$firstRun) {
                    $orderPos++;
                    $skiesPos = 0;
                    $res[$orderPos] = $row;
                    $res[$orderPos]['skiis'][$skiesPos] = $skiData;
                    unset($res[$orderPos]['product_id']);
                    unset($res[$orderPos]['skiis_ordered']);
                    unset($res[$orderPos]['skiis_ready']);
                    $skiesPos++;
                } else {
                    $res[$orderPos]['skiis'][$skiesPos] = $skiData;
                    $skiesPos++;
                }
                
                $currentOrderNr = $row['order_nr'];
                $firstRun = false;
            }
        }

        foreach ($res as $key=>$val) {
            $query = 'SELECT state_name from state WHERE state_value = :state_value';
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':state_value', $val['state_value']);
            $stmt->execute();
            if ($state = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $res[$key]['state_name'] = $state['state_name'];
            }
            
        }

        return $res;
  
    }

    /**
     * Creates an order in the database.
     * @param array $resource is the data(body) with information about the new order to be created
     * @param bool $fromSplit defines wether this order is runned from the splitOrder function or not.
     * @return array a message to returned to the client after the order is created
     */
    function createOrder(array $resource, bool $fromSplit = false): array
    {

        $res = array();
        $this->db->beginTransaction();
        $rec = $this->verifyResource($resource, true);
        if($rec['error_code'] != RESTConstants::HTTP_OK){
            return $rec;
        }

        $query = 'INSERT INTO orders (customer_id) VALUES(:customer_id)';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':customer_id', $resource['customer_id']);
        $stmt->execute();

        $order_nr = intval($this->db->lastInsertId());

        foreach ($resource['skiis'] as $skiis) { // for each ski data in the body, insert into the skiorders table
            $query = 'INSERT INTO skiorders (order_nr, product_id, skiis_ordered, skiis_ready) VALUES (:order_nr, :product_id, :skiis_ordered, :skiis_ready)';
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':order_nr', $order_nr);
            $stmt->bindValue(':product_id', $skiis['product_id']);
            $stmt->bindValue(':skiis_ordered', $skiis['skiis_ordered']);
            $stmt->bindValue(':skiis_ready', '0');   
            $stmt->execute();
        }

        $query = 'INSERT INTO transition (state_name, order_nr) VALUES (:state_name, :order_nr)';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':state_name', 'new');
        $stmt->bindValue(':order_nr', $order_nr);
        $stmt->execute();

        $transition_id = intval($this->db->lastInsertId()); // get the id for the last inserted row
        $query = 'INSERT INTO history (transition_id, start_date) VALUES (:transition_id, CURDATE() )';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':transition_id', $transition_id);
        
        $stmt->execute();

        if ($fromSplit){ // if function is ran from the splitOrders function
            $res['lastInsertId'] = $order_nr;
        } else { // if function is not ran from the splitOrders function
            $res['message'] = 'Order created with order number: ' . $order_nr;
        }

        $this->db->commit();

        return $res;
    }

    /**
     * Change the date of an order in the database.
     * @param int $id is the order_nr which is the one we modify.
     * @param array $resource is the data(body) with information about what to modifyed in the order
     * @return array a message to returned to the client after the order is modifyed
     */
    function modifyOrder(int $id, array $resource): array
    {
        $res = array();
        if(!$this->isOrderExisting($id)) { // if the order does not exist
            $res['error_message'] = 'cannot find order with order number: ' . $id;
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            return $res;
        }
        $this->db->beginTransaction();

        if(array_key_exists("skiis",$resource)) { // if skiis is in the body, then update the skis data in the order
            foreach ($resource['skiis'] as $skiis) {
                $query = 'UPDATE `skiorders` SET skiis_ready = :skiis_ready  WHERE `order_nr` = :id AND product_id = :product_id'; 
                $stmt = $this->db->prepare($query);
                $stmt->bindValue(':id', $id);
                $stmt->bindValue(':skiis_ready', $skiis["skiis_ready"]);
                $stmt->bindValue(':product_id', $skiis["product_id"]);
                $stmt->execute();
            }
        }

        if(array_key_exists("created_employee_nr", $resource)) { // if created_employee_nr is in the body, the change state to the next value

            $query = 'SELECT `transition_id`, `state_name` FROM `transition` WHERE `order_nr` = :id'; 
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $highestState = 0;
            $highestStateId = 0;
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { // for each transition of the order, find the highest state (current state)
                //print_r(array_keys($row));
                $stateValue = stateToValue($row['state_name']);
                if ($highestState <= $stateValue) {
                    $highestState = stateToValue($row['state_name']);
                    $highestStateId = $row['transition_id'];
                }
            }

            if($highestState == 2){ // if highest state value is 2
                $rec = $this->verifySkiState($id); // check is skiis ready is the same as skiis ordered (fullfilled order)
                if($rec['error_code'] != RESTConstants::HTTP_OK){ // if skiis ready is not the same as skiis ordered
                    return $rec; // return error
                }

            }
            

            if($highestState < 4) { // if not shipped, set the next state value on order
                $query = 'UPDATE `history` SET end_date = CURDATE() WHERE `transition_id` = :id'; 
                $stmt = $this->db->prepare($query);
                $stmt->bindValue(':id', $highestStateId);
                $stmt->execute();

                $query = 'INSERT INTO transition (created_employee_nr, state_name, order_nr) VALUES (:created_employee_nr, :state_name, :order_nr)'; 
                $stmt = $this->db->prepare($query);
                $newStateName = valueToState($highestState+1);
                $stmt->bindValue(':created_employee_nr', $resource['created_employee_nr']);
                $stmt->bindValue(':state_name', $newStateName);
                $stmt->bindValue(':order_nr', $id);
                $stmt->execute();


                $query = 'INSERT INTO `history` (transition_id, start_date) VALUES (:id, CURDATE())'; 
                $stmt = $this->db->prepare($query);
                $transition_id = intval($this->db->lastInsertId());
                $stmt->bindValue(':id', $transition_id);
                $stmt->execute();
              

            }

        }

        $res['message'] = 'resource ' . $id . ' has been updated';

        $this->db->commit();
        return $res;
    }

    /**
     * Splits an order in to two orders, where one gets ready_to_be_shippet status with the ready skiis, 
     * and the other order gets the remaining skis of the order.
     * @param int $id is the order_nr which is the one we will split.
     * @param resource $resource contains the created_employee_nr needed for history table.
     */
    function splitOrder(int $id, array $resource)
    {
        $res = array();
        if(!$this->isOrderExisting($id)) { // if the order does not exist
            $res['error_message'] = 'cannot find order with order number: ' . $id;
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            return $res;
        }

        if(!$this->isHavingSkiisReady($id)){ // if the order does not have any skis ready, then it cannot be splitted
            $res['error_message'] = 'cannot split order that does not have any skiis ready';
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            return $res;
        }

        $query = 'SELECT customer_id FROM orders WHERE order_nr = :order_nr';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':order_nr', $id);
        $stmt->execute();
        $customer_id = $stmt->fetch(PDO::FETCH_ASSOC)['customer_id'];

        $query = 'SELECT product_id, skiis_ordered, skiis_ready FROM skiorders WHERE order_nr = :order_nr';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':order_nr', $id);
        $stmt->execute();
        $newSkiOrderData = array();
        if(!$this->isOrderExisting($id)) { // if the order does not exist
            $res['error_message'] = 'cannot find order with order number: ' . $id;
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            return $res;
        }

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {  // for each row in result. update the skis order to skis ready on the current order     
            $query = 'UPDATE `skiorders` SET skiis_ordered = :skiis_ordered WHERE `order_nr` = :order_nr AND `product_id` = :product_id'; 
            $stmt2 = $this->db->prepare($query);
            $stmt2->bindValue(':order_nr', $id);
            $stmt2->bindValue(':product_id', $row['product_id']);
            $stmt2->bindValue(':skiis_ordered', $row['skiis_ready']);
            $stmt2->execute();

            $skiisLeft = (int)$row['skiis_ordered'] - (int)$row['skiis_ready'];
            if($skiisLeft > 0) {
                $skiData = array();
                $skiData['product_id'] = $row['product_id'];
                $skiData['skiis_ordered'] = $skiisLeft;
                array_push($newSkiOrderData, $skiData);
            }
        }

        $newOrderData['customer_id'] = $customer_id;
        $newOrderData['skiis'] = $newSkiOrderData;

        $modifyData = array();
        $modifyData['created_employee_nr'] = $resource['created_employee_nr'];

    
        $neworder_res = $this->createOrder($newOrderData, true); // creates a new order with the remaining skiis that are not ready
        $neworder_nr = $neworder_res['lastInsertId'];
        $res = $this->modifyOrder($neworder_nr, $modifyData); // modifes the state of the newly created order
        $res = $this->modifyOrder($id, $modifyData); // modifes the state of the original order to ready_to_be_shipped




       $res['message'] = 'order ' . $id . ' has been split into order: ' . $neworder_nr;

        //$this->db->commit();
        return $res;
    }


    /**
     * Deletes an order in the database and everything else associated in the correct order.
     * @param int $id is the order_nr for the order we want to delete.
     */
    function deleteOrder(int $id)
    {
        $res = array();
        if(!$this->isOrderExisting($id)) { // if the order does not exist
            $res['error_message'] = 'cannot find order with order number: ' . $id;
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            return $res;
        }
        $query = 'DELETE FROM `skiorders` WHERE `order_nr` = :id'; // deleting from ski-orders
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id',$id);
        $stmt->execute();

        $query = 'SELECT `transition_id` FROM `transition` WHERE `order_nr` = :id'; // finds all transitions to to the orders
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id',$id);
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { // for each row in result
            $query = 'DELETE FROM `history` WHERE `transition_id` = :id'; // deletion from transition table.
            $stmt2 = $this->db->prepare($query); 
            $stmt2->bindValue(':id',$row['transition_id']);
            $stmt2->execute();
        }

        $query = 'DELETE FROM `transition` WHERE `order_nr` = :id'; // deleting from history
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id',$id);
        $stmt->execute();

        $query = 'DELETE FROM `shipments` WHERE `order_nr` = :id'; // deleting shipment if exist
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id',$id);
        $stmt->execute();

        $query = 'DELETE FROM `orders` WHERE `order_nr` = :id'; // delete order
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $res['message'] = 'order ' . $id . ' has been deleted';
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

    if (!array_key_exists('customer_id', $resource)) { 
        $res['error_message'] = 'key customer_id is missing from body';
        $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
        return $res;
    }

    if (!$this->isCustomerExisting($resource['customer_id'])) {
        $res['error_message'] = 'Customer with customer_id ' . $resource['customer_id'] . ' does not exist';
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
        if (!array_key_exists('skiis_ordered', $skii)) {
            $res['error_message'] = 'key skiis_ordered is missing from skiis body';
            $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
            return $res;
        }
    }

    $res['error_code'] = RESTConstants::HTTP_OK;
    return $res;
    }

    /**
     * verify if the customer exist
     * @param string $customer_id the id for the customer
     * @return bool true if customer exist, false if not
     */
    protected function isCustomerExisting(string $customer_id): bool
    {
      $query = 'SELECT COUNT(*) FROM customer WHERE customer_id = :customer_id';

      $stmt = $this->db->prepare($query);
      $stmt->bindValue(':customer_id', $customer_id);
      $stmt->execute();

      $row = $stmt->fetch(PDO::FETCH_NUM);
      if ($row[0] == 0) {
          return false;
      } 

      return true;
    }

    
    /**
     * verify if the order exist
     * @param string $order_nr the id for the order
     * @return bool true if order exist, false if not
     */
    protected function isOrderExisting(string $order_nr): bool
    {
      $query = 'SELECT COUNT(*) FROM orders WHERE order_nr = :order_nr';

      $stmt = $this->db->prepare($query);
      $stmt->bindValue(':order_nr', $order_nr);
      $stmt->execute();

      $row = $stmt->fetch(PDO::FETCH_NUM);
      if ($row[0] == 0) {
          return false;
      } 

      return true;
    }

    /**
     * verify if the order has more than 1 skis ready
     * @param string $order_nr the id for the order
     * @return bool true if order has skis ready exist, false if not
     */
    protected function isHavingSkiisReady(string $order_nr): bool
    {
      $query = 'SELECT COUNT(*) FROM skiorders WHERE order_nr = :order_nr AND skiis_ready > 0';

      $stmt = $this->db->prepare($query);
      $stmt->bindValue(':order_nr', $order_nr);
      $stmt->execute();

      $row = $stmt->fetch(PDO::FETCH_NUM);
      if ($row[0] == 0) {
          return false;
      } 

      return true;
    }

    /**
     * verify if skiis ordered is not the same as skis ready
     * @param string $id the id for the skiorder table
     * @return bool true if skiis ordered is not the same as skis ready, false if not
     */
    function verifySkiState(int $id): array
    {
        $res = array();
        $query = 'SELECT * FROM skiorders WHERE order_nr = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row['skiis_ordered'] != $row['skiis_ready']) {
                $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
                $res['error_message'] = "cannot change state to 'ready to be shipped' becouse number of skiis ready is not the same as skiis ordered";
                return $res;
            }
        }
        $res['error_code'] = RESTConstants::HTTP_OK;
  
        return $res;
    }

    /**
     * validates that the string is a valid date
     * @param string $date is the date to be validated
     * @param string $format is the date format it should validate
     * @return bool true if string is valid date, false if not
     */
    function validateDate(string $date, string $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}
<?php

use Codeception\Module\REST;

require_once 'RESTConstants.php';
require_once 'db/SkiModel.php';
require_once 'db/OrderModel.php';
require_once 'db/ShipmentModel.php';
require_once 'db/ProductionModel.php';
require_once 'db/AuthorisationModel.php';
require_once 'db/Errors.php';

/**
 * Class APIController manages the routing of all the four endpoints to the correct functions
 */
class APIController
{
    /**
     * Verifies whether the user queried a correct endpoint.
     * @param array $uri the endpoint constructed recieved in api.php routing.
     * @return bool True if the endpoint was valiated for one possible endpoint.
     */
    public function isValidEndpoint(array $uri): bool
    {
        if ($uri[0] == RESTConstants::ENDPOINT_CUSTOMER) { 
            if ($uri[1] == RESTConstants::ENDPOINT_ORDERS) { 
                if (count($uri) == 2) {  // customer/orders/
                    return true;
                } 
                if (count($uri) == 3) { // customer/orders/{:order_nr}
                    return ctype_digit($uri[2]);
                } 
                if(count($uri) == 4){
                    if($uri[1] == RESTConstants::ENDPOINT_ORDERS && $uri[2] == RESTConstants::ENDPOINT_SPLIT && ctype_digit($uri[3])){ // customer/orders/split/{:order_nr}
                        return true;
                    }
                }
            } 
            if ($uri[1] == RESTConstants::ENDPOINT_PRODUCTIONPLANS) {
                if (count($uri) == 2) { // customer/productionplans
                    return true;
                } 
            }
        }
        

        if ($uri[0] == RESTConstants::ENDPOINT_EMPLOYEE) {
            if (count($uri) == 2) {
                if($uri[1] == RESTConstants::ENDPOINT_ORDERS){
                    return true;
                }
                if($uri[1] == RESTConstants::ENDPOINT_SHIPMENTS){ 
                    return true;
                }
                if($uri[1] == RESTConstants::ENDPOINT_SKIIS){ 
                    return true;
                }
                if($uri[1] == RESTConstants::ENDPOINT_PRODUCTIONPLANS){
                    return true;
                }
            } else if(count($uri) == 3){
                if($uri[1] == RESTConstants::ENDPOINT_MODIFYSTATE && ctype_digit($uri[2])){  // employee/modifystate/{:order_nr}
                    return true;
                }
                if($uri[1] == RESTConstants::ENDPOINT_ORDERS && ctype_digit($uri[2])){ // employee/orders/{:order_nr}
                    return true;
                }
            }
        }

        if ($uri[0] == RESTConstants::ENDPOINT_TRANSPORTER){
            if (count($uri) == 2) {
                if($uri[1] == RESTConstants::ENDPOINT_ORDERS){
                    return true;
                }
            }
            if (count($uri) == 3) {
                if($uri[1] == RESTConstants::ENDPOINT_ORDERS && ctype_digit($uri[2])){ // transporter/orders/{:order_nr}
                    return true;
                }
            }
        }

        if ($uri[0] == RESTConstants::ENDPOINT_PUBLIC){
            if (count($uri) == 2) {
                if($uri[1] == RESTConstants::ENDPOINT_SKIIS){
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * Verifies that the request contains a valid authorisation token. The authorisation scheme is quite simple -
     * assuming that there is only one authorisation token for the complete API
     * @param string $token the authorisation token to be verified
     * @param string $endpointPath the request endpoint
     * @throws APIException with the code set to HTTP_FORBIDDEN if the token is not valid
     */
    public function authorise(string $token, string $endpointPath) {
        if (!(new AuthorisationModel())->isValid($token)) {
            throw new APIException(RESTConstants::HTTP_FORBIDDEN, $endpointPath);
        }
    }



    /**
    * Verifies whether the user queried a correct endpoint.
    * @param array $uri the endpoint constructed recieved in api.php routing.
    * @param string $requestMethod the endpoint constructed recieved in api.php routing.
    * @return bool True if the endpoint was valiated for one possible endpoint.
    */
    public function isValidMethod(array $uri, string $requestMethod): bool {
        switch ($uri[0]) {
            case RESTConstants::ENDPOINT_CUSTOMER:
                if((count($uri) == 2 || count($uri) == 3) && $requestMethod == RESTConstants::METHOD_GET) {
                    return true;
                } else if(count($uri) == 2 && $requestMethod == RESTConstants::METHOD_POST){
                    return true;
                }  else if(count($uri) == 4 && $requestMethod == RESTConstants::METHOD_PUT){
                    return true;
                }
            case RESTConstants::ENDPOINT_EMPLOYEE:
                if((count($uri) == 3 || count($uri) == 2) && $requestMethod == RESTConstants::METHOD_GET) {
                    return true;
                } else if (count($uri) == 2 && $requestMethod == RESTConstants::METHOD_POST){
                    return true;
                } else if (count($uri) == 3 && $requestMethod == RESTConstants::METHOD_DELETE) {
                    return true;
                } else if (count($uri) == 3 && $requestMethod == RESTConstants::METHOD_PUT) {
                    return true;
                }
            case RESTConstants::ENDPOINT_TRANSPORTER:
                if (count($uri) == 2 && $requestMethod == RESTConstants::METHOD_GET){
                    return true;
                } else if (count($uri) == 3 && $requestMethod == RESTConstants::METHOD_PUT) {
                    return true;
                }
            case RESTConstants::ENDPOINT_PUBLIC:
                if (count($uri) == 2 && $requestMethod == RESTConstants::METHOD_GET){
                    return true;
                }
        }
        return false;
    }

    /**
    * Verifies if the payload is valid.
    * @param array $uri the endpoint constructed recieved in api.php routing.
    * @param string $requestMethod the endpoint constructed recieved in api.php routing.
    * @param array $payload the endpoint constructed recieved in api.php routing.
    * @return bool True if the payload is valid.
    */
    public function isValidPayload(array $uri, string $requestMethod, array $payload): bool
    {
        
      
        if ($requestMethod == RESTConstants::METHOD_GET)  {
            return true;
        } else if ($requestMethod == RESTConstants::METHOD_POST)  {
            return true;
        } else if ($requestMethod == RESTConstants::METHOD_DELETE) {
            return true;
        } else if ($requestMethod == RESTConstants::METHOD_PUT) {
            return true;
        }
        return false;
    }


   /**
    * Routes the request to the correct function.
    * @param array $uri the endpoint constructed recieved in api.php routing.
    * @param string $requestMethod the endpoint constructed recieved in api.php routing.
    * @param array $queries filter parameter in url
    * @param array $payload the endpoint constructed recieved in api.php routing.
    * @param string $token the token for authentication 
    * @return array result returned to the client. Could be error or data from database
     */
    public function handleRequest(array $uri, string $requestMethod, array $queries, array $payload, string $token): array
    {
        $endpointUri = $uri[0];
        switch ($endpointUri) {
            case RESTConstants::ENDPOINT_CUSTOMER:
                return $this->handleCustomerRequest($uri, $requestMethod, $queries, $payload, $token);
                break;
            case RESTConstants::ENDPOINT_EMPLOYEE:
                return $this->handleEmployeeRequest($uri, $requestMethod, $queries, $payload, $token);
                break;
            case RESTConstants::ENDPOINT_TRANSPORTER:
                return $this->handleTransporterRequest($uri, $requestMethod, $queries, $payload, $token);
                break;
            case RESTConstants::ENDPOINT_PUBLIC:
                return $this->handlePublicRequest($uri, $requestMethod, $queries, $payload);
                break;
        }
        return array();
   }

    /**
    * Routes the request to the correct function.
    * @param array $uri the endpoint constructed recieved in api.php routing.
    * @param string $requestMethod the endpoint constructed recieved in api.php routing.
    * @param array $queries filter parameter in url
    * @param array $payload the endpoint constructed recieved in api.php routing.
    * @param string $token the token for authentication 
    * @return array result returned to the client. Could be error or data from database
    */
   protected function handleCustomerRequest(array $uri, string $requestMethod, array $queries, array $payload, string $token): array
    {
        $auth = new AuthorisationModel();
        if(!$auth->checkTokenAndPermission($token, "customer")){
            return errorMessageForbidden();
        }
        $model = new OrderModel();
        switch($requestMethod) {
            case RESTConstants::METHOD_GET:
                if($uri[1] == RESTConstants::ENDPOINT_ORDERS && count($uri) == 3 &&  array_key_exists("since", $queries) ) {
                    return $model->getOrdersByCustomerIdAndSince(intval($uri[2]), $queries["since"]);
                }
                if ($uri[1] == RESTConstants::ENDPOINT_ORDERS && count($uri) == 3) {
                    return $model->getOrdersByCustomerId(intval($uri[2]));
                }
                if ($uri[1] == RESTConstants::ENDPOINT_PRODUCTIONPLANS && count($uri) == 2) {
                    $model = new ProductionModel();
                    return $model->getProductionplan();
                }
            case RESTConstants::METHOD_POST:
                if ($uri[1] == RESTConstants::ENDPOINT_ORDERS && count($uri) == 2) {
                    return $model->createOrder($payload);
                }
                if ($uri[1] == RESTConstants::ENDPOINT_SKIIS && count($uri) == 2) {
                    return $model->createSki($payload);
                }
                if ($uri[1] == RESTConstants::ENDPOINT_SHIPMENTS && count($uri) == 2) {
                    return $model->createOrder($payload);
                }
            case RESTConstants::METHOD_PUT:
                if($uri[1] == RESTConstants::ENDPOINT_ORDERS && $uri[2] == RESTConstants::ENDPOINT_SPLIT && count($uri) == 4) {
                    return $model->splitOrder(intval($uri[3]), $payload);
                }
            case RESTConstants::METHOD_DELETE:
                if(count($uri) == 3) {
                    return $model->deleteOrder(intval($uri[2]));
                }
        }
        return array();
    }
   


     /**
      * Routes the request to the correct function.
    * @param array $uri the endpoint constructed recieved in api.php routing.
    * @param string $requestMethod the endpoint constructed recieved in api.php routing.
    * @param array $queries filter parameter in url
    * @param array $payload the endpoint constructed recieved in api.php routing.
    * @param string $token the token for authentication 
    * @return array result returned to the client. Could be error or data from database
     * @return array 
     */
   protected function handleEmployeeRequest(array $uri, string $requestMethod, array $queries, array $payload, string $token): array
   {
       $auth = new AuthorisationModel();
       switch($requestMethod) {
            case RESTConstants::METHOD_GET:
                $model = new OrderModel();
                if (count($uri) == 2) {
                    $model = new OrderModel();
                    if ($uri[1] == RESTConstants::ENDPOINT_ORDERS && array_key_exists("state", $queries)) {
                        if(!$auth->checkTokenAndPermission($token, "customer_rep") && (!$auth->checkTokenAndPermission($token, "storekeeper") || $queries['state'] != "skis_available")){
                            return errorMessageForbidden();
                        }
                        return $model->getOrdersByState($queries["state"]);
                    } else if ($uri[1] == RESTConstants::ENDPOINT_ORDERS)  {
                        if(!$auth->checkTokenAndPermission($token, "customer_rep")){
                            return errorMessageForbidden();
                        }
                        return $model->getAllOrders();
                    }
                }
                break;
           
            case RESTConstants::METHOD_POST:
                if (count($uri) == 2) {
                    if($uri[1] == RESTConstants::ENDPOINT_SHIPMENTS) {
                        if(!$auth->checkTokenAndPermission($token, "customer_rep")){
                            return errorMessageForbidden();
                        }
                        $model = new ShipmentModel();
                        return $model->createShipment($payload);
                    }
                    if($uri[1] == RESTConstants::ENDPOINT_SKIIS) {
                        if(!$auth->checkTokenAndPermission($token, "storekeeper")){
                            return errorMessageForbidden();
                        }
                        $model = new SkiModel();
                        return $model->createSki($payload);
                    }
                    if($uri[1] == RESTConstants::ENDPOINT_PRODUCTIONPLANS) {
                        if(!$auth->checkTokenAndPermission($token, "production_planner")){
                            return errorMessageForbidden();
                        }
                        $model = new ProductionModel();
                        return $model->createProductionPlan($payload);
                    }
                } 
                break;
   
            case RESTConstants::METHOD_PUT:
                if(count($uri) == 3) {
                    if($uri[1] == RESTConstants::ENDPOINT_MODIFYSTATE || RESTConstants::ENDPOINT_ORDERS) {
                        if(!$auth->checkTokenAndPermission($token, "customer_rep")){
                            return errorMessageForbidden();
                        }
                        $model = new OrderModel();
                        return $model->modifyOrder(intval($uri[2]), $payload);
                    }
                    
                }
         
       }
       return array();
   }

     /**
    * Routes the request to the correct function.
    * @param array $uri the endpoint constructed recieved in api.php routing.
    * @param string $requestMethod the endpoint constructed recieved in api.php routing.
    * @param array $queries filter parameter in url
    * @param array $payload the endpoint constructed recieved in api.php routing.
    * @param string $token the token for authentication 
    * @return array result returned to the client. Could be error or data from database
     * @return array 
     */
   protected function handleTransporterRequest(array $uri, string $requestMethod, array $queries, array $payload, string $token): array
   {
        $auth = new AuthorisationModel();
        if(!$auth->checkTokenAndPermission($token, "transporter")){
            return errorMessageForbidden();
        }
       switch($requestMethod) {
           case RESTConstants::METHOD_GET:
               if ($uri[1] == RESTConstants::ENDPOINT_ORDERS && count($uri) == 2) {
                   $model = new OrderModel();
                   return $model->getOrdersByState("ready_to_be_shipped");
               }
            case RESTConstants::METHOD_PUT:
                if ($uri[1] == RESTConstants::ENDPOINT_ORDERS && count($uri) == 3) {
                    $model = new ShipmentModel();
                    return $model->pickupShipment(intval($uri[2]), $payload);
                }
       }
       return array();
   }

     /**
     * Routes the request to the correct function.
    * @param array $uri the endpoint constructed recieved in api.php routing.
    * @param string $requestMethod the endpoint constructed recieved in api.php routing.
    * @param array $queries filter parameter in url
    * @param array $payload the endpoint constructed recieved in api.php routing.
    * @param string $token the token for authentication 
    * @return array result returned to the client. Could be error or data from database
    */
   protected function handlePublicRequest(array $uri, string $requestMethod, array $queries, array $payload): array
   {
       switch($requestMethod) {
           case RESTConstants::METHOD_GET:
                if ($uri[1] == RESTConstants::ENDPOINT_SKIIS && count($uri) == 2 &&  array_key_exists("model", $queries)) {
                    $model = new SkiModel();
                    return $model->getAllSkies("model", $queries["model"]);
                } else if ($uri[1] == RESTConstants::ENDPOINT_SKIIS && count($uri) == 2 &&  array_key_exists("grip_system", $queries)) {
                    $model = new SkiModel();
                    return $model->getAllSkies("grip_system", $queries["grip_system"]);
                } else if ($uri[1] == RESTConstants::ENDPOINT_SKIIS && count($uri) == 2) {
                    $model = new SkiModel();
                    return $model->getAllSkies();
                }
       }
       return array();
   }


}
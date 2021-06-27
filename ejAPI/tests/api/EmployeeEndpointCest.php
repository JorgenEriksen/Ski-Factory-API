<?php

require_once 'AuthorisationCest.php';

class EmployeeEndpointCest
{
    public function _before(ApiTester $I)
    {
    }
    
    // Checks the endpoint for storekeeper getting skis_available orders.
    public function getOrdersByState(ApiTester $I)
    {
        AuthorisationCest::setAuthorisationTokenStorekeeper($I);
        $I->sendGet('ejAPI/v1.0/employee/orders?state=skis_available');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['state_value' => 3]);
        $I->seeResponseContainsJson(['order_nr' => 2]);
    }
    
    // Tests the endpoint for changing the state of an order as a customer rep.
    public function TestModifyStateEndpoint(ApiTester $I)
    {
        AuthorisationCest::setAuthorisationTokenCustomerRep($I);
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPut('/ejAPI/v1.0/employee/modifystate/4', ['created_employee_nr' => 2]);
        $I->seeResponseCodeIs(200);
    }

    // Tests error message for invalid state modification.
    public function TestInvalidModifyStateEndpoint(ApiTester $I)
    {
        AuthorisationCest::setAuthorisationTokenCustomerRep($I);
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPut('/ejAPI/v1.0/employee/modifystate/2', ['created_employee_nr' => 2]);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('error_message' => "cannot change state to 'ready to be shipped' becouse number of skiis ready is not the same as skiis ordered"));
    }

    // Test creating a production plan
    public function UploadingAProductionPlanTest(ApiTester $I)
    { 
        AuthorisationCest::setAuthorisationTokenProductionPlanner($I);
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPost('/ejAPI/v1.0/employee/productionplans', ['plan_name' => 'plan for 24-28', 'responsible_employee_nr' => 3, 'skiis' => [['product_id' => 1, 'number_of_skiis' => '7'], ['product_id' => 2, 'number_of_skiis' => 8]]]);
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(201);
    }
}

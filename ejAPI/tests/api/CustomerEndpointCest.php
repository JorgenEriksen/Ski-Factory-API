<?php

require_once 'AuthorisationCest.php';

class CustomerEndpointCest
{
    public function _before(ApiTester $I)
    {
    }

    // Tests endpoint for getting customer order by ID.
    public function getOrdersByCustomerIdTest(ApiTester $I)
    {
        AuthorisationCest::setAuthorisationTokenCustomer($I);
        $I->sendGet('ejAPI/v1.0/customer/orders/2');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['order_nr' => 2]);
    }

    // Checks for the correct amount of production plans in the database.
    public function getProductionPlanTest(ApiTester $I)
    {
        AuthorisationCest::setAuthorisationTokenCustomer($I);
        $I->sendGet('ejAPI/v1.0/customer/productionplans');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertEquals(3, count(json_decode($I->grabResponse())));
    }
    
    // Tests endpoint for getting orders with since filter
    public function GetOrdersWithSinceFilterTest(ApiTester $I)
    {
        AuthorisationCest::setAuthorisationTokenCustomer($I);
        $I->sendGet('ejAPI/v1.0/customer/orders/2?since=2020-04-04');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->assertEquals(1, count(json_decode($I->grabResponse())));
    }
    
     public function PlaceAnOrderTest(ApiTester $I)
     {
        AuthorisationCest::setAuthorisationTokenCustomer($I);
         $I->haveHttpHeader('Content-Type', 'application/json');
         $I->sendPost('/ejAPI/v1.0/customer/orders', ['customer_id' => '9', 'skiis' => [['product_id' => '1', 'skiis_ordered' => '1']]]);
         $I->seeResponseCodeIs(201);
         $I->seeResponseIsJson();
     }
}

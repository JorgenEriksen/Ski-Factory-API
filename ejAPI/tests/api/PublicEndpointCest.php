<?php

require_once 'AuthorisationCest.php';

class PublicEndpointCest
{
    public function _before(ApiTester $I)
    {
    }

    // Tests endpoint for getting customer order by ID.
    public function TestPublicSkiisEndpoint(ApiTester $I)
    {
        $I->sendGet('ejAPI/v1.0/public/skiis');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['model' => "Active"]);
    }
    
    // Tests endpoint for getting customer order by ID.
    public function TestPublicSkiisEndpointWithModelFilter(ApiTester $I)
    {
        $I->sendGet('ejAPI/v1.0/public/skiis?model=Active');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['type_of_skiing' => "skate"]);
        $I->seeResponseContainsJson(['size' => "197"]);
        $I->seeResponseContainsJson(['grip_system' => "wax"]);
    }

    // Tests endpoint for getting customer order by ID.
    public function TestInvalidPublicSkiisEndpointWithModelFilter(ApiTester $I)
    {
        $I->sendGet('ejAPI/v1.0/public/skiis?model=197');
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['message' => "unable to find resource in the database"]);
    }

     // Tests endpoint for getting customer order by ID.
     public function TestInvalidPublicSkiisEndpoint(ApiTester $I)
     {
         $I->sendGet('ejAPI/v1.0/public/skiis/1');
         $I->seeResponseCodeIs(404);
     }
}

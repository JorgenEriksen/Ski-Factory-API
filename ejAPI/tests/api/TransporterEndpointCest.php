<?php

require_once 'AuthorisationCest.php';

class TransporterEndpointCest
{
    public function _before(ApiTester $I)
    {
    }

    // Tests endpoint for 404 seeing as we are not shipping the database with shipments ready for shipment.
    public function TestInformationAboutOrdersReadyForShipment(ApiTester $I)
    {
        AuthorisationCest::setAuthorisationTokenTransporter($I);
        $I->sendGet('ejAPI/v1.0/transporter/orders');
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('message' => "unable to find resource in the database"));
    }
}

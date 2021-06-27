<?php
require_once 'RESTConstants.php';
require_once 'controller/APIController.php';

class AuthorisationTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // series of tests that go through the specific endpoint they are supposed to be authorized for.


    public function testValidAuthorisationCustomer()
    {
        (new APIController())->authorise('4BAC27393BDD9777CE02453256C5577CD02275510B2227F473D03F533924F877', 'ejAPI/v1.0/customer/orders/2');
    }


    public function testValidAuthorisationProductionPlanner()
    {
        (new APIController())->authorise('50AD41624C25E493AA1DC7F4AB32BDC5A3B0B78ECC35B539936E3FEA7C565AF7', 'ejAPI/v1.0/employee/productionplans');
    }

    public function testValidAuthorisationStorekeeper()
    {
        (new APIController())->authorise('CA7E3F3F3391B594650E7BA0FA4787C90BCD4A3ABE5224C50C1D255A0A67A891', 'ejAPI/v1.0/employee/orders?state=skis_available');
    }

    public function testValidAuthorisationTransporter()
    {
        (new APIController())->authorise('AD0A5EECDAD5BC4B6102A8CED84CBCA4CD664BFCFDFC65D7B53B46CB6362AD42', 'ejAPI/v1.0/transporter/orders');
    }


    public function testValidAuthorisationCustomerRep()
    {
        (new APIController())->authorise('4BAC27393BDD9777CE02453256C5577CD02275510B2227F473D03F533924F877', 'ejAPI/v1.0/customer/orders/2');
    }

}

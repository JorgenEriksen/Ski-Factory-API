<?php
require_once 'controller/APIController.php';

class controllerTest extends \Codeception\Test\Unit
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

    // TESTING PUBLIC ENDPOINTS =============================================================================

    public function testValidPublicSkiResourceEndpoint()
    {
        $controller = new APIController();
        self::assertEquals(true, $controller->isValidEndpoint(['public', 'skiis'], 'GET', [], []));
    }

    public function testValidPublicSkiWithParamResourceEndpoint()
    {
        $controller = new APIController();
        self::assertEquals(true, $controller->isValidEndpoint(['public', 'skiis'], 'GET', ['model','Active'], []));
    }

    public function testInValidPublicSkiResourceEndpoint()
    {
        $controller = new APIController();
        self::assertEquals(false, $controller->isValidEndpoint(['public', 'skiis','model=race'], 'GET', [], []));
    }


    // TESTING EMPLOYEE ENDPOINTS ============================================================================

    public function testIsInvalidEmployeeEndpoint()
    {
        $controller = new APIController();
        self::assertEquals(true, $controller->isValidEndpoint(['employee', 'skiis'], 'GET', [], []));
    }

    public function testNonExistentEmployeeResource()
    {
        $controller = new APIController();
        $res = $controller->handleRequest(['employee','orders'], 'GET', [], [], 'extra');
        self::assertNotEmpty($res); // get an error message
    }


    // TESTING CUSTOMER ENDPOINTS =============================================================================

    public function testValidCustomerEndpoint()
    {
        $controller = new APIController();
        self::assertEquals(true, $controller->isValidEndpoint(['customer', 'orders', '2'], 'GET', [], []));
    }

    public function testDeleteValidCustomerEndpoint()
    {
        $controller = new APIController();
        self::assertEquals(true, $controller->isValidEndpoint(['customer', 'orders', '2'], 'DELETE', [], []));
    }

    public function testValidCustomerEndpointPlan()
    {
        $controller = new APIController();
        self::assertEquals(true, $controller->isValidEndpoint(['customer', 'productionplans'], 'GET', [], []));
    }

    public function testInvalidValidCustomerEndpoint()
    {
        $controller = new APIController();
        self::assertEquals(false, $controller->isValidEndpoint(['customer', 'skiis'], 'GET', [], []));
    }

    // TESTING TRANSPORTER ENDPOINTS ==========================================================================
    public function testValidTransporterEndpoint()
    {
        $controller = new APIController();
        self::assertEquals(true, $controller->isValidEndpoint(['transporter', 'orders'], 'GET', [], []));
    }

    public function testInValidTransporterEndpoint()
    {
        $controller = new APIController();
        self::assertEquals(false, $controller->isValidEndpoint(['transporter', 'shipments'], 'GET', [], []));
    }

}
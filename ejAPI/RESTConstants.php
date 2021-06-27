<?php

/**
 * Class RESTConstants class for application constants.
 */
class RESTConstants
{
    // HTTP method names
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    // HTTP status codes
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_ACCEPTED = 202;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_NOT_IMPLEMENTED = 501;

    // MAIN FOUR API ENDPOINTS
    const ENDPOINT_CUSTOMER = "customer";
    const ENDPOINT_EMPLOYEE = "employee";
    const ENDPOINT_TRANSPORTER = "transporter";
    const ENDPOINT_PUBLIC = "public";
  
    // SUPPLEMENT FOR MAIN API ENDPOINTS
    const ENDPOINT_SKIIS = 'skiis';
    const ENDPOINT_ORDERS = 'orders';
    const ENDPOINT_MODIFYSTATE = 'modifystate';
    const ENDPOINT_SHIPMENTS = 'shipments';
    const ENDPOINT_SPLIT = 'split';
    const ENDPOINT_PRODUCTIONPLANS = 'productionplans';
    const ENDPOINT_ORDERS_READY = 'ready';
    const ENDPOINT_ORDERS_CUSTOMERID = 'customer_id';
        
    // database errors
    const DB_ERR_ATTRIBUTE_MISSING = 1;
    const DB_ERR_FK_INTEGRITY = 2;
    
}

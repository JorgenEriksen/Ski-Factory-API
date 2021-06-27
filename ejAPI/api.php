<?php
require_once 'RESTConstants.php';
require_once 'controller/APIController.php';

header('Content-Type: application/json');

// Parse request parameters
$queries = array();
parse_str($_SERVER['QUERY_STRING'], $queries);

$uri = explode('/', $queries['request']);
unset($queries['request']);

$requestMethod = $_SERVER['REQUEST_METHOD'];

$content = file_get_contents('php://input');
if (strlen($content) > 0) {
    $payload = json_decode($content, true);
} else {
    $payload = array();
}

$token = isset($_COOKIE['auth_token']) ? $_COOKIE['auth_token'] : ''; // gets the auth token from the cookie, if there are one

$controller = new APIController();

// Check that the request is valid
if (!$controller->isValidEndpoint($uri)) {
    // Endpoint not recognised
    echo 'Unable to find the endpoint "';
    foreach ($uri as $val) {
        echo $val . '/';
    }
    echo '". Please refer to the documentation to view the valid endpoints for this api';
    http_response_code(RESTConstants::HTTP_NOT_FOUND);
    return;
}
if (!$controller->isValidMethod($uri, $requestMethod)) {
    // Method not supported
    echo 'Not a supported method: ' . $requestMethod;
    http_response_code(RESTConstants::HTTP_METHOD_NOT_ALLOWED);
    return;
}
if (!$controller->isValidPayload($uri, $requestMethod, $payload)) {
    // Payload is incorrectly formatted
    echo 'The payload sent was not valid!';
    http_response_code(RESTConstants::HTTP_BAD_REQUEST);
    return;
}

try {
    $res = $controller->handleRequest($uri, $requestMethod, $queries, $payload, $token);
    if(array_key_exists("error_code", $res)) {
        http_response_code($res["error_code"]);
        print(json_encode($res));
    } else if ($requestMethod == RESTConstants::METHOD_GET && count($res) == 0) { // if no result from the get request
        http_response_code(RESTConstants::HTTP_NOT_FOUND);
        $res['message'] = 'unable to find resource in the database';
        print(json_encode($res));
    } else if ($requestMethod == RESTConstants::METHOD_POST) { // if post request was successful
        http_response_code(RESTConstants::HTTP_CREATED);
        print(json_encode($res));
    } else {                                                   // otherwise, just give back the result
        http_response_code(RESTConstants::HTTP_OK);
        print(json_encode($res));
    }
} catch (Exception $e) {
    http_response_code(RESTConstants::HTTP_INTERNAL_SERVER_ERROR);
    return;
}


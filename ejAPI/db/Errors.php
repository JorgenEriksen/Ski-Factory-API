<?php

require_once 'RESTConstants.php';

function createErrorMessage($code = "", $title = "", $message = "") :array
{
    $res = array();
    $res['title'] = $title;
    $res['message'] = $message;
    $res['error_code'] = $code;
    return $res;
}

/**
 * creates an error message when client is not authorized
 * @return array the data returned to the client
 */
function errorMessageForbidden():array
{
    $res = array();
    $res['title'] = "Forbidden";
    $res['message'] = "The client is not authorized";
    $res['error_code'] = RESTConstants::HTTP_FORBIDDEN;
    return $res;
}

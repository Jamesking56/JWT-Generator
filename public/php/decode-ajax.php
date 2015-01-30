<?php error_reporting(0);

use JWTGenerator\JsonWebToken;

require_once("../../vendor/autoload.php");

$jsonWebToken = new JsonWebToken();
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $data = $_POST;
    if(empty($data['token']))
    {
        echo error("Token is required.");
        return;
    }
    if(empty($data['secret']))
    {
        echo error("Secret is required.");
        return;
    }

    $token = $jsonWebToken->decodeToken($data['token'], $data['secret']);

    if(!empty($token))
    {
        echo success($token);
    }
    else
    {
        echo error("Unknown error occurred.");
    }

    return;
}

function error($msg){
    $data = [
        "success" => false,
        "error" => $msg
    ];

    return json_encode($data);
}

function success($data)
{
    $data = [
        "success" => true,
        "data" => $data
    ];

    return json_encode($data);
}

echo "Hack Attempt";

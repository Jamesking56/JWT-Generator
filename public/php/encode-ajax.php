<?php error_reporting(0);

use JWTGenerator\JsonWebToken;

require_once("../../vendor/autoload.php");

$jsonWebToken = new JsonWebToken();
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // Check POST data
    $data = $_POST;
    $supportedAlgorithms = [
        "HS256",
        "HS384",
        "HS512"
    ];

    if(!in_array($data['algorithm'], $supportedAlgorithms))
    {
        echo error("Algorithm is invalid.");
        return;
    }

    if(!$data['secret'] || $data['secret'] == "")
    {
        echo error("Secret is required.");
        return;
    }
    $secret = $data['secret'];
    $algorithm = $data['algorithm'];
    unset($data['secret']);
    unset($data['algorithm']);

    // Get Private data into the correct format.
    $data['privateData'] = json_decode($data['privateData'], true);

    // Lets encode!
    $token = $jsonWebToken->encodeToken($secret, $algorithm, $data['issuer'], $data['subject'], $data['audience'], $data['expires'], $data['nbf'], $data['privateData']);
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

function success($token)
{
    $data = [
        "success" => true,
        "token" => $token
    ];

    return json_encode($data);
}

echo "Hack Attempt";

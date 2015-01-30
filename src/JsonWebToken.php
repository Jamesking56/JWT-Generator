<?php namespace JWTGenerator;
use JWT;

/**
 * Class JsonWebToken - JWT Token class.
 *
 * @package JWTGenerator
 * @author James King <james@jamesking56.co.uk>
 */
class JsonWebToken {

    public function encodeToken($secret, $algorithm = "HS256", $issuer = "", $subject = "", $audience = "", $expires = null, $nbf = null, $privateData = [])
    {
        $iat = time();
        $data = [
            "iat" => $iat
        ];

        if(!empty($issuer)) $data['iss'] = $issuer;
        if(!empty($subject)) $data['sub'] = $subject;
        if(!empty($audience)) $data['aud'] = $audience;
        if(!empty($expires)) $data['exp'] = $expires;
        if(!empty($nbf)) $data['nbf'] = $nbf;

        foreach($privateData as $key=>$value) {
            $data[$key] = $value;
        }

        return JWT::encode($data, $secret, $algorithm);
    }

    public function decodeToken($token, $secret)
    {
        return JWT::decode($token, $secret);
    }

}
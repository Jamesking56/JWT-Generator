<?php namespace JWTGenerator;
use JWT;

/**
 * Class JsonWebToken - JWT Token class.
 *
 * @package JWTGenerator
 * @author James King <james@jamesking56.co.uk>
 */
class JsonWebToken {

    /**
     * Encode a Token.
     *
     * @param string $secret        Encryption secret
     * @param string $algorithm     Encryption algorithm ('HS256', 'HS384' or 'HS512')
     * @param string $issuer        Issuer of the JWT
     * @param string $subject       Subject of the JWT
     * @param string $audience      Audience of the JWT
     * @param int|null $expires     UNIX Timestamp of when the JWT becomes invalid
     * @param int|null $nbf         UNIX Timestamp of when the JWT becomes valid
     * @param array $privateData    Array of custom data to add to the JWT
     * @return string               Encoded JWT.
     */
    public function encodeToken($secret,
                                $algorithm = "HS256",
                                $issuer = "",
                                $subject = "",
                                $audience = "",
                                $expires = null,
                                $nbf = null,
                                $privateData = [])
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
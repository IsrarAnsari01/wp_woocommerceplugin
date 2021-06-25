<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
class stripeConfiguration
{
    /**
     * Function that create Tokens
     * @param $this => This is use to get the private key | object $payload contain the required information
     * @return Response  
     */

    public function createToken($privateKey, $payload)
    {
        $endPoint = "https://api.stripe.com/v1/tokens";
        $tokenCreationResponse = wp_remote_post($endPoint,  array(
            'method'    => 'POST',
            'headers'     => array(
                'Authorization' => 'Bearer ' . $privateKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ),
            'body'      => http_build_query($payload),
            'timeout'   => 90,
            'sslverify' => false,
        ));
        return $tokenCreationResponse;
    }

    /**
     * Function that charge money for the specific product 
     * @param object $payloadForPayment contain the required information
     * @return Response  
     */

    public function createCharge($payloadForPayment)
    {
        $endPointForChargers = "https://api.stripe.com/v1/charges";
        $respose = wp_remote_post($endPointForChargers, array(
            'method'    => 'POST',
            'body'      => http_build_query($payloadForPayment),
            'timeout'   => 90,
            'sslverify' => false,
        ));
        return $respose;
    }
}
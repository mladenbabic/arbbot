<?php
/**
 * Created by PhpStorm.
 * User: mladen
 * Date: 2/21/18
 * Time: 01:16
 */

class PushServer
{

    public static function pushTrade($currencyProfitLoss = 0)
    {

        $apiKey='';
        $singleId = '';

        logg( 'Send a message to mobile to notify trader' );
        //logg(  sprintf( "Push message to mobile: %s , %s %.8f", $tradeable, $currency, formatBTC( $currencyProfitLoss) ));

        $fcmMsg = array(
            'body'  => 'New trade is made with profit ' . $currencyProfitLoss,
            'title' => 'Trade is made',
            'sound' => "true",
            'color' => "#203E78",
        );

// 'to' => $singleID ;  // expecting a single ID
// 'registration_ids' => $registrationIDs ;  // expects an array of ids
// 'priority' => 'high' ; // options are normal and high, if not set, defaults to high.

        $fcmFields = array(
            'to'           => $singleId,
            'priority'     => 'high',
            'notification' => $fcmMsg,
            'data' => $fcmMsg,
        );

        $headers = array(
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json',
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmFields));
        $result = curl_exec($ch);
        curl_close($ch);
        echo $result . "\n\n";

    }

}

?>
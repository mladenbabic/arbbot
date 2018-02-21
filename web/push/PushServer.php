<?php
/**
 * Created by PhpStorm.
 * User: mladen
 * Date: 2/21/18
 * Time: 01:16
 */

class PushServer
{

    const API_KEY = 'push.api-key';
    const SINGLE_DEVICE_ID = 'push.single-device-id';

    private static $config = [];

    public static function pushTrade($currencyProfitLoss = 0)
    {

        $apiKey   = self::get(PushServer::API_KEY, '');
        $singleId = self::get(PushServer::SINGLE_DEVICE_ID, '');

        logg('Send a message to mobile');

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
            'data'         => $fcmMsg,
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

    public static function exists($key)
    {

        $value = self::get($key, null);
        return !is_null($value) && strlen($value) > 0;

    }

    public static function get($key, $default = null)
    {

        self::refresh();

        $config = self::$config;

        $value = $config;
        $keys  = explode('.', $key);
        foreach ($keys as $k) {
            if (!key_exists($k, $value)) {
                return $default;
            }
            $value = $value[$k];
        }
        return $value;

    }

    public static function refresh($throwException = false)
    {

        $config = @parse_ini_file("config_fcm.ini", true);
        if (!$config) {
            // The web UI accesses the Config object from ../bot, so config.ini will
            // be placed in the parent directory.
            $config = @parse_ini_file("../config_fcm.ini", true);
            if (!$config && $throwException) {
                throw new Exception("Configuration not found or invalid!");
            }
        }
        logg('Config data ' . $config[0]);
        self::$config = $config;

    }

}

?>
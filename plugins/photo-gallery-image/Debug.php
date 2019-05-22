<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 7/3/2017
 * Time: 1:21 PM
 */
namespace GDGallery;


class Debug
{
    public static function trace($arr)
    {
        $client_ip = self::get_client_ip();

        if ($client_ip == GDGALLERY_ACCESS_IP && GDGALLERY_DEBUG_ENABLE === true) {
            echo "<div><pre style='background-color: #ffff82;padding: 10px;'>";
            print_r($arr);
            echo "</pre></div>";
        }
    }

    private static function get_client_ip()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}
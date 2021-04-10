<?php


namespace App\Service\constants;


class AdEnum
{
    public static $status_wait_verification = "Ожидает проверки";
    public static $status_verified = "Проверено";
    public static $status_canceled = "Отклонено";
    public static $status_rented = "Снято";

    public static function getStatusList(){
    return [self::$status_wait_verification,
        self::$status_verified,
        self::$status_canceled,
        self::$status_rented,];}
}
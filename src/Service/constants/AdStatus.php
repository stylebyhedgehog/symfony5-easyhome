<?php


namespace App\Service\constants;


class AdStatus
{
    public static $status_wait=1;
    public static $status_ok=2;
    public static $status_canceled=3;
    public static $status_rented=4;

    public static $status = [
        "Ожидает проверки"=>1,
        "Проверено"=>2,
        "Отклонено"=>3,
        "Снято"=>4
    ];
    public static $status_get = [
        1=>"Ожидает проверки",
        2=>"Проверено",
        3=>"Отклонено",
        4=>"Снято"
    ];


}
<?php


namespace App\Service\constants;


class AdStatus
{
    public static $status_wait=1;
    public static $status_ok=2;
    public static $status_canceled=3;
    public static $status_wait_deal=4;
    public static $status_rented=5;

    public static $status = [
        "Ожидает проверки"=>1,
        "Проверено"=>2,
        "Отклонено"=>3,
        "Ожидает заключения договора"=>4,
        "Снято"=>5
    ];
    public static $status_by_agent = [
        "Ожидает проверки"=>1,
        "Проверено"=>2,
        "Отклонено"=>3,
    ];
    public static $status_get = [
        1=>"Ожидает проверки",
        2=>"Проверено",
        3=>"Отклонено",
        4=>"Ожидает заключения договора",
        5=>"Снято"
    ];


}
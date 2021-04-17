<?php


namespace App\Service\constants;


class AdFilter
{
    public static $new=1;
    public static $old=2;
    public static $cheap=3;
    public static $expensive=4;

    public static $sort=[
        "Сперва новые"=>1,
        "Сперва старые"=>2,
        "Сперва дешевые"=>3,
        "Сперва дорогие"=>4
    ];

    public static $sort_date=[
        "Сперва новые"=>1,
        "Сперва старые"=>2,
    ];

    public static $status = [
        "Ожидает проверки"=>1,
        "Проверено"=>2,
        "Отклонено"=>3,
        "Снято"=>4,
        "Все"=>5
    ];
}
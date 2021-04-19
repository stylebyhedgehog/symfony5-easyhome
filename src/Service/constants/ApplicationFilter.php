<?php


namespace App\Service\constants;


class ApplicationFilter
{
    public static $new=1;
    public static $old=2;

    public static $sort=[
        "Сперва новые"=>1,
        "Сперва старые"=>2
    ];

    public static $status = [
        "Ожидает ответа хозяина"=>1,
        "Отклонено хозяином"=>2,
        "Принято хозяином, ожидает проверки агентом"=>3,
        "Принято агентом"=>4,
        "Отклонено агентом"=>5,
        "Выполнена"=>6,
        "Все"=>7
    ];
}
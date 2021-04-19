<?php


namespace App\Service\constants;


class ApplicationStatus
{
    public static $status_wait_owner=1;
    public static $status_canceled_owner=2;
    public static $status_wait_agent=3;
    public static $status_accept_agent=4;
    public static $status_canceled_agent=5;
    public static $status_done=6;

    public static $status = [
        "Ожидает ответа хозяина"=>1,
        "Отклонено хозяином"=>2,
        "Принято хозяином, ожидает проверки агентом"=>3,
        "Принято агентом"=>4,
        "Отклонено агентом"=>5,
        "Выполнена"=>6
    ];
    public static $status_get = [
        1=>"Ожидает ответа хозяина",
        2=>"Отклонено хозяином",
        3=>"Принято хозяином, ожидает проверки агентом",
        4=>"Принято агентом",
        5=>"Отклонено агентом",
        6=>"Выполнена"
    ];
}
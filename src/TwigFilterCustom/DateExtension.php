<?php


namespace App\TwigFilterCustom;


use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DateExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('date_interval', [$this, 'dateInterval']),
        ];
    }

    public function dateInterval($date)
    {

        return $this->getPeriod($date, new DateTime());
    }
    function getPeriod($date1,$date2){
        $interval = date_diff($date1, $date2);
        $y='';$m='';$d='';

        if ($interval->y>0) {
            if ($interval->y>4)
                $y .=$interval->y . ' лет';
            else if ($interval->y == 1)
                $y .=$interval->y . ' год';
            else
                $y .=$interval->y . ' года';
            $y .= ', ';
        }

        if ($interval->m>0) {
            if ($interval->m>4)
                $m .= $interval->m . ' месяцев';
            else if ($interval->m>1)
                $m .= $interval->m . ' месяца';
            else
                $m .= $interval->m . ' месяц';
            $m .= ', ';
        }

        if ($interval->d>0) {
            if ($interval->d>4)
                $d .= $interval->d . ' дней';
            else if ($interval->d>1)
                $d .= $interval->d . ' дня';
            else
                $d .= $interval->d . ' день';
        }
        else
            return "сегодня";

        return $y . $m . $d . " назад";
    }
}
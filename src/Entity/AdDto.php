<?php


namespace App\Entity;


class AdDto
{
    /**
     * @var string
     */
    public $q;

    /**
     * @var integer
     */
    public $sort_param;

    /**
     * @var string|null
     */
    public $city;
    /**
     * @var integer|null
     */
    public $min_price;

    /**
     * @var integer|null
     */
    public $max_price;

    /**
     * @var float|null
     */
    public $min_sqr;

    /**
     * @var float|null
     */
    public $max_sqr;

    /**
     * @var integer|null
     */
    public $choice_status;


}
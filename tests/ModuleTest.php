<?php

namespace App\Tests;

use App\Entity\Ad;
use App\Entity\PersonalData;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class ModuleTest extends TestCase
{

    /** @test
     */
    public function testAdCreate(): void
    {
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $ad = new Ad();
        $ad->setCity("Казань");
        $ad->setRegion("Татарстан");
        $ad->setCreateDate(new \DateTime());
        $ad->setUpdateDate(new \DateTime());
        $ad->setPrice(4444);
        $ad->setDescription("asd");
        $ad->setHouseNumber("4");
        $ad->setSqr("44.2");
        $ad->setStreet("Кремлевская");
        $ad->setStreetType("ул");
        $ad->setTypeRent("Продажа");
        $errors = $validator->validate($ad);
        $this->assertCount(0,$errors);
    }
    public function testPersonalDataCreate(): void
    {
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $application = new PersonalData();
        $application->setName("Бублик");
        $application->setSurname("Сладкий");
        $application->setPassport("33344444");
        $errors = $validator->validate($application);
        $this->assertCount(0,$errors);
    }
}

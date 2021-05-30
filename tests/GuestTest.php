<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
//vendor\bin\phpunit tests
class GuestTest extends WebTestCase
{
    /** @test */
    public function loadingSite(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Log in!');
    }
    /** @test */
    public function LogIn(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $form = $crawler->selectButton('Войти')->form();
        $client->submit($form, array('email' => 'asd@asd.ru','password'=>'123'));
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'EasyHome');
    }

}

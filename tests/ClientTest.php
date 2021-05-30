<?php

namespace App\Tests;

use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
//vendor\bin\phpunit tests
class ClientTest extends WebTestCase
{

    public function logIn()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(ClientRepository::class);
        $testUser = $userRepository->findOneByEmail('admin@admin.ru');
        $client->loginUser($testUser);
        return $client;
    }

    /** @test */
    public function personalArea(): void
    {
        $client=$this->logIn();
        $client->request('GET', '/client/1/personal_data/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Личный кабинет');
    }
    /** @test */
    public function ownReviews(): void
    {
        $client=$this->logIn();
        $client->request('GET', '/client/1/review/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Отзывы');
    }
    /** @test */
    public function anotherClientReviews(): void
    {
        $client=$this->logIn();
        $client->request('GET', '/client/2/review/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Отзывы');
    }

    /** @test */
    public function formSubmit(): void
    {
        $client=$this->logIn();
        $crawler=$client->request('GET', '/client/7/personal_data/create');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Заполнить');
        $form = $crawler->selectButton('Сохранить')->form();

        $form['personal_data[name]'] = 'asd';
        $form['personal_data[surname]'] = 'asd';
        $form['personal_data[passport]'] = 'asd';
        $client->submit($form);
        $client->followRedirect();
        $this->assertSelectorTextContains('title', 'Личный кабинет');
        $this->assertResponseIsSuccessful();
    }
}

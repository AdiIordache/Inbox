<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PersonControllerTest extends WebTestCase
{
    public function testNewperson()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/newPerson');
    }

    public function testModifyperson()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/modifyPerson');
    }

    public function testDeleteperson()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/deletePerson');
    }

}

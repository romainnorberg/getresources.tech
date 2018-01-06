<?php

namespace App\Tests\Controller;

use App\Tests\AppWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RegistrationControllerTest extends AppWebTestCase
{
    private $filterXPathForm = "//*[@name='user_form']";

    /**
     * @dataProvider urlProvider
     *
     * @param $url
     */
    public function testPageIsSuccessful($url)
    {
        $this->client->request('GET', $url);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function urlProvider()
    {
        yield ['/login'];
        yield ['/register'];
    }

    public function testShowRegisterForm()
    {
        $url = $this->client->getContainer()->get('router')->generate('user_registration', []);
        $crawler = $this->client->request('GET', $url);

        $response = $this->client->getResponse();

        // Form
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertContains('Register', $response->getContent());

        return $crawler;
    }

    /**
     * @depends testShowRegisterForm
     */
    public function testSubmitRegisterFormWithMissingField($crawler)
    {
        $values = [
            'user_form[username]' => 'user-test',
        ];
        $response = $this->submitForm($crawler, $values);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertContains('This value should not be blank', $response->getContent());
    }

    /**
     * @depends testShowRegisterForm
     */
    public function testSubmitRegisterFormWithDifferentPassword($crawler)
    {
        $values = [
            'user_form[username]'              => 'user-test',
            'user_form[email]'                 => 'email@example.com',
            'user_form[plainPassword][first]'  => 'password',
            'user_form[plainPassword][second]' => 'p1ssw0rd',
        ];
        $response = $this->submitForm($crawler, $values);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertContains('This value is not valid', $response->getContent());
    }

    private function submitForm($crawler, $values)
    {
        // Test 2: fill form with error
        $form = $crawler->filterXPath($this->filterXPathForm)->form();
        $form->setValues($values);
        $this->client->submit($form);

        return $this->client->getResponse();
    }
}
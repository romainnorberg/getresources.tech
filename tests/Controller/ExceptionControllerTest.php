<?php

namespace App\Tests\Controller;

use App\Tests\AppWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ExceptionControllerTest extends AppWebTestCase
{
    /**
     * @dataProvider errorPagesProvider()
     *
     * @param       $errorCode
     * @param array $assertEquals
     */
    public function testHtmlErrorPages($errorCode, array $assertEquals): void
    {
        $url = $this->client->getContainer()->get('router')->generate('_twig_error_test', [
            '_format' => 'html',
            'code'    => $errorCode,
        ]);
        $crawler = $this->client->request('GET', $url);
        $response = $this->client->getResponse();

        // Asserts
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        foreach ($assertEquals as $XPath => $expected) {
            $this->assertEquals($expected, $crawler->filterXPath($XPath)->text());
        }
    }

    public function errorPagesProvider(): array
    {
        return [
            [
                'error_code'    => 500,
                'assert_equals' => [
                    "//h3[@class='title is-2']"    => "We've a problem.", // XPath => content
                    "//h4[@class='subtitle is-4']" => 'An error occurred with this HTTP request. We have been notified of the issue.',
                ],
            ],
            [
                'error_code'    => 404,
                'assert_equals' => [
                    "//h3[@class='title is-2']" => 'Oups, not found.', // XPath => content
                ],
            ],
            [
                'error_code'    => 403,
                'assert_equals' => [
                    "//h3[@class='title is-2']" => 'Access Denied.', // XPath => content
                ],
            ],
        ];
    }
}
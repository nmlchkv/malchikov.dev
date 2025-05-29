<?php

namespace Tests\Unit\Services;

use App\Services\UrlService;
use PHPUnit\Framework\TestCase;

class UrlServiceTest extends TestCase
{
    private UrlService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UrlService();
    }

    /** @test */
    public function it_normalizes_url_correctly()
    {
        $url = 'HTTP://EXAMPLE.COM/SomePath/';
        $expected = 'http://example.com/somepath';

        $result = $this->service->normalizeUrl($url);

        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function it_adds_default_scheme_if_missing()
    {
        $url = 'example.com/';
        $expected = 'https://example.com';

        $result = $this->service->normalizeUrl($url);

        $this->assertEquals($expected, $result);
    }
}

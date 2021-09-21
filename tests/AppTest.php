<?php

use HabrReverseProxy\App;
use PHPUnit\Framework\TestCase;
use simplehtmldom\HtmlWeb;

final class AppTest extends TestCase
{
    private $client;
    private $path;
    private $app;

    protected function setUp(): void
    {
        $this->app = new App('');
        $this->client = new HtmlWeb();
        $this->path = 'https://habr.com/ru/all/';
    }

    protected function tearDown(): void
    {
    }

    public function testHttpResponseCode(): void
    {
//        $this->assertEquals('200', $this->app->getHttpResponseCode());
    }

    public function testRunResponse(): void
    {
//        $this->assertStringNotEqualsFile($this->testdata, $this->app->run());
    }

    public function testAddProtocolWithoutHtts (): void
    {
//        $this->assertEquals('https:', $this->app->addProtocol($this->path_without_protocol));
    }

    public function testAddProtocolWithHtts (): void
    {
//        $this->assertEquals('', $this->app->addProtocol($this->path_with_protocol));
    }

}

<?php

namespace NwSilexTest\Testing;

use NwSilex\Foundation\Application;
use NwSilex\Testing\TestCase;

class TestCaseTest extends \PHPUnit_Framework_TestCase
{
    public function testGetHello()
    {
        $stubTestCase = new StubTestCase();
        $stubTestCase->setUp();

        $response = $stubTestCase->call('GET', '/hello');
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('world', $response->getContent());
    }
}

class StubTestCase extends TestCase
{
    public function createApplication()
    {
        $app = new Application();

        $app->match('/hello', function () {
            return 'world';
        });

        $app->match('/html', function () {
            return '<h1>title</h1>';
        });

        $app->match('/server', function () use ($app) {
            $user = $app['request']->server->get('PHP_AUTH_USER');
            $pass = $app['request']->server->get('PHP_AUTH_PW');

            return "<h1>$user:$pass</h1>";
        });

        return $app;
    }
}
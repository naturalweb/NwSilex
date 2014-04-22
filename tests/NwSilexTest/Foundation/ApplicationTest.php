<?php

namespace NwSilexTest\Foundation;

use NwSilex\Foundation\Application;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Monolog\Logger;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testMethodTwig()
    {
        $app = new Application;

        $view = 'path.view';
        $params = array('foo' => 'bar');

        $app['twig'] = $twig = $this->getMockBuilder('Twig_Environment')->disableOriginalConstructor()->getMock();
        $twig->expects($this->once())
            ->method('render')
            ->with($view, $params)
            ->will($this->returnValue('html-render'));
        
        $response = $app->twig($view, $params);

        $this->assertEquals('Symfony\Component\HttpFoundation\Response', get_class($response));
        $this->assertEquals('html-render', $response->getContent());
    }

    public function testMethodConfig()
    {
        $app = new Application;
        $app['app.config'] = array('foobar' => 'baz');

        $value = $app->config('foobar');

        $this->assertEquals('baz', $value);
    }

    public function testMethodPath()
    {
        $app = new Application;

        $route = 'home';
        $params = array('foo' => 'bar');
        $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH;

        $app['url_generator'] = $url_generator = $this->getMockBuilder('Symfony\Component\Routing\Generator\UrlGeneratorInterface')->disableOriginalConstructor()->getMock();
        $url_generator->expects($this->once())
                      ->method('generate')
                      ->with($route, $params, $referenceType)
                      ->will($this->returnValue('http://localhost'));

        $return = $app->path($route, $params);

        $this->assertEquals('http://localhost', $return);
    }

    public function testMethodUrl()
    {
        $app = new Application;

        $route = 'home';
        $params = array('foo' => 'bar');
        $referenceType = UrlGeneratorInterface::ABSOLUTE_URL;

        $app['url_generator'] = $url_generator = $this->getMockBuilder('Symfony\Component\Routing\Generator\UrlGeneratorInterface')->disableOriginalConstructor()->getMock();
        $url_generator->expects($this->once())
                      ->method('generate')
                      ->with($route, $params, $referenceType)
                      ->will($this->returnValue('http://localhost'));
        
        $return = $app->url($route, $params);

        $this->assertEquals('http://localhost', $return);
    }

    public function testMethodMail()
    {
        $app = new Application;

        $view = 'path.view';
        $data = array('foo' => 'bar');
        $callback = function(){};
        $failedRecipients = array();

        $failures = array('test@test.com');

        $app['app.mailer'] = $mailler = $this->getMockBuilder('NwSilex\Mail\Mailer')->disableOriginalConstructor()->getMock();
        $mailler->expects($this->once())
                ->method('send')
                ->with($view, $data, $callback)
                ->will($this->returnValue(true));
        
        $mailler->expects($this->once())
                ->method('failures')
                ->will($this->returnValue($failures));

        $success = $app->mail($view, $data, $callback, $failedRecipients);

        $this->assertTrue($success);
        $this->assertEquals($failures, $failedRecipients);
    }

    public function testMethodSessionWithEmpty()
    {
        $app = new Application;

        $app['session'] = $session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session\SessionInterface')->disableOriginalConstructor()->getMock();

        $this->assertSame($session, $app->session());
    }

    public function testMethodSessionWithGet()
    {
        $app = new Application;
        
        $key = 'key';
        $app['session'] = $session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session\SessionInterface')->disableOriginalConstructor()->getMock();
        $session->expects($this->once())
                ->method('get')
                ->with($key)
                ->will($this->returnValue('foobar'));

        $this->assertEquals('foobar', $app->session($key));
    }

    public function testMethodSessionWithSet()
    {
        $app = new Application;
        
        $key = 'key';
        $value = 'foobar';
        $app['session'] = $session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session\SessionInterface')->disableOriginalConstructor()->getMock();
        $session->expects($this->once())
                ->method('set')
                ->with($key, $value)
                ->will($this->returnValue($value));

        $this->assertEquals($value, $app->session($key, $value));
    }

    public function testMethodLog()
    {
        $app = new Application;
        
        $level = Logger::ERROR;
        $message = 'baz error';
        $context = array('test');

        $app['monolog'] = $monolog = $this->getMockBuilder('Monolog\Logger')->disableOriginalConstructor()->getMock();
        $monolog->expects($this->once())
                ->method('addRecord')
                ->with($level, $message, $context)
                ->will($this->returnValue(true));

        $this->assertTrue($app->log($message, $context, $level));
    }
}
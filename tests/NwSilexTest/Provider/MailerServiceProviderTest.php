<?php

namespace NwSilexTest\Provider;

use NwSilex\Foundation\Application;
use NwSilex\Provider\MailerServiceProvider;
use NwSilex\Mail\Mailer;


class MailerServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testMailerServiceProvider()
    {
        $app = new Application();

        $app['app.config'] = array('mail' => array());
        $app['logger']  = $this->getMockBuilder('Monolog\Logger')->disableOriginalConstructor()->getMock();
        $app['twig'] = $this->getMockBuilder('Twig_Environment')->disableOriginalConstructor()->getMock();
        $app['mailer'] = $this->getMockBuilder('Swift_Mailer')->disableOriginalConstructor()->getMock();

        $mailer = new Mailer($app['mailer'], $app['twig'], $app['app.config']['mail'], $app['logger']);

        $app->register(new MailerServiceProvider());
        $app->boot();
        
        $this->assertInstanceOf('NwSilex\Mail\Mailer', $app['app.mailer']);
    }
}
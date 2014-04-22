<?php

namespace NwSilexTest\Foundation;

use NwSilex\Mail\Mailer;

class MailerTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $swift  = $this->getMockBuilder('Swift_Mailer')->disableOriginalConstructor()->getMock();
        $twig   = $this->getMock('Twig_Environment');
        $from = array('address' => 'foo@bar.com', 'name' => 'FooBar');
        $config = array('from' => $from, 'pretend' => true);
        $logger = $this->getMockBuilder('Monolog\Logger')->disableOriginalConstructor()->getMock();

        $mailer = new Mailer($swift, $twig, $config, $logger);

        $this->assertAttributeEquals($swift,  'swift',  $mailer);
        $this->assertAttributeEquals($twig,   'twig',   $mailer);
        $this->assertAttributeEquals($logger, 'logger', $mailer);
        $this->assertAttributeEquals($from, 'from', $mailer);
        $this->assertAttributeEquals(true, 'pretending', $mailer);
    }

    public function testMethodsAlwaysFromAndPretend()
    {
        $swift  = $this->getMockBuilder('Swift_Mailer')->disableOriginalConstructor()->getMock();
        $twig   = $this->getMock('Twig_Environment');
        $from = array('address' => 'foo@bar.com', 'name' => 'FooBar');

        $mailer = new Mailer($swift, $twig);

        $this->assertAttributeEquals(null, 'logger', $mailer);
        $this->assertAttributeEquals(array('address' => null, 'name' => null), 'from', $mailer);
        $this->assertAttributeEquals(false, 'pretending', $mailer);

        $mailer->alwaysFrom($from['address'], $from['name']);
        $this->assertAttributeEquals($from, 'from', $mailer);

        $mailer->pretend(true);
        $this->assertAttributeEquals(true, 'pretending', $mailer);
    }

    public function testSendWithPretend()
    {
        $swift  = $this->getMockBuilder('Swift_Mailer')->disableOriginalConstructor()->getMock();
        $twig   = $this->getMock('Twig_Environment');
        $twig->expects($this->once())
             ->method('render')
             ->with('view', array('test'))
             ->will($this->returnValue('html-renderized'));

        $mailer = new Mailer($swift, $twig);
        $mailer->pretend(true);

        $callback = function($message){};

        $this->assertEquals(1,  $mailer->send('view', array('test'), $callback));
    }

    public function testSend()
    {
        $swift  = $this->getMockBuilder('Swift_Mailer')->disableOriginalConstructor()->getMock();
        $swift->expects($this->once())
              ->method('send')
              ->will($this->returnValue(true));

        $twig   = $this->getMock('Twig_Environment');
        $twig->expects($this->once())
             ->method('render')
             ->with('view', array('test'))
             ->will($this->returnValue('html-renderized'));

        $mailer = new Mailer($swift, $twig);

        $callback = function($message){};

        $this->assertEquals(true,  $mailer->send('view', array('test'), $callback));
    }

    public function testErrorSend()
    {
        $swift  = $this->getMockBuilder('Swift_Mailer')->disableOriginalConstructor()->getMock();
        $swift->expects($this->once())
              ->method('send')
              ->will($this->returnValue(false));

        $twig   = $this->getMock('Twig_Environment');
        $twig->expects($this->once())
             ->method('render')
             ->with('view', array('test'))
             ->will($this->returnValue('html-renderized'));

        $from = array('address' => 'foo@bar.com', 'name' => 'FooBar');
        $config = array('from' => $from);
        $logger = $this->getMockBuilder('Monolog\Logger')->disableOriginalConstructor()->getMock();
        $logger->expects($this->once())
               ->method('info');

        $mailer = new Mailer($swift, $twig, $config, $logger);

        $callback = function($message){};

        $this->assertEquals(false,  $mailer->send('view', array('test'), $callback));
    }
}
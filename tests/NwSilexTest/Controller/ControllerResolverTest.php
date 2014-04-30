<?php

namespace NwSilexTest\Controller;

use NwSilex\Controller\ControllerResolver;
use NwSilex\Foundation\Request;

class ControllerResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testGetController()
    {
        $mockApp        = $this->getMock('NwSilex\Foundation\Application');

        $mockController = $this->getMock('NwSilex\Controller\AbstractController', array('__invoke', 'setRequest', 'setApplication'));
        $mockController->expects($this->once())
                       ->method('setRequest');

        $mockController->expects($this->once())
                       ->method('setApplication')
                       ->with($mockApp);
        
        $attributes = array('_controller' => array($mockController));
        $request = new Request(array(), array(), $attributes);

        $resolver = new ControllerResolver($mockApp);
        
        $this->assertEquals(array($mockController), $resolver->getController($request));
    }
}
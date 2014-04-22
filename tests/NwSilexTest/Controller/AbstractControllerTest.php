<?php

namespace NwSilexTest\Controller;

class AbstractControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $class       = 'NwSilex\Controller\AbstractController';
        $mockRequest = $this->getMock('NwSilex\Foundation\Request');
        $mockApp     = $this->getMock('NwSilex\Foundation\Application');
        $args = array($mockRequest, $mockApp);
        
        $instance = $this->getMockForAbstractClass($class, $args);
        
        $this->assertAttributeEquals($mockRequest, 'request', $instance);
        $this->assertAttributeEquals($mockApp, 'app', $instance);
    }
    
    public function testSetAndGetRequest()
    {
        $class    = 'NwSilex\Controller\AbstractController';
        $instance = $this->getMockForAbstractClass($class);
        
        $mockRequest = $this->getMock('NwSilex\Foundation\Request');
        $this->assertAttributeEquals(null, 'request', $instance);
        $this->assertNull($instance->getRequest());
        
        $instance->setRequest($mockRequest);
        $this->assertAttributeEquals($mockRequest, 'request', $instance);
        $this->assertEquals($mockRequest, $instance->getRequest());
    }

    public function testSetAndGetApplication()
    {
        $class    = 'NwSilex\Controller\AbstractController';
        $instance = $this->getMockForAbstractClass($class);
        
        $mockApp = $this->getMock('NwSilex\Foundation\Application');
        $this->assertAttributeEquals(null, 'app', $instance);
        $this->assertNull($instance->getApplication());
        
        $instance->setApplication($mockApp);
        $this->assertAttributeEquals($mockApp, 'app', $instance);
        $this->assertEquals($mockApp, $instance->getApplication());
    }
    
    public function testCall()
    {
        $class    = 'NwSilex\Controller\AbstractController';
        $instance = $this->getMockForAbstractClass($class);
        
        $param = 'test';
        
        $mockApp = $this->getMock('NwSilex\Foundation\Application');
        $mockApp->expects($this->any())
                ->method('config')
                ->with($param)
                ->will($this->returnValue('valueFooBar'));
        
        $instance->setApplication($mockApp);
        
        $this->assertEquals('valueFooBar', $instance->config($param));
    }
}


<?php

namespace NwSilexTest\Foundation;

use NwSilex\Foundation\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testInstanceOf()
    {
        $request = new Request;

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $request);
    }

    public function testAjaxMethod()
    {
        $request = Request::create('/', 'GET');
        $this->assertFalse($request->ajax());
        $request = Request::create('/', 'GET', array(), array(), array(), array('HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'), '{}');
        $this->assertTrue($request->ajax());
    }

    public function testAllInputReturnsInputAndFiles()
    {
        $file = $this->getMock('Symfony\Component\HttpFoundation\File\UploadedFile', null, array(__FILE__, 'photo.jpg'));
        $request = Request::create('/?boom=breeze', 'GET', array('foo' => 'bar'), array(), array('baz' => $file));
        $this->assertEquals(array('foo' => 'bar', 'baz' => $file, 'boom' => 'breeze'), $request->all());
    }

    public function testInputMethod()
    {
        $request = Request::create('/', 'GET', array('name' => 'Taylor'));
        $this->assertEquals('Taylor', $request->input('name'));
        $this->assertEquals('Bob', $request->input('foo', 'Bob'));
    }

    public function testOnlyMethod()
    {
        $request = Request::create('/', 'GET', array('name' => 'Taylor', 'age' => 25));
        $this->assertEquals(array('age' => 25), $request->only('age'));
        $this->assertEquals(array('name' => 'Taylor', 'age' => 25), $request->only('name', 'age'));
    }

    public function testExceptMethod()
    {
        $request = Request::create('/', 'GET', array('name' => 'Taylor', 'age' => 25));
        $this->assertEquals(array('name' => 'Taylor'), $request->except('age'));
        $this->assertEquals(array(), $request->except('age', 'name'));
    }
}
<?php

namespace NwSilex\Controller;

use Silex\ControllerResolver as BaseControllerResolver;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class ControllerResolver extends BaseControllerResolver
{
    public function getController(SymfonyRequest $request)
    {
        $callable = parent::getController($request);
        
        if (is_array($callable)) {
	        if (isset($callable[0]) and $callable[0] instanceof ControllerInterface) {
	        	$callable[0]->setRequest($request);
	        	$callable[0]->setApplication($this->app);
	        }
	    }
        return $callable;
    }
}
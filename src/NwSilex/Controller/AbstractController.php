<?php
namespace NwSilex\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Abstract Controller
 */
abstract class AbstractController implements ControllerInterface
{
	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @var Application
	 */
	protected $app;

	/**
	 * Construct Controller
	 *
	 * @param Request $request
	 * @param Application $app
	 */
	public function __construct(Request $request = null, Application $app = null)
    {
    	$this->request = $request;
    	$this->app = $app;
    }
    
	/**
	 * Set Request
	 *
	 * @param Request $request
	 *
	 * @return void
	 */
	public function setRequest(Request $request)
    {
    	$this->request = $request;
    }

    /**
	 * Get Request
	 *
	 * @return Request
	 */
	public function getRequest()
    {
    	return $this->request;
    }

    /**
	 * Set Application
	 *
	 * @param Application $app
	 * 
	 * @return void
	 */
	public function setApplication(Application $app)
    {
    	$this->app = $app;
    }

    /**
	 * Get Application
	 *
	 * @return Application
	 */
	public function getApplication()
    {
    	return $this->app;
    }

    /**
	 * Call Method Application
	 *
	 * @param string $method
	 * @param array  $parameters
	 *
	 * @return mixed
	 */
    public function __call($method, array $parameters)
    {
    	return call_user_func_array(array($this->app, $method), $parameters);
    }
}
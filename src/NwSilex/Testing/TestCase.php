<?php

namespace NwSilex\Testing;

use NwSilex\Foundation\Application;
use Symfony\Component\HttpKernel\Client;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Application
	 */
	protected $app;

	/**
	 * @var Client
	 */
	protected $client;

	/**
	 * Setup the test environment.
	 *
	 * @return void
	 */
	public function setUp()
	{
		if ( ! $this->app)
		{
			$this->refreshApplication();
		}
	}

	/**
	 * Refresh the application instance.
	 *
	 * @return void
	 */
	protected function refreshApplication()
	{
		$this->app = $this->createApplication();

		$this->client = $this->createClient();

		$this->app->boot();
	}

	/**
     * Creates the application.
     *
     * @return Application
     */
    abstract public function createApplication();

    /**
     * Creates a Client.
     *
     * @param array $server An array of server parameters
     *
     * @return Client A Client instance
     */
    public function createClient(array $server = array())
    {
        return new Client($this->app, $server);
    }

    /**
	 * Call the given URI and return the Response.
	 *
	 * @param  string  $method
	 * @param  string  $uri
	 * @param  array   $parameters
	 * @param  array   $files
	 * @param  array   $server
	 * @param  string  $content
	 * @param  bool    $changeHistory
	 * @return \Illuminate\Http\Response
	 */
	public function call()
	{
		call_user_func_array(array($this->client, 'request'), func_get_args());

		return $this->client->getResponse();
	}
}
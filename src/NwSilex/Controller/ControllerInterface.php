<?php

namespace NwSilex\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

interface ControllerInterface
{
	/**
	 * Set Request
	 *
	 * @param Request $request
	 *
	 * @return void
	 */
	public function setRequest(Request $request);

    /**
	 * Get Request
	 *
	 * @return Request
	 */
	public function getRequest();

    /**
	 * Set Application
	 *
	 * @param Application $app
	 * 
	 * @return void
	 */
	public function setApplication(Application $app);

    /**
	 * Get Application
	 *
	 * @return Application
	 */
	public function getApplication();
}

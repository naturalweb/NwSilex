<?php

namespace NwSilex\Foundation;

use Closure;
use Silex\Application as SilexApplication;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use NwSilex\Foundation\Request;
use Monolog\Logger;

class Application extends SilexApplication
{
    /**
     * Renders a view with twig.
     *
     * @param string $view       The view name
     * @param array  $parameters An array of parameters to pass to the view
     *
     * @return Response A Response instance
     */
    public function twig($view, array $parameters = array())
    {
        $response = new SymfonyResponse();

        $response->setContent($this['twig']->render($view, $parameters));

        return $response;
    }

    /**
     * Get Value Config
     *
     * @param string   $config
     *
     * @return mixed
     */

    public function config($config)
    {
        return isset($this['app.config'][$config]) ? $this['app.config'][$config] : null;
    }

    /**
     * Generates a path from the given parameters.
     *
     * @param string $route      The name of the route
     * @param mixed  $parameters An array of parameters
     *
     * @return string The generated path
     */
    public function path($route, $parameters = array())
    {
        return $this['url_generator']->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    /**
     * Generates an absolute URL from the given parameters.
     *
     * @param string $route      The name of the route
     * @param mixed  $parameters An array of parameters
     *
     * @return string The generated URL
     */
    public function url($route, $parameters = array())
    {
        return $this['url_generator']->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * Sends an email.
     *
     * @param string   $view
     * @param array    $data
     * @param Closure  $callback
     * @param array    $failedRecipients An array of failures by-reference
     *
     * @return int The number of sent messages
     */

    public function mail($view, array $data, Closure $callback, &$failedRecipients = null)
    {
        $success = $this['app.mailer']->send($view, $data, $callback);

        $failedRecipients = $this['app.mailer']->failures();
        
        return $success;
    }

    /**
     * Sessions
     *
     * @param string   $key
     * @param mixed    $value
     *
     * @return Session
     */
    public function session($key = null, $value = null)
    {
        if (is_string($key) && !empty($key)) {
            if (is_null($value)) {
                return $this['session']->get($key);
            }

            return $this['session']->set($key, $value);
        }

        return $this['session'];
    }

    /**
     * Adds a log record.
     *
     * @param string  $message The log message
     * @param array   $context The log context
     * @param integer $level   The logging level
     *
     * @return Boolean Whether the record has been processed
     */
    public function log($message, array $context = array(), $level = Logger::INFO)
    {
        return $this['monolog']->addRecord($level, $message, $context);
    }
    
    /**
     * Handles the request and delivers the response.
     *
     * @param Request|null $request Request to process
     */
    public function run(SymfonyRequest $request = null)
    {
        if (null === $request) {
            $request = Request::createFromGlobals();
        }
        
        parent::run($request);
    }
}

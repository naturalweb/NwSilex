<?php 

namespace NwSilex\Mail;

use Closure;
use Swift_Mailer;
use Swift_Message;
use Twig_Environment;
use Monolog\Logger;

class Mailer {

	/**
	 * The Twig Environment instance.
	 *
	 * @var \Twig_Environment
	 */
	protected $twig;

	/**
	 * The Swift Mailer instance.
	 *
	 * @var \Swift_Mailer
	 */
	protected $swift;

	/**
	 * The global from address and name.
	 *
	 * @var array
	 */
	protected $from;

	/**
	 * The log writer instance.
	 * 
	 * @var Logger
	 */
	protected $logger;

	/**
	 * Indicates if the actual sending is disabled.
	 *
	 * @var bool
	 */
	protected $pretending = false;

	/**
	 * Array of failed recipients.
	 *
	 * @var array
	 */
	protected $failedRecipients = array();

	/**
	 * Create a new Mailer instance.
	 *
	 * @param  \Twig_Environment $twig
	 * @param  \Swift_Mailer     $swift
	 * @param  array             $config
	 * @return void
	 */
	public function __construct(Swift_Mailer $swift, Twig_Environment $twig, array $config = array(), Logger $logger = null)
	{
		$this->twig   = $twig;
		$this->swift  = $swift;
		$this->logger = $logger;

		$address = isset($config['from']['address']) ? $config['from']['address'] : null;
		$name = isset($config['from']['name']) ? $config['from']['name'] : null;
		$pretend = isset($config['pretend']) ? (bool) $config['pretend'] : false;

		$this->alwaysFrom($address, $name);
		$this->pretend($pretend);
	}

	/**
	 * Send a new message using a view.
	 *
	 * @param  string  $view
	 * @param  array  $data
	 * @param  \Closure  $callback
	 * @return int
	 */
	public function send($view, array $data, Closure $callback)
	{
		$this->failedRecipients = array();

		$message = $this->createMessage();

		$this->callMessageBuilder($callback, $message);

		$this->addContent($message, $view, $data);

		return $this->sendSwiftMessage($message);
	}

	/**
	 * Send a Swift Message instance.
	 *
	 * @param  \Swift_Message  $message
	 * @return int
	 */
	protected function sendSwiftMessage(Swift_Message $message)
	{
		if ( ! $this->pretending)
		{	
			$return = $this->swift->send($message, $this->failedRecipients);

			if (!$return) {
				$this->logMessage("Error Send Email", $message);
			}

			return $return;
		}
		else
		{
			//$this->logMessage("Pretending to mail message", $message);

			return 1;
		}
	}

	/**
	 * Add the content to a given message.
	 *
	 * @param  \Swift_Message  $message
	 * @param  string  $view
	 * @param  array   $data
	 * @return void
	 */
	protected function addContent(Swift_Message $message, $view, $data)
	{
		$html = $this->twig->render($view, $data);

		$message->setBody($html, 'text/html');
	}

	/**
	 * Call the provided message builder.
	 *
	 * @param  \Closure        $callback
	 * @param  \Swift_Message  $message
	 * @return mixed
	 */
	protected function callMessageBuilder(Closure $callback, Swift_Message $message)
	{
		return call_user_func($callback, $message);
	}

	/**
	 * Create a new message instance.
	 *
	 * @return \Swift_Message
	 */
	protected function createMessage()
	{
		$message = Swift_Message::newInstance();

		if (isset($this->from['address'])) {
			$message->setFrom($this->from['address'], $this->from['name']);
		}

		return $message;
	}

	/**
	 * Set the global from address and name.
	 *
	 * @param  string  $address
	 * @param  string  $name
	 * @return void
	 */
	public function alwaysFrom($address, $name = null)
	{
		$this->from = compact('address', 'name');
	}

	/**
	 * Tell the mailer to not really send messages.
	 *
	 * @param  bool  $value
	 * @return void
	 */
	public function pretend($value = true)
	{
		$this->pretending = $value;
	}

	/**
	 * Log that a message was sent.
	 *
	 * @param  string         $msg
	 * @param  \Swift_Message $message
	 * 
	 * @return void
	 */
	protected function logMessage($msg, Swift_Message $message)
	{
		if ($this->logger) {
			
			if (count($this->failures())) {
				$failures = implode(', ', array_keys((array) $this->failures()));
				$msg .= PHP_EOL . "Failures: {$failures}";
				$msg .= PHP_EOL;
			}
			
			$msg .= PHP_EOL . $message->toString();
			$msg .= PHP_EOL . "------";

			$this->logger->info($msg);
		}
	}

	/**
	 * Get the array of failed recipients.
	 *
	 * @return array
	 */
	public function failures()
	{
		return $this->failedRecipients;
	}
}

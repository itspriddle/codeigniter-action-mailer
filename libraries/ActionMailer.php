<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Mailer handles all email functions
 *
 * @package     ActionMailer
 * @subpackage  Libraries
 * @author      Joshua Priddle <jpriddle@nevercraft.net>
 * @version     1.0
 */

class ActionMailer {

	/**
	 * Deliver prefix
	 *
	 * Changing this means you'll have to change any code that calls
	 * $this->deliver_something();
	 *
	 * @var string
	 */
	private $deliver_prefix = 'deliver_';

	/**
	 * The message to send to the user
	 * Set by class/method.php
	 *
	 * @var string
	 */
	protected $message = '';

	/**
	 * Data available inside of the message view
	 *
	 * @var array
	 */
	protected $body = array();

	/**
	 * TO: address
	 *
	 * @var string
	 */
	protected $to = '';

	/**
	 * Reply-TO: address
	 *
	 * @var string
	 */
	protected $reply_to = '';

	/**
	 * FROM: address
	 *
	 * @var string
	 */
	protected $from = '';

	/**
	 * Subject: lline
	 *
	 * @var string
	 */
	protected $subject = '';

	// --------------------------------------------------------------------

	/**
	 * Initialize CI's mail library
	 *
	 * @access public
	 * @return void
	 */

	public function __construct()
	{
		$this->ci =& get_instance();

		$config = array(
			'protocol' => 'sendmail',
			'mailpath' => '/usr/sbin/sendmail',
			'charset'  => 'iso-8859-1',
			'wordwrap' => TRUE,
		);

		$this->ci->load->library('email', $config);
	}

	// --------------------------------------------------------------------

	/**
	 * Deliver email
	 *
	 * This "magic method" is used to handle sending emails.
	 *
	 * IE: calling $this->deliver_account_created() will
	 * call $this->account_created().
	 *
	 * @access public
	 * @param  string  $method
	 * @param  string  $arguments
	 * @return void
	 */

	public function __call($method, $arguments) {
		$cut = strlen($this->deliver_prefix);
		if (substr($method, 0, $cut) == $this->deliver_prefix)
		{
			$method = substr($method, $cut);
		}
		if (method_exists($this, $method))
		{
			call_user_func_array(array($this, $method), $arguments);
			if ($this->load_view($method) && $this->valid())
			{
				return $this->send_message();
			}
		}
		else
		{
			$class = get_class($this);
			trigger_error("{$class}::{$method}() doesn't exist");
			exit;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Load the email view file
	 *
	 * @access private
	 * @param  string  $method
	 * @return void
	 */

	private function load_view($method)
	{
		$class   = strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', get_class($this)));
		$dir     = APPPATH.'views/';
		$html    = "{$class}/{$method}.html.php";
		$text    = "{$class}/{$method}.txt.php";
		$default = "{$class}/{$method}.php";
		if (file_exists("{$dir}/{$html}"))
		{
			$view = $html;
		}
		else if (file_exists("{$dir}/{$text}"))
		{
			$view = $text;
		}
		else if (file_exists("{$dir}/{$default}"))
		{
			$view = $default;
		}
		else
		{
			trigger_error("Couldn't find a view");
			exit;
		}
		$this->message = $this->ci->load->view($view, $this->body, TRUE);
		return $this->message;
	}

	// --------------------------------------------------------------------

	/**
	 * Send the email
	 *
	 * @access	private
	 * @return	void
	 */

	private function send_message()
	{
		$this->ci->email->clear(empty($this->attachments) ? FALSE : TRUE);
		$this->ci->email->to($this->to);
		$this->ci->email->from($this->from);
		$this->ci->email->subject($this->subject);
		$this->ci->email->message($this->message);

		if (trim($this->reply_to) != '' && $this->reply_to != $this->to)
		{
			$this->ci->email->reply_to($this->reply_to);
		}

		if ( ! empty($this->attachments))
		{
			foreach ($this->attachments as $attachment)
			{
				$this->ci->email->attach($attachment);
			}
		}

		return $this->ci->email->send();
	}

	// --------------------------------------------------------------------

	/**
	 * Check if we have set required fields (to, from, subject, message)
	 *
	 * @access	private
	 * @return	boolean
	 */

	private function valid()
	{
		$check = array($this->to, $this->from, $this->subject, $this->message);

		foreach ($check as $c)
		{
			if (trim($c) == '')
			{
				return FALSE;
			}
		}
		return TRUE;
	}

	// --------------------------------------------------------------------

}

/* End of file ActionMailer.php */
/* Location: ./application/libraries/ActionMailer.php */

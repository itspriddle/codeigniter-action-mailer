<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ActionMailer
 *
 * DRY email for CodeIgniter, similar to ActionMailer in Ruby on Rails
 *
 * @package     ActionMailer
 * @subpackage  Libraries
 * @author      Joshua Priddle <jpriddle@nevercraft.net>
 * @version     0.1.1
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
	 * Set by load_view()
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
	 * CC: address
	 *
	 * @var string or array
	 */
	protected $cc = '';

	/**
	 * BCC: address
	 *
	 * @var string or array
	 */
	protected $bcc = '';

	/**
	 * Reply-TO: address
	 *
	 * @var string or array(email, name)
	 */
	protected $reply_to = '';

	/**
	 * FROM: address
	 *
	 * @var string or array(email, name)
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
		$this->ci->email->subject($this->subject);
		$this->ci->email->message($this->message);

		foreach (array('from', 'reply_to') as $key)
		{
			$k = $this->$key;
			if (is_array($k) && count($k) == 2)
			{
				$this->ci->email->$key($k[0], $k[1]);
			}
			elseif (is_string($k) && trim($k) != '')
			{
				$this->ci->email->$key($k);
			}
		}

		foreach (array('cc', 'bcc', 'to') as $key)
		{
			$this->ci->email->$key($this->$key);
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

<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once(dirname(__FILE__).'/ActionMailer.php');

/**
 * Application Mailer
 *
 * @package     ActionMailer
 * @subpackage  Libraries
 * @author      Joshua Priddle <jpriddle@nevercraft.net>
 * @copyright   Copyright (c) 2010, ViaTalk, LLC
 * @version     1.0
 */

class ApplicationMailer extends Mailer {

	public function __construct()
	{
		parent::__construct();
		$this->ci =& get_instance();
		$this->from = 'noreply@example.com.com';
	}

	// ------------------------------------------------------------------------

	/**
	 * Account Created Email
	 *
	 * @access private
	 * @param  User obj  $user
	 * @return void
	 */

	public function account_created($user)
	{
		$this->to      = $user->email;
		$this->subject = 'THIS IS A TEST MAN';
		$this->body['name'] = 'Josh';
		$this->body['url'] = 'http://example.com/login';
	}

	// --------------------------------------------------------------------
}

/* End of file ApplicationMailer.php */
/* Location: ./application/libraries/ApplicationMailer.php */

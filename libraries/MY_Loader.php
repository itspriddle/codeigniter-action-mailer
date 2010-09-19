<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package   CodeIgniter
 * @author    ExpressionEngine Dev Team
 * @copyright Copyright (c) 2008, EllisLab, Inc.
 * @license   http://codeigniter.com/user_guide/license.html
 * @link      http://codeigniter.com
 * @since     Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Loader Class
 *
 * Loads views and files
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @author      ExpressionEngine Dev Team
 * @category    Loader
 * @link        http://codeigniter.com/user_guide/libraries/loader.html
 */
class MY_Loader extends CI_Loader {

	function MY_Loader()
	{
		parent::CI_Loader();
	}

	// --------------------------------------------------------------------

	/**
	 * Load mailer from APPPATH/mailers
	 *
	 * @param   string $mailer
	 * @return  void
	 * @author  Joshua Priddle
	 */

	function mailer($mailer)
	{
		$CI =& get_instance();
		$func = create_function('$c', 'return strtoupper($c[1]);');
		$class = ucfirst($mailer);
		$classname = preg_replace_callback('/_([a-z])/', $func, $class);
		$path  = APPPATH."mailers/{$mailer}.php";
		if ( ! file_exists($path))
		{
			log_message('error', "Unable to load the requested mailer: {$classname}");
			show_error("Unable to load the requested mailer: {$classname}");
		}
		if ( ! isset($CI->$mailer))
		{
			require_once(APPPATH.'libraries/ActionMailer.php');
			include_once($path);
			$CI->$mailer = new $classname();
		}
	}
}

/* End of file MY_Loader.php */
/* Location: ./application/libraries/MY_Loader.php */

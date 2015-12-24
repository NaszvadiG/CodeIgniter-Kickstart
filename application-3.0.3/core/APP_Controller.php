<?php defined('BASEPATH') OR exit('No direct script access allowed');

// --------------------------------------------------------------------

/**
 * This APP_Controller will be extended by all controllers in this app
 *
 * @author https://github.com/gotphp
 * @version	1.0.0
 *
 */
class APP_Controller extends CI_Controller 
{

	/** 
	 * @var array	An array of variables to be passed to view templates 
	 */
	var $tpl_data = array();

	/** 
	 * @var array	An array of variables passed in from the URL to the app 
	 */
	var $uri_data = array();

	/** 
	 * @var string 	The current date time in 'Y-m-d H:i:s' format 
	 */
	var $current_dts = 0;

	/** 
	 * @var int	The current logged in administrator's admin_id
	 */
	var $admin_id = 0;

	/** 
	 * @var string	The current logged in administrator's admin_uuid
	 */
	var $admin_uuid = 0;

	/** 
	 * @var int	The current logged in administrator's data
	 */
	var $admin_data = NULL;

	/** 
	 * @var int	The current logged in account's account_id
	 */
	var $account_id	= 0;

	/** 
	 * @var string	The current logged in account's account_uuid
	 */
	var $account_uuid = 0;

	/** 
	 * @var int	The current logged in account's account_data
	 */
	var $account_data = NULL;

	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Set money format
		// Ex. http://php.net/manual/en/function.money-format.php	
		setlocale(LC_MONETARY, 'en_US');
		
		// Enable/Disable CI's built-in profiler
		$this->output->enable_profiler(FALSE);

		// Load config/version.php - access in views using $version
		$this->config->load('version');

		// Init: current_dts
		$this->current_dts = date('Y-m-d H:i:s');
		
		// Init: The URI data as an assoc array.
		$this->uri_data = $this->uri->ruri_to_assoc();	

		// Init: tpl_data to pass to all templates in this app
		$this->tpl_data = array(
			'assets_path' 	=> '/assets',
			'app_version'	=> $this->config->item('app_version'),
			'ci_version'	=> CI_VERSION,
		);

	}
		
	// --------------------------------------------------------------------
							
}

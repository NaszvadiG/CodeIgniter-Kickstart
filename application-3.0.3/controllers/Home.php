<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * The Home controller: /home
 *
 * @author https://github.com/gotphp
 * @version	1.0.0
 */
class Home extends APP_Controller {

	/**
	 * 
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 */
	public function index()
	{
		$this->_tpl('home/home_index');	
	}
			
	// --------------------------------------------------------------------
	       	       	
	/**
	 *	Method to load the "view" and set template variables in $this->tpl_data
	 * 	A common header and footer will be wrapped around the template.
	 *
	 *	@param	string	Path to the template file.
	 */
	function _tpl($tpl_name)
	{
		$this->load->vars($this->tpl_data);
		$this->load->view("common/header");	
		$this->load->view($tpl_name);	
		$this->load->view("common/footer");	
	}
	
	// --------------------------------------------------------------------
			
}

<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *	Extend the CI_Model class to add support for simple CRUD database methods
 *
 *	// --------------------------------------------------------------------
 *
 *	C => CREATE METHODS:
 *
 *		create_batch($batches = array()) - An indexed array of assoc arrays
 *		create_data($data = array()) 
 *
 *	R => READ METHODS:
 *
 *		read_data_by_id($id)
 *
 *	U => UPDATE METHODS:
 *
 *		update_attributes($attribute_table, $attributes = array(), $id)
 *		update_data_by_id($data = array(), $id)
 *		update_data_where($data = array(), $where)
 *
 *	D => DELETE METHODS:
 *
 *		delete_data_by_id($id) 
 *
 *	// --------------------------------------------------------------------
 *
 * @author https://github.com/gotphp
 * @version	1.0.0
 */
class APP_Model extends CI_Model {

	/**
	 *	Init & set defaults
	 */	
	var $db_table 		= "";
	var $primary_key 	= "";
	var $valid_columns	= array();
	
	// --------------------------------------------------------------------
	
	/**
	 *	The class constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	// --------------------------------------------------------------------
	// CREATE METHODS
	// --------------------------------------------------------------------

	/**
	 * 	CRUD method to handle creating a new record using a batch
	 *
	 *	@param	array 	An indexed array of assoc arrays
	 * 	@return VOID
	 */
	function create_batch($batches = array()) 
	{		
		$this->db->insert_batch($this->db_table, $batches);
	}
	
	// --------------------------------------------------------------------

	/**
	 * 	CRUD method to handle creating a new record and return the id
	 *
	 *	@param	array 	The assoc array of data to insert
	 * 	@return mixed	The last insert id or False on failure
	 */
	function create_data($data = array()) 
	{
		// insert only valid columns
		foreach($data as $key => $value) {
			if (in_array($key, $this->valid_columns)) {
				$this->db->set($key, $value);
			}
		}
		
		if (! $this->db->insert($this->db_table) ) {
			return FALSE;
		}
		
		return $this->db->insert_id();
	}
	
	// --------------------------------------------------------------------
	// READ METHODS
	// --------------------------------------------------------------------
			
	/**
	 * 	CRUD method to handle reading a single record's data by its id
	 *
	 * 	@param 	int		The primary key's $id
	 * 	@return mixed	The array of data or False if no results found.
	 */
	function read_data_by_id($id)
	{
		$this->db->from($this->db_table);
		$this->db->where($this->primary_key, (int) $id);
		
		$query = $this->db->get();

		/** / // For debugging.
		$this->dbg->last_query($this->db);
		$this->dbg->display($query->result_array()); exit;
		/**/

		if ($query->num_rows == 1)
		{
			$data = $query->result_array();
			return $data[0];
		} else {
			return FALSE;
		}			
	}

	// --------------------------------------------------------------------
	// UPDATE METHODS
	// --------------------------------------------------------------------
	
	/**
	 * 	Method to update an attribute or insert a new one if it does not exist
	 *
	 *	@author	MF
	 *
	 *	@param	string 	The table containing the attribute
	 *	@param	array 	The assoc array of data to update
	 * 	@param 	int		The primary key's $id
	 * 	@return boolean	True on success, False on failure
	 */
	function update_attributes($attribute_table = "", $attributes = array(), $id = 0)
	{
		// invalid table, exit
		if ($attribute_table == "") { return FALSE; }

		// invalid attributes, exit
		if (count($attributes) == 0) { return FALSE; }

		// invalid id, exit
		if ($id == 0) { return FALSE; }

		// process each attribute
		foreach($attributes as $attribute_key => $attribute_value) {
			
			// invalid attribute key, go to next attribute key
			if ( !array_key_exists($attribute_key, $this->valid_attributes) ) {
				continue;
			}

			// init $new_data, which is valid for updates or inserts
			$new_data = array();

			// define the right key type			
			switch($this->valid_attributes[$attribute_key]["type"]) {
				
				case "int":			$new_data["attr_int"] 			= (int)$attribute_value;	break;
				case "varchar":		$new_data["attr_varchar"] 		= $attribute_value;			break;
				case "datetime":	$new_data["attr_datetime"] 		= $attribute_value;			break;
				case "mediumtext":	$new_data["attr_mediumtext"] 	= $attribute_value;			break;
				default:
					// unknown type, skip this attribute
					continue;
				break;

			}

			// get existing attribute if it exists
			$query = $this->db->get_where($attribute_table, array("attr_key" => $attribute_key, $this->primary_key => (int)$id), 1, 0);
			
			if ($query->num_rows == 1) {

				// attributes exists, just update it by ID					
				$existing_data = $query->result_array();

				$this->db->update($attribute_table, $new_data, array("attr_id" => $existing_data[0]["attr_id"]));
				
			} else {

				// attribute does not exist, create it
				/*
				1	attr_id			int			11
				0	account_id		int			11
				0	contest_id		int			11
				0	attr_key		varchar		50
				0	attr_type		enum		'int','varchar','datetime','mediumtext'		
				0	attr_int		int			11
				0	attr_varchar	varchar		255
				0	attr_datetime	datetime	-	
				0	attr_mediumtext	mediumtext	-
				*/
			
				// Exception to skip the account_id if these are account_attributes, 
				// because it will be set by the primary_key already.
				// Other tables will require the account_id from the session
				if ($this->primary_key != "account_id") {
					$this->db->set("account_id", $this->account_id);
				}

				$this->db->set($this->primary_key, 	(int)$id);
				$this->db->set("attr_key", 			$attribute_key);
				$this->db->set("attr_type", 		$this->valid_attributes[$attribute_key]["type"]);
				$this->db->set($new_data);
				$this->db->insert($attribute_table);
				
			}
			
		}

	}
	
	// --------------------------------------------------------------------
		
	/**
	 * 	CRUD method to handle updating a single record's data
	 *
	 *	@param	array 	The assoc array of data to update
	 * 	@param 	array	An array of name => value pairs for the where clause
	 * 	@return boolean	True on success, False on failure
	 */
	function update_data_where($data = array(), $where) 
	{
		// update only valid columns
		$valid_data = array();
		foreach($data as $key => $value) {
			if (in_array($key, $this->valid_columns)) {
				$valid_data[$key] = $value;
			}
		}

		if (! $this->db->update($this->db_table, $valid_data, $where) ) {
			return FALSE;
		}
		
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * 	CRUD method to handle updating a single record's data
	 *
	 *	@param	array 	The assoc array of data to update
	 * 	@param 	int		The primary key's $id
	 * 	@return boolean	True on success, False on failure
	 */
	function update_data_by_id($data = array(), $id) 
	{
		return $this->update_data_where($data, array($this->primary_key => (int)$id));
	}


	// --------------------------------------------------------------------
	// DELETE METHODS
	// --------------------------------------------------------------------

	/**
	 * 	CRUD method to handle deleting a record
	 *
	 *	@param	array 	The assoc array of data to insert
	 * 	@return mixed	The last insert id or False on failure
	 */
	function delete_data_by_id($id) 
	{		
		return $this->db->delete($this->db_table, array($this->primary_key => $id));
	}
	
	// --------------------------------------------------------------------
	
}

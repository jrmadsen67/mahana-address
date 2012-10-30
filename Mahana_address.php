<?php

/**
* Name:  Mahana Address Library 
*
* Author: Jeff Madsen
*		  jrmadsen67@gmail.com
          http://www.codebyjeff.com  
*
* Location: - https://github.com/jrmadsen67/mahana-address
* 			- https://packagist.org/packages/jrmadsen67/mahana-address
*
* Description:  Php library for outputting addresses
*           No views or controllers included - DO CHECK the README.txt for setup instructions and notes
*
*/

class Mahana_address
{
	var $l_tag;

	var $r_tag;

	var $key_prefix;

	var $keys = array();

	private $internal_keys = array();

	private $address_array = array();

	public function __construct($config= null)
	{

		$this->l_tag 		= '<div>';

		$this->r_tag 		= '</div>';

		$this->key_prefix 	= '';

		$this->_initialize_keys();

		if(is_array($config)) $this->initialize($config);

    }

	/**
	 *  Configure your tags, fields
	 *
	 * @access	private
	 * @param	array
	 * @return	this
	 */
	public function initialize($config){
		if(!is_array($config)) return false;
		
		foreach($config as $key => $val){
			$this->$key = $val;
		}

		$this->internal_keys = array_merge($this->internal_keys, $this->keys);

		return $this;
	} 

	
	/**
	 *  these are your default keys that will be read from the database row you pass in
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @return	void
	 */
	private function _initialize_keys()  
	{
		$this->internal_keys = array(
			'name' => 'name',
			'address1' => 'address1',
			'address2' => 'address2',
			'city' => 'city',
			'state' => 'state',
			'zip' => 'zip',
			'country' => 'country',
		);

	}

	/**
	 *  Set the left/right html tags
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	this
	 */
	function set_tags($l = '<div>', $r = '</div>')
	{
		$this->l_tag = $l;
		$this->r_tag = $r;

		return $this; //for chaining
	}

	/**
	 *  Set the prefix for the address array fields (ex., shipping_, billing_)
	 *
	 * @access	public
	 * @param	string
	 * @return	this
	 */
	function set_key_prefix($prefix = '')
	{
		$this->key_prefix = $prefix;

		return $this; //for chaining
	}

	/**
	 *  Creates array from object
	 *
	 * @access	private
	 * @param	object
	 * @return	array
	 */
	function _object_to_array($object)
	{
		return (is_object($object)) ? get_object_vars($object) : $object;
	}

	/**
	 *  Sets global address array
	 *
	 * @access	private
	 * @param	array
	 * @return	void
	 */
	function _set_address_array($address_array)
	{
		$this->address_array = (is_object($address_array)) ?  $this->_object_to_array($address_array): $address_array;
	}

	/**
	 *  Gets sting from address array
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */
	function _get_string($key)
	{
		if (!empty($this->address_array[$this->key_prefix.$this->internal_keys[$key]])) 
			return $this->address_array[$this->key_prefix.$this->internal_keys[$key]] ;
		else
			return '';
	}

	/**
	 *  Adds tags to address string
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function _build_string($key, $tags = 'both') // can be l=left, r=right, b=both, n=none
	{

		$value = $this->_get_string($key);

		$tags = strtolower(substr($tags, 0, 1));

		if (!empty($value)) 
		{
			switch ($tags) {
				case 'n':
					return $value;
					break;
				case 'l':
					return $this->l_tag. $value ;
					break;
				case 'r':
					return $value . $this->r_tag;
					break;
				case 'b':					
				default:
					return $this->l_tag. $value . $this->r_tag;
					break;
			}
		}	
		else
		{
			return '';
		}
			
	}	


	// This function belongs to you - you may create a copy, rework this, change the additional parameters, whatever
	function build_address($address_array = array(), $include_name = false, $include_country = false)
	{

		if (empty($address_array)) return false;

		$this->_set_address_array($address_array);

		$address = '';

		// below this line - feel free to rebuild to your needs
		if ($include_name) $address .= $this->_build_string('name');
		$address .= $this->_build_string('address1');
		$address .= $this->_build_string('address2');
		$address .= implode(', ' , array($this->_build_string('city', 'l'),  $this->_build_string('state', 'right') ));
		$address .= $this->_build_string('zip');
		if ($include_country) $address .= $this->_build_string('country');

		return $address;
	}	




}    
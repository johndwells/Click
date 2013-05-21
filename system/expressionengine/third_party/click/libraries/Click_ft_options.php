<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * A helper class to handle the setting and getting of our fieldtype options.
 * Yes, this is perhaps over the top. But as this fieldtype grows, having
 * a framework in place to handle settings will be very helpful.
 */
class Click_ft_options
{
	/**
	 * Accessible options
	 * 
	 * Which options should be allowed to be edited
	 */
	protected $_accessible = array(
		'field_placeholder',
	);
	
	
	/**
	 * Default options
	 * 
	 * Preserved & defined here for clarity
	 */
	protected $_default = array(
		'field_fmt' => 'none',
		'field_show_fmt' => 'n',
    	'field_placeholder' => 'n'
    );

	
	/**
	 * Runtime options
	 *
	 * Only options modified directly.
	 */
	protected $_runtime	= array();


	// ------------------------------------------------------


	/**
	 * Constructor
	 *
	 * Takes an array and sets it to options
	 */
	public function __construct($options = array())
	{
		$this->options = $options;
	}


	// ------------------------------------------------------


	/**
	 * Magic Getter
	 *
	 * @param 	string	Name of option to retrieve
	 * @return 	mixed	Array of all options, value of individual option, or NULL if not valid
	 */
	public function __get($prop)
	{
		// Find & retrieve the runtime options
		if (array_key_exists($prop, $this->_runtime))
		{
			return $this->_runtime[$prop];
		}
		
		// Find & retrieve the default option
		if (array_key_exists($prop, $this->_default))
		{
			return $this->_default[$prop];
		}

		// Nothing found.
		return NULL;
	}


	// ------------------------------------------------------


	/**
	 * Magic Setter
	 *
	 * @param 	string	Name of option to set
	 */
	public function __set($prop, $value)
	{
		// are we setting all options?
		if ($prop == 'options' && is_array($value))
		{
			$this->_runtime = $this->_sanitise_options($value);
		}
		// just set an individual option?
		elseif (in_array($prop, $this->_accessible))
		{
			$this->_runtime[$prop] = $this->_sanitise_option($prop, $value);
		}
	}


	// ------------------------------------------------------


	/**
	 * Return array of all options at runtime
	 */
	public function to_array()
	{
		// merge with defaults first
		return array_merge($this->_default, $this->_runtime);
	}


	// ------------------------------------------------------


	/**
	 * Sanitise an array of options
	 *
	 * @param 	array	Array of possible options
	 * @return 	array	Sanitised array
	 */
	protected function _sanitise_options($options)
	{
		// Trying to sanitise a non-array of options
		if ( ! is_array($options)) {
			return array();
		}

		// reduce our $options array to only valid keys
        $valid = array_flip(array_intersect(array_keys($this->_default), array_keys($options)));
        
        // now sanitise each value
		foreach($valid as $option => $value)
		{
			$valid[$option] = $this->_sanitise_option($option, $options[$option]);
		}
		
		return $valid;
	}


	// ------------------------------------------------------


	/**
	 * Sanitise an individual option
	 *
	 * @param 	string	Name of option
	 * @param 	string	potential value of option
	 * @return 	string	Sanitised option
	 */
	protected function _sanitise_option($option, $value)
	{
		switch($option) :

			/* BOOLean - default NO */
			case('placeholder') :
				return ($value === TRUE OR preg_match('/1|true|on|yes|y/i', $value)) ? 'y' : 'n';
			break;

			/* Default */			
			default :
				return $value;
			break;
		
		endswitch;
	}


	// ------------------------------------------------------
	
}

/* End of file Click_ft_options.php */
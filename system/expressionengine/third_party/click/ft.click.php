<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD . 'click/config.php';

/**
 * 
 * Click: A simple ExpressionEngine 2.x Fieldtype for creating links using Markdown formatting.
 * 
 * @package	click
 * @author	John D Wells <http://johndwells.com>
 * @license	http://www.opensource.org/licenses/bsd-license.php BSD license
 * @link	https://github.com/johndwells/click
 */
class Click_ft extends EE_Fieldtype {


	/**
	 * Tells EE if can be parsed as tag pair
	 */
	public $has_array_data = FALSE;


	/**
	 * Info for the CP
	 */
	public $info = array(
		'name'		=> CLICK_NAME,
		'version'	=> CLICK_VER
	);


	/**
	 * Holds our parsed fieldtype tag data
	 */
	public $dataParts = array();


	/**
	 * Key for cache, package loading, etc
	 */
	public $package = 'click';


	/**
	 * Placeholder text, if used
	 */
	public $placeholder_text = "[link text](url &quot;optional title&quot;)";


	// --------------------------------------------------------------------


	/**
	 * Fieldtype Constructor
	 */
	function __construct()
	{
		parent::EE_Fieldtype();

		// Load our language file
		$this->EE->lang->loadfile($this->package);

		// Define the package path
		$this->EE->load->add_package_path(PATH_THIRD . $this->package);

		// Load helper
		$this->EE->load->helper($this->package);

		// Load our options class
		$this->EE->load->library('click_ft_options');

		// Set up our cache
		if ( ! isset($this->EE->session->cache[$this->package]))
		{
			$this->EE->session->cache[$this->package] = array();
		}
		$this->cache = $this->EE->session->cache[$this->package];
	}
	// --------------------------------------------------------------------


	/**
	 * Display Field on Publish
	 *
	 * @param 	string 	The current variable data.
	 * @param 	bool 	TRUE if field is a Matrix cell; else FALSE
	 * @return 	string 	A string containing the HTML to be used in the publish field
	 */
	function display_field($data, $cell = FALSE)
	{
		// determine input name
		$name = $cell ? $this->cell_name : $this->field_name;

		// set our per-fieldtype options
		$options = new Click_ft_options($this->settings);

		// show placeholder text?
		$placeholder = '';
		if ( $options->is_yes('field_placeholder') )
		{
			$placeholder = " placeholder=\"" . $this->placeholder_text . "\" ";
		}

		return "<input name=\"" . $name . "\" type=\"text\" value=\"" . $data . "\"" . $placeholder . " />";
	}
	// --------------------------------------------------------------------


	/**
	 * Display Cell
	 *
	 * @param 	string 	The current variable data.
	 * @return 	string 	A string containing the HTML to be used in the publish field
	 */
	function display_cell($data)
	{
		return $this->display_field($data, TRUE);
	}
	// --------------------------------------------------------------------


	/**
	 * Display Low Variable
	 *
	 * @param 	string 	The current variable data.
	 * @return 	string 	A string containing the HTML to be used in the publish field
	 */
	function display_var_field($data)
	{
		if ( ! $this->var_id)
		{
			return;
		}

		// we need to prep our variable data first
		$data = form_prep($data);

		return $this->display_field($data);
	}
	// --------------------------------------------------------------------


	/**
	 * Save Custom Field
	 *
	 * @param 	string 	The posted variable data.
	 * @return 	string 	A string containing the modified variable data to be saved.
	 */
	function save($data)
	{
		// Remove placeholder text as precaution?
		return ($data == $this->placeholder_text) ? '' : $data;
	}
	// --------------------------------------------------------------------


	/**
	 * Save Matrix Cell
	 *
	 * @param 	string 	The posted variable data.
	 * @return 	string 	A string containing the modified variable data to be saved.
	 */
	function save_cell($data)
	{
		return $this->save($data);
	}
	// --------------------------------------------------------------------


	/**
	 * Save Low Variable
	 *
	 * @param 	string 	The posted variable data.
	 * @return 	string 	A string containing the modified variable data to be saved.
	 */
	function save_var_field($data)
	{
		if ( ! $this->var_id)
		{
			return;
		}

		return $this->save($data);
	}
	// --------------------------------------------------------------------


	/**
	 * Display Custom Field settings
	 *
	 * @param 	array 	The current variable’s settings.
	 * @return 	string 	An array containing two elements: the name/label in the first element, the form element(s) in the second.
	 */
	public function display_settings($settings)
	{
		$settings = $this->_display_settings($settings);

		// load the table lib
		$this->EE->load->library('table');

		foreach($settings as $row)
		{
			$this->EE->table->add_row($row);
		}
	}
	// --------------------------------------------------------------------


	/**
	 * Display Matrix cell settings
	 *
	 * @param 	array 	The current variable’s settings.
	 * @return 	string 	An array containing two elements: the name/label in the first element, the form element(s) in the second.
	 */
	public function display_cell_settings($settings)
	{
		return $this->_display_settings($settings, true);
	}
	// --------------------------------------------------------------------


	/**
	 * Display Low Variable FT settings
	 *
	 * @param 	array 	The current variable’s settings.
	 * @return 	string 	An array containing two elements: the name/label in the first element, the form element(s) in the second.
	 */
	public function display_var_settings($settings)
	{
		return $this->_display_settings($settings);
	}
	// --------------------------------------------------------------------


	/**
	 * Internal function used by all display_(var|cell)_settings() methods
	 *
	 * @param 	array 	The current variable’s settings.
	 * @param 	bool 	TRUE if field is a Matrix cell; else FALSE
	 * @return 	string 	An array containing two elements: the name/label in the first element, the form element(s) in the second.
	 */
	protected function _display_settings($settings, $cell = false)
	{
		// set our options based on what is passed via $settings
		$options = new Click_ft_options($settings);

		// Here's our checkbox field
		$checkbox = form_checkbox(array(
			'name'        => 'field_placeholder',
			'value'       => 'y',
			'checked'     => ($options->is_yes('field_placeholder')) ? TRUE : FALSE
		));

		// A hidden field of the same name as the checkbox, to ensure something is always posted
		// (must be returned first in DOM order)
		$hidden = form_hidden('field_placeholder', 'n');

		return array(
			array(lang('placeholder'), $hidden . "\n" . $checkbox),
		);
	}
	// --------------------------------------------------------------------


	/**
	 * Save Custom Field settings
	 *
	 * Also used when saving Matrix cell
	 *
	 * @param 	array 	The posted variable's settings.
	 * @return 	string 	An associative array containing the settings to be saved.
	 */
	function save_settings($settings)
	{
		// take advantage of EE/CI security
		$post = array();
		foreach($_POST as $key => $value)
		{
			$post[$key] = $this->EE->input->post($key);
		}

		// Create our FT options
		$options = new Click_ft_options($post);

		// Return as array
		return $options->to_array();
	}
	// --------------------------------------------------------------------


	/**
	 * Save Low Variable FT settings
	 *
	 * @param 	array 	The posted variable's settings.
	 * @return 	string 	An associative array containing the settings to be saved.
	 */
	function save_var_settings($settings)
	{
		return $this->save_settings($settings);
	}
	// --------------------------------------------------------------------


	/**
	 * Pre-Process our $data before replacing template tags
	 *
	 * Use regular expression match to pull out URL, Text & Title values.
	 *
	 * @param 	string 	Data of fieldtype
	 * @return 	string 	Data of fieldtype (unedited)
	 */
	function pre_process($data)
	{

		// start by setting each to $data
		$this->dataParts = array(
			'link_text'		=> $data,
			'url'			=> $data,
			'title'			=> $data
		);

		# [link text](url "optional title")
		preg_match('{
			(				# wrap whole match in $1
			  \[
				((?>[^\[\]]+|\[\])*)	# link text = $2
			  \]
			  \(			# literal paren
				[ \n]*
				(?:
					<(.+?)>	# href = $3
				|
					((?>[^()\s]+|\((?>\)))*)	# href = $4
				)
				[ \n]*
				(			# $5
				  ([\'"])	# quote char = $6
				  (.*?)		# Title = $7
				  \6		# matching quote
				  [ \n]*	# ignore any spaces/tabs between closing quote and )
				)?			# title is optional
			  \)
			)
			}xs', $data, $matches);

		// If matches, set each part
		if ( $matches )
		{
			$link_text		=  $matches[2];
			$url			=  $matches[3] == '' ? $matches[4] : $matches[3];
			$title			=& $matches[7];

			$url = click_encodeAttribute($url);
			$title = click_encodeAttribute($title);

			$this->dataParts = array(
				'link_text'		=> $link_text,
				'url'			=> $url,
				'title'			=> $title
			);
		}

		// return original data untouched
		return $data;
	}
	// --------------------------------------------------------------------


	/**
	 * Parse template tag
	 *
	 * @param 	string 	Data of fieldtype
	 * @param 	array 	Array of tag parameters
	 * @param 	string 	Any tagdata
	 * @return 	string 	The formatted link
	 */
	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		if ( $data == '' )
		{
			return;
		}

		if ( ! $this->dataParts )
		{
			$data = $this->pre_process($data);
		}

		$result = "<a href=\"" . $this->dataParts['url'] . "\"";

		if ( isset($this->dataParts['title']) )
		{
			$result .=  " title=\"" . $this->dataParts['title'] . "\"";
		}

		$result .= ">" . $this->dataParts['link_text'] . "</a>";

		return $result;
	}
	// --------------------------------------------------------------------


	/**
	 * Parse any template tag modifiers
	 *
	 * @param 	string 	Data of fieldtype
	 * @param 	array 	Array of tag parameters
	 * @param 	string 	Any tagdata
	 * @param 	string 	The tag modifier
	 * @return 	string 	The contents of the requested modifier method
	 */
	function replace_tag_catchall($data, $params = array(), $tagdata = FALSE, $modifier)
	{
		if ( $data == '' )
		{
			return;
		}

		$method = '_replace_' . $modifier;

		if( ! method_exists($this, $method) )
		{
			return $data;
		}

		if ( ! $this->dataParts )
		{
			$data = $this->pre_process($data);
		}

		return $this->$method($data, $params, $tagdata);
	}
	// --------------------------------------------------------------------


	/**
	 * Parse template tag modifiers for URL data
	 *
	 * @param 	string 	Data of fieldtype
	 * @param 	array 	Array of tag parameters
	 * @param 	string 	Any tagdata
	 * @return 	string 	The URL part of the field content
	 */
	protected function _replace_url($data, $params = array(), $tagdata = FALSE)
	{
		return $this->dataParts['url'];
	}
	// --------------------------------------------------------------------


	/**
	 * Parse template tag modifiers for Text data
	 *
	 * @param 	string 	Data of fieldtype
	 * @param 	array 	Array of tag parameters
	 * @param 	string 	Any tagdata
	 * @return 	string 	The Text part of the field content
	 */
	protected function _replace_text($data, $params = array(), $tagdata = FALSE)
	{
		return $this->dataParts['link_text'];
	}
	// --------------------------------------------------------------------



	/**
	 * Parse template tag modifiers for Alternative Title data
	 *
	 * @param 	string 	Data of fieldtype
	 * @param 	array 	Array of tag parameters
	 * @param 	string 	Any tagdata
	 * @return 	string 	The Title part of the field content
	 */
	protected function _replace_title($data, $params = array(), $tagdata = FALSE)
	{
		return $this->dataParts['title'];
	}
	// --------------------------------------------------------------------


	/**
	 * Parse template tag modifiers for original fieldtype data
	 *
	 * @param 	string 	Data of fieldtype
	 * @param 	array 	Array of tag parameters
	 * @param 	string 	Any tagdata
	 * @return 	string 	The original field content
	 */
	protected function _replace_original($data, $params = array(), $tagdata = FALSE)
	{
		return $data;
	}
	// --------------------------------------------------------------------


	/**
	 * Replace the tag for Low Variables
	 *
	 * @param 	string 	Data of fieldtype
	 * @param 	array 	Array of tag parameters
	 * @param 	string 	Any tagdata
	 * @return 	string 	The formatted link
	 */
	public function display_var_tag($data, $params = array(), $tagdata = FALSE)
	{
		if ( ! $this->var_id )
		{
			return;
		}

		return $this->replace_tag($data, $params, $tagdata);
	}
	// --------------------------------------------------------------------
}

/* End of file ft.click.php */
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
	public $has_array_data = TRUE;


	/**
	 * Info for the CP
	 */
	public $info = array(
		'name'		=> CLICK_NAME,
		'version'	=> CLICK_VER
	);


	/**
	 * Array of associated array of link parts
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

		// allow multiple lines?
		if($options->is_yes('field_allow_multiple'))
		{
			return "<textarea rows=\"5\" name=\"" . $name . "\"" . $placeholder . ">" . $data . "</textarea>";
		}
		else
		{
			return "<input name=\"" . $name . "\" type=\"text\" value=\"" . $data . "\"" . $placeholder . " />";
		}
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
		$placeholder_checkbox = form_checkbox(array(
			'name'        => 'field_placeholder',
			'value'       => 'y',
			'checked'     => ($options->is_yes('field_placeholder')) ? TRUE : FALSE
		));

		// A hidden field of the same name as the checkbox, to ensure something is always posted
		// (must be returned first in DOM order)
		$placeholder_hidden = form_hidden('field_placeholder', 'n');


		// Here's our checkbox field
		$allow_multiple_checkbox = form_checkbox(array(
			'name'        => 'field_allow_multiple',
			'value'       => 'y',
			'checked'     => ($options->is_yes('field_allow_multiple')) ? TRUE : FALSE
		));

		// A hidden field of the same name as the checkbox, to ensure something is always posted
		// (must be returned first in DOM order)
		$allow_multiple_hidden = form_hidden('field_allow_multiple', 'n');

		return array(
			array(lang('placeholder'), $placeholder_hidden . "\n" . $placeholder_checkbox),
			array(lang('allow_multiple'), $allow_multiple_hidden . "\n" . $allow_multiple_checkbox),
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
	function pre_process($data = '')
	{
		// set our per-fieldtype options
		$options = new Click_ft_options($this->settings);

		// turn our data into an array
		$as_array =  (! is_array($data)) ? array_filter(preg_split("/[\r\n]+/", $data)) : $data;

		// clear our dataParts as precaution
		$this->dataParts = array();

		foreach($as_array as $line)
		{
			$matches = click_doAnchors($line);

			// If matches, set each part
			if ( $matches )
			{
				$original		=  $matches[1];
				$link_text		=  $matches[2];
				$url			=  $matches[3] == '' ? $matches[4] : $matches[3];
				$title			=& $matches[7];

				$url = click_encodeAttribute($url);
				$title = click_encodeAttribute($title);

				$this->dataParts[] = array(
					'original'		=> $original,
					'link_text'		=> $link_text,
					'url'			=> $url,
					'title'			=> $title
				);
			}
			// just set every part to same
			else
			{
				$this->dataParts[] = array(
					'original'		=> $line,
					'link_text'		=> $line,
					'url'			=> $line,
					'title'			=> $line
				);
			}
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
	function replace_tag($data = '', $params = array(), $tagdata = FALSE)
	{
		if ( $data == '' )
		{
			return;
		}

		// precautionary prep?
		if( ! $this->dataParts)
		{
			$this->pre_process($data);
		}

		// set our per-fieldtype options
		$options = new Click_ft_options($this->settings);

		// if has tagdata and allowed to show multiple...
		if($tagdata && $options->is_yes('field_allow_multiple'))
		{
			// prepare for {switch} and {count} tags
			$this->_prep_iterators($tagdata);

			// prefix?
			$prefix = (isset($params['var_prefix']) && $params['var_prefix']) ? $params['var_prefix'] . ':' : '';

			// limit?
			$limit = count($this->dataParts);
			$limit = (isset($params['limit']) && $params['limit'] < $limit) ? $params['limit'] : $limit;
			$count = 0;

			$return = '';
			foreach($this->dataParts as $key => $value)
			{
				// copy $tagdata
				$click_tagdata = $tagdata;

				// simple var swaps
				$click_tagdata = $this->EE->TMPL->swap_var_single($prefix . 'click', $this->_replace_tag($key), $click_tagdata);
				$click_tagdata = $this->EE->TMPL->swap_var_single($prefix . 'url', $this->_replace_url($key), $click_tagdata);
				$click_tagdata = $this->EE->TMPL->swap_var_single($prefix . 'text', $this->_replace_text($key), $click_tagdata);
				$click_tagdata = $this->EE->TMPL->swap_var_single($prefix . 'title', $this->_replace_title($key), $click_tagdata);
				$click_tagdata = $this->EE->TMPL->swap_var_single($prefix . 'original', $this->_replace_original($key), $click_tagdata);

				// parse total
				$click_tagdata = $this->EE->TMPL->swap_var_single($prefix . 'total', $this->replace_total(), $click_tagdata);

				// parse {switch} and {count} tags
				$this->_parse_iterators($click_tagdata);

				// append to our return string
				$return .= $click_tagdata;

				// have we reached our limit?
				$count++;
				if($count == $limit)
				{
					break;
				}
			}

			if (isset($params['backspace']) && $params['backspace'])
			{
				$return = substr($return, 0, -$params['backspace']);
			}

			return $return;
		}
		// if a single tag, just pluck off the first dataParts array item
		else
		{
			return $this->_replace_tag(0);
		}
	}
	// --------------------------------------------------------------------


	/**
	 * Create and return a fully-rendered link
	 *
	 * @param 	integer Key of $this->dataParts[] to return
	 * @return 	string 	The formatted link
	 */
	function _replace_tag($index = 0)
	{
		$result = "<a href=\"" . $this->dataParts[$index]['url'] . "\"";

		if ( isset($this->dataParts[$index]['title']) )
		{
			$result .=  " title=\"" . $this->dataParts[$index]['title'] . "\"";
		}

		$result .= ">" . $this->dataParts[$index]['link_text'] . "</a>";

		return $result;
	}
	// --------------------------------------------------------------------


	/**
	 * Returns the first link in the field
	 *
	 * @param 	string 	Data of fieldtype
	 * @param 	array 	Array of tag parameters
	 * @param 	string 	Any tagdata
	 * @return 	string 	The first link in the field
	 */
	function replace_first($data = '', $params = array(), $tagdata = FALSE, $modifier = 'tag')
	{
		if ( $data == '' )
		{
			return '';
		}

		// precautionary prep?
		if( ! $this->dataParts)
		{
			$this->pre_process($data);
		}

		// build our method
		$method = '_replace_' . $modifier;

		return $this->$method(0);
	}
	// --------------------------------------------------------------------


	/**
	 * Returns the last link in the field
	 *
	 * @param 	string 	Data of fieldtype
	 * @param 	array 	Array of tag parameters
	 * @param 	string 	Any tagdata
	 * @return 	string 	The last link in the field
	 */
	function replace_last($data = '', $params = array(), $tagdata = FALSE, $modifier = 'tag')
	{
		if ( $data == '' )
		{
			return '';
		}

		// precautionary prep?
		if( ! $this->dataParts)
		{
			$this->pre_process($data);
		}

		// build our method
		$method = '_replace_' . $modifier;

		return $this->$method(count($this->dataParts) - 1);
	}
	// --------------------------------------------------------------------


	/**
	 * Return an unordered list of links
	 *
	 * @param 	string 	Data of fieldtype
	 * @param 	array 	Array of tag parameters
	 * @param 	string 	Any tagdata
	 * @return 	string 	The unordered list of links
	 */
	function replace_ul($data = '', $params = array(), $tagdata = FALSE, $modifier = 'tag')
	{
		if ( $data == '' )
		{
			return '';
		}

		// precautionary prep?
		if( ! $this->dataParts)
		{
			$this->pre_process($data);
		}

		// build our method
		$method = '_replace_' . $modifier;

		$return = "<ul>";

		foreach($this->dataParts as $key => $value)
		{
			$return .= "<li>" . $this->$method($key) . "</li>";
		}

		return $return .= "</ul>";
	}
	// --------------------------------------------------------------------


	/**
	 * Return an ordered list of links
	 *
	 * @param 	string 	Data of fieldtype
	 * @param 	array 	Array of tag parameters
	 * @param 	string 	Any tagdata
	 * @return 	string 	The ordered list of links
	 */
	function replace_ol($data = '', $params = array(), $tagdata = FALSE, $modifier = 'tag')
	{
		if ( $data == '' )
		{
			return '';
		}

		// precautionary prep?
		if( ! $this->dataParts)
		{
			$this->pre_process($data);
		}

		// build our method
		$method = '_replace_' . $modifier;

		$return = "<ol>";

		foreach($this->dataParts as $key => $value)
		{
			$return .= "<li>" . $this->$method($key) . "</li>";
		}

		return $return .= "</ol>";
	}
	// --------------------------------------------------------------------


	/**
	 * Calculate the total lines in field
	 *
	 * @param 	string 	Data of fieldtype
	 * @param 	array 	Array of tag parameters
	 * @param 	string 	Any tagdata
	 * @return 	integer	The number of lines in field
	 */
	function replace_total($data = '', $params = array(), $tagdata = FALSE)
	{
		if ( $data == '' )
		{
			return 0;
		}

		// precautionary prep?
		if( ! $this->dataParts)
		{
			$this->pre_process($data);
		}

		return count($this->dataParts);
	}

	/**
	 * Parse any template tag modifiers that we haven't explicitly defined
	 *
	 * @param 	string 	Data of fieldtype
	 * @param 	array 	Array of tag parameters
	 * @param 	string 	Any tagdata
	 * @param 	string 	The tag modifier
	 * @return 	string 	The contents of the requested modifier method
	 */
	function replace_tag_catchall($data = '', $params = array(), $tagdata = FALSE, $modifier = 'tag')
	{
		if ( $data == '' )
		{
			return;
		}

		// precautionary prep?
		if( ! $this->dataParts)
		{
			$this->pre_process($data);
		}

		// do we have two modifiers to parse?
		if (strpos($modifier, ':'))
		{
			list($meth, $mod) = explode(':', $modifier);

			$method = 'replace_' . $meth;

			if ( ! method_exists($this, $method))
			{
				return $data;
			}

			return $this->$method($data, $params, $tagdata, $mod);
		}
		else
		{
			$method = '_replace_' . $modifier;

			if( ! method_exists($this, $method) )
			{
				return $data;
			}

			// with field modifiers, only return first line
			return $this->$method(0);
		}
	}
	// --------------------------------------------------------------------


	/**
	 * Parse template tag modifiers for URL data
	 *
	 * @param 	integer Key of $this->dataParts[] to return
	 * @return 	string 	The URL part of the field content
	 */
	protected function _replace_url($index = 0)
	{
		return $this->dataParts[$index]['url'];
	}
	// --------------------------------------------------------------------


	/**
	 * Parse template tag modifiers for Text data
	 *
	 * @param 	integer Key of $this->dataParts[] to return
	 * @return 	string 	The Text part of the field content
	 */
	protected function _replace_text($index = 0)
	{
		return $this->dataParts[$index]['link_text'];
	}
	// --------------------------------------------------------------------


	/**
	 * Parse template tag modifiers for Alternative Title data
	 *
	 * @param 	integer Key of $this->dataParts[] to return
	 * @return 	string 	The Title part of the field content
	 */
	protected function _replace_title($index = 0)
	{
		return $this->dataParts[$index]['title'];
	}
	// --------------------------------------------------------------------


	/**
	 * Parse template tag modifiers for original fieldtype data
	 *
	 * @param 	integer Key of $this->dataParts[] to return
	 * @return 	string 	The original field content
	 */
	protected function _replace_original($index = 0)
	{
		return $this->dataParts[$index]['original'];
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
	public function display_var_tag($data = '', $params = array(), $tagdata = FALSE)
	{
		if ( ! $this->var_id )
		{
			return;
		}

		// precautionary prep?
		if( ! $this->dataParts)
		{
			$this->pre_process($data);
		}

		return $this->replace_tag($data, $params, $tagdata);
	}
	// --------------------------------------------------------------------


	/**
	 * Prep Iterators (borrowed from P&T Field Pack)
	 */
	protected function _prep_iterators(&$tagdata)
	{
		// find {switch} tags
		$this->_switches = array();
		$tagdata = preg_replace_callback('/'.LD.'switch\s*=\s*([\'\"])([^\1]+)\1'.RD.'/sU', array(&$this, '_get_switch_options'), $tagdata);

		$this->_count_tag = 'count';
		$this->_iterator_count = 0;
	}
	// --------------------------------------------------------------------


	/**
	 * Get Switch Options (borrowed from P&T Field Pack)
	 */
	protected function _get_switch_options($match)
	{
		$marker = LD.'SWITCH['.$this->EE->functions->random('alpha', 8).']SWITCH'.RD;
		$this->_switches[] = array('marker' => $marker, 'options' => explode('|', $match[2]));
		return $marker;
	}
	// --------------------------------------------------------------------


	/**
	 * Parse Iterators (borrowed from P&T Field Pack)
	 */
	protected function _parse_iterators(&$tagdata)
	{
		// {switch} tags
		foreach($this->_switches as $i => $switch)
		{
			$option = $this->_iterator_count % count($switch['options']);
			$tagdata = str_replace($switch['marker'], $switch['options'][$option], $tagdata);
		}

		// update the count
		$this->_iterator_count++;

		// {count} tags
		$tagdata = $this->EE->TMPL->swap_var_single($this->_count_tag, $this->_iterator_count, $tagdata);
	}
	// --------------------------------------------------------------------

}

/* End of file ft.click.php */
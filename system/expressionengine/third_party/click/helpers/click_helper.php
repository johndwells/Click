<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Helper functions
 */

// --------------------------------------------------------------------

/**
 * Borrowed from Markdown.php
 */
if ( ! function_exists('click_encodeAttribute'))
{
	function click_encodeAttribute($text)
	{
		$text = click_encodeAmpsAndAngles($text);
		$text = str_replace('"', '&quot;', $text);
		return $text;
	}
}

// --------------------------------------------------------------------

/**
 * Borrowed from Markdown.php
 */
if ( ! function_exists('click_encodeAmpsAndAngles'))
{
	function click_encodeAmpsAndAngles($text)
	{
		# Ampersand-encoding based entirely on Nat Irons's Amputator
		# MT plugin: <http://bumppo.net/projects/amputator/>
		$text = preg_replace('/&(?!#?[xX]?(?:[0-9a-fA-F]+|\w+);)/',
							'&amp;', $text);;

		# Encode remaining <'s
		$text = str_replace('<', '&lt;', $text);

		return $text;
	}
}

// --------------------------------------------------------------

/* End of file click_helper.php */
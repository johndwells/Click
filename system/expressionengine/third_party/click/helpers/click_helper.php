<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Click Helper functions
 * 
 * @package	click
 * @author	John D Wells <http://johndwells.com>
 * @license	http://www.opensource.org/licenses/bsd-license.php BSD license
 * @link	https://github.com/johndwells/click
 */


// --------------------------------------------------------------------


/**
 * Borrowed (and butchered) from Markdown.php
 */
 if ( ! function_exists('click_doAnchors') )
 {
 	function click_doAnchors($text = '')
 	{
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
			}xs', $text, $matches);

		return $matches;
 	}
 }
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
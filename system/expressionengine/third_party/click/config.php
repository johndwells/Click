<?php
if (! defined('CLICK_VER'))
{
	define('CLICK_NAME', 'Click');
	define('CLICK_VER',  '1.0.0');
	define('CLICK_AUTHOR',  'John D Wells');
	define('CLICK_DOCS',  'https://github.com/johndwells/Click');
	define('CLICK_DESC',  'A simple ExpressionEngine 2.x Fieldtype for creating links using Markdown formatting.');
}

$config['name'] = CLICK_NAME;
$config['version'] = CLICK_VER;
//$config['nsm_addon_updater']['versions_xml'] = '';
<?php

// magento module creator
// take ff. input
// Namespace
// Module
// codePool
// enabled
// directories?
// any other files to extend?


// mkdir experiment
// var_dump(mkdir(dirname(__FILE__) . '/Namespace'));
// var_dump(mkdir(dirname(__FILE__) . '/Namespace/Module'));
// var_dump(mkdir(dirname(__FILE__) . '/Namespace/Module/etc'));
/*
var_dump(touch(dirname(__FILE__) . '/Namespace/Module/etc/config.xml'));
var_dump(chown(dirname(__FILE__) . '/Namespace', 'www-data'));
var_dump(chown(dirname(__FILE__) . '/Namespace/Module/etc/config.xml', 'www-data'));
 */

$s = $_SERVER;
/**
 * get action page URL
 * @param 	string $action_page
 * @return 	string full URL to action page
 */
function get_action_url($action_page)
{
	global $s;
	// @todo: refine url (strip of any malicious stuff?)
	return 'http://' . $s['HTTP_HOST'] . $s['REQUEST_URI'] . $action_page;
}

function module_creation_autoload($class)
{
	include "$class.php";
}

spl_autoload_register('module_creation_autoload');
<?php
/**
* @package User-Private-Page (com_userprivatepage)
* @version 1.2.1
* @copyright Copyright (C) 2014-2015 Carsten Engel. All rights reserved.
* @license GPL versions free/trial/pro
* @author http://www.pages-and-items.com
*/

defined('_JEXEC') or die;

function userprivatepageBuildRoute( &$query ){

	$segments = array();
	if(isset($query['view'])){
		$segments[] = $query['view'];
		unset( $query['view'] );
	}	
	return $segments;
}

function userprivatepageParseRoute($segments){

	$vars = array();
	switch($segments[0]){
	
		case 'userpage':
			$vars['view'] = 'userpage';					  
			break;		
	}	
	return $vars;
}

?>
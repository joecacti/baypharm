<?php
/**
* @package User-Private-Page (com_userprivatepage)
* @version 1.2.1
* @copyright Copyright (C) 2014-2015 Carsten Engel. All rights reserved.
* @license GPL versions free/trial/pro
* @author http://www.pages-and-items.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class userprivatepageModelUserpage extends JModelList{	
	
	protected $option = 'com_userprivatepage';		
	
	protected function getListQuery(){			
		
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);		

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.* '					
			)
		);
		$query->from('`#__userprivatepage_comments` AS a');

		// Filter for user	
		$user = JFactory::getUser();
		$user_id = $user->get('id');
		$query->where('a.user_id = '.(int)$user_id);			

		// Add the list ordering clause.			
		$query->order($db->escape('date DESC'));
		
		//echo nl2br($query);
		return $query;
	}	
	
	
}
?>
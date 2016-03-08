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

jimport( 'joomla.application.component.view');

class userprivatepageViewScript extends JViewLegacy{
	
	function display($tpl = null){	
				
		$db = JFactory::getDBO();			
		
		//get configuration
		$controller = new userprivatepageController();			
		
		//get item id
		$id = intval(JRequest::getVar('id', ''));	
		
		//get redirect		
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__userprivatepage_scripts');
		$query->where('id='.$db->q($id));
		$rows = $db->setQuery($query, 0, 1);	
		$rows = $db->loadObjectList();	
		
		//set defaults for new
		$script = (object)'';			
		$script->id = 0;
		$script->name = '';
		$script->value = '';			
		$new_line = '
';			
		foreach($rows as $temp){
			$script = $temp;	
			$temp_value = $temp->value;			
			$temp_value = str_replace('[newline]', $new_line, $temp_value);
			$temp_value = str_replace('[equal]', '=', $temp_value);			
			$script->value = $temp_value;
		}	
		$this->assignRef('script', $script);
		
		//language
		$lang = JFactory::getLanguage();		
		$lang->load('com_installer', JPATH_ADMINISTRATOR, null, false);			

		//toolbar		
		JToolBarHelper::apply('script_apply', 'JToolbar_Apply');
		JToolBarHelper::save('script_save', 'JToolbar_Save');		
		JToolBarHelper::cancel('cancel', 'JToolbar_Close');		
		
		//sidebar
		if(userprivatepageHelper::joomla_version() >= '3.0'){			
			JHtmlSidebar::setAction('index.php?option=com_userprivatepage&view=script');					
			userprivatepageHelper::addSubmenu('script');							
			$this->sidebar = JHtmlSidebar::render();	
		}else{
			$this->sidebar = '';
		}
		
		parent::display($tpl);
	}	
	
	
}
?>
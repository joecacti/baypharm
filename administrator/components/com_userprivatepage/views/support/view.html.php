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

class userprivatepageViewSupport extends JViewLegacy{

	function display($tpl = null){
	
		$controller = new userprivatepageController();	
		$this->assignRef('controller', $controller);			
		
		//include language files. Reuse or die ;-)#
		$lang = JFactory::getLanguage();
		$lang->load('com_installer', JPATH_ADMINISTRATOR, null, false);	
		$lang->load('mod_menu', JPATH_ADMINISTRATOR, null, false);	
		$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false);	
		$lang->load('com_modules', JPATH_ADMINISTRATOR, null, false);	
		
		//sidebar
		if(userprivatepageHelper::joomla_version() >= '3.0'){			
			JHtmlSidebar::setAction('index.php?option=com_userprivatepage&view=support');					
			userprivatepageHelper::addSubmenu('support');							
			$this->sidebar = JHtmlSidebar::render();	
		}else{
			$this->sidebar = '';
		}				

		parent::display($tpl);
	}	
	
}
?>
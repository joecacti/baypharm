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

class userprivatepageViewConfiguration extends JViewLegacy{
	
	function display($tpl = null){	
	
		$db = JFactory::getDBO();		
		
		//get controller
		$controller = new userprivatepageController();	
		$this->assignRef('controller', $controller);
		
		//get text_when_empty
		$query = $db->getQuery(true);
		$query->select('text_when_empty, text_when_unpub, notifymessage, notifymessage_admin_add, notifymessage_admin_edit');
		$query->from('#__userprivatepage_config');
		$query->where('id='.$db->q('1'));		
		$rows = $db->setQuery($query);				
		$rows = $db->loadObjectList();
		
		$text_when_empty = '';
		$text_when_unpublished = '';
		$notifymessage = '';
		$notifymessage_admin_add = '';
		$notifymessage_admin_edit = '';
		foreach($rows as $row){		
			$text_when_empty = $row->text_when_empty;	
			$text_when_unpublished = $row->text_when_unpub;
			$notifymessage = $row->notifymessage;
			$notifymessage_admin_add = $row->notifymessage_admin_add;
			$notifymessage_admin_edit = $row->notifymessage_admin_edit;
		}		
		$this->assignRef('text_when_empty', $text_when_empty);
		$this->assignRef('text_when_unpublished', $text_when_unpublished);
		$this->assignRef('notifymessage', $notifymessage);
		$this->assignRef('notifymessage_admin_add', $notifymessage_admin_add);
		$this->assignRef('notifymessage_admin_edit', $notifymessage_admin_edit);
		
		//language
		$lang = JFactory::getLanguage();		
		$lang->load('com_templates', JPATH_ADMINISTRATOR, null, false);	
		$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false);	
		$lang->load('com_messages', JPATH_ADMINISTRATOR, null, false);			
		$lang->load('com_users', JPATH_ADMINISTRATOR, null, false);	
		$lang->load('com_wrapper', JPATH_ADMINISTRATOR, null, false);	
		$lang->load('com_installer', JPATH_ADMINISTRATOR, null, false);
		$lang->load('mod_toolbar', JPATH_ADMINISTRATOR, null, false);
		$lang->load('com_weblinks.sys', JPATH_ADMINISTRATOR, null, false);
		$lang->load('mod_menu', JPATH_ADMINISTRATOR, null, false);
		$lang->load('com_cache', JPATH_ADMINISTRATOR, null, false);
		$lang->load('com_contact', JPATH_ADMINISTRATOR, null, false);	
		$lang->load('com_admin', JPATH_ADMINISTRATOR, null, false);		

		//toolbar				
		JToolBarHelper::apply('config_apply', 'JToolbar_Apply');			
		JToolBarHelper::save('config_save', 'JToolbar_Save');		
		JToolBarHelper::cancel('cancel', 'JToolbar_Close');	
		if(JFactory::getUser()->authorise('core.admin', 'com_userprivatepage')){
			JToolBarHelper::preferences('com_userprivatepage');
		}
		
		//sidebar
		if(userprivatepageHelper::joomla_version() >= '3.0'){			
			JHtmlSidebar::setAction('index.php?option=com_userprivatepage&view=configuration');					
			userprivatepageHelper::addSubmenu('configuration');			
			$this->sidebar = JHtmlSidebar::render();	
		}else{
			$this->sidebar = '';
		}	
		
		parent::display($tpl);
	}

	
}
?>
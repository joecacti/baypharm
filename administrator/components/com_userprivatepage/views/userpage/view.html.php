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

class userprivatepageViewUserpage extends JViewLegacy{
	
	function display($tpl = null){	
				
		$db = JFactory::getDBO();	
		$app = JFactory::getApplication();
		
		//get configuration
		$controller = new userprivatepageController();			
		
		//get item id
		$user_id = intval(JRequest::getVar('user_id', ''));	
		$this->assignRef('user_id', $user_id);
		
		//get page		
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__userprivatepage_pages');
		$query->where('user_id='.$db->q($user_id));
		$rows = $db->setQuery($query, 0, 1);	
		$rows = $db->loadObjectList();	
		
		//set defaults for new
		$page = (object)'';			
		$page->id = 0;
		$page->user_id = 0;	
		$page->title = '';	
		$page->text = '';
		$page->published = 1;	
		$page->show_title = 2;	
		$page->comments = 2;		
			
		$new = 1;
		foreach($rows as $row){
			$page = $row;
			$new = 0;														
		}	
		$this->assignRef('page', $page);	
		
		if($new && $controller->get_version_type()=='free'){			
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from('#__userprivatepage_pages');					
			$rows = $db->setQuery($query);				
			$rows = $db->loadObjectList();				
			if(count($rows)>=5){				
				$message = JText::_('COM_USERPRIVATEPAGE_LIMITED_USERPAGES');
				$app->redirect('index.php?option=com_userprivatepage&view=userspages', $message);
			}	
		}	
		
		//language
		$lang = JFactory::getLanguage();		
		$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false);	
		$lang->load('com_messages', JPATH_ADMINISTRATOR, null, false);	
		$lang->load('com_installer', JPATH_ADMINISTRATOR, null, false);
		$lang->load('com_admin', JPATH_ADMINISTRATOR, null, false);
		$lang->load('mod_toolbar', JPATH_ADMINISTRATOR, null, false);		

		//toolbar				
		JToolBarHelper::apply('userpage_apply', 'JToolbar_Apply');
		JToolBarHelper::save('userpage_save', 'JToolbar_Save');		
		JToolBarHelper::cancel('cancel', 'JToolbar_Close');	
		//JToolBarHelper::custom('preview','upp_preview','preview_f2.png',JText::_('JGLOBAL_PREVIEW'),false,false);	
		
		//sidebar
		if(userprivatepageHelper::joomla_version() >= '3.0'){			
			JHtmlSidebar::setAction('index.php?option=com_userprivatepage&view=userpage');					
			userprivatepageHelper::addSubmenu('userpage');							
			$this->sidebar = JHtmlSidebar::render();	
		}else{
			$this->sidebar = '';
		}
		
		parent::display($tpl);
	}	
	
	
}
?>
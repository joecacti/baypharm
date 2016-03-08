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

class userprivatepageViewScripts extends JViewLegacy{
	
	protected $items;	
	protected $state;
	protected $pagination;		
	
	public function display($tpl = null){		
	
		//get configuration
		$controller = new userprivatepageController();	
		$this->assignRef('controller', $controller);
				
		$this->state = $this->get('State');	
		$this->items = $this->get('Items');			
		$this->pagination = $this->get('Pagination');
		
		//include mod_menu language. Reuse or die ;-)#
		$lang = JFactory::getLanguage();
		$lang->load('mod_menu', JPATH_ADMINISTRATOR, null, false);
		//$lang->load('com_users', JPATH_ADMINISTRATOR, null, false);
		
		//toolbar		
		JToolBarHelper::custom('script','new.png','new_f2.png',JText::_('JTOOLBAR_NEW'),false,false);	
		JToolBarHelper::custom('scripts_delete','delete.png','delete_f2.png',JText::_('JTOOLBAR_DELETE'),false,false);	
		
		//sidebar
		if(userprivatepageHelper::joomla_version() >= '3.0'){			
			JHtmlSidebar::setAction('index.php?option=com_userprivatepage&view=scripts');					
			userprivatepageHelper::addSubmenu('scripts');						
			$this->sidebar = JHtmlSidebar::render();	
		}else{
			$this->sidebar = '';
		}

		parent::display($tpl);
	}		
	
	protected function getSortFields(){
		
		return array(
			'a.name' => JText::_('COM_USERPRIVATEPAGE_NAME'),
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),				
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}	
	
	
	
	
	
	
}
?>
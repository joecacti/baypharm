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

class userprivatepageViewUserscomments extends JViewLegacy{
	
	protected $items;	
	protected $state;
	protected $pagination;	
	protected $user_index;
	protected $group_level_index;
	
	public function display($tpl = null){	
	
		$db = JFactory::getDBO();	
	
		//get configuration
		$controller = new userprivatepageController();	
		$this->assignRef('controller', $controller);
				
		$this->state = $this->get('State');	
		$this->items = $this->get('Items');			
		$this->pagination = $this->get('Pagination');	
		$this->user_index = $this->get_userindex($this->items);
		$this->group_level_index = $this->get_group_level_index();
		
		//get groups ordered
		$groups_title_order = $this->get_groups_title_order();
		$this->assignRef('groups_title_order', $groups_title_order);
		
		//get levels in order
		$levels_title_order = $this->get_levels_title_order();
		$this->assignRef('levels_title_order', $levels_title_order);						
		
		//include mod_menu language. Reuse or die ;-)#
		$lang = JFactory::getLanguage();
		$lang->load('mod_menu', JPATH_ADMINISTRATOR, null, false);
		$lang->load('com_users', JPATH_ADMINISTRATOR, null, false);
		
		//toolbar	
		/*
		JToolBarHelper::publish('userspages_publish', 'JTOOLBAR_PUBLISH', true);
		JToolBarHelper::unpublish('userspages_unpublish', 'JTOOLBAR_UNPUBLISH', true);
		JToolBarHelper::divider();			
		JToolBarHelper::custom('userspages_empty','delete.png','delete_f2.png',JText::_('COM_USERPRIVATEPAGE_EMPTY').' '.JText::_('COM_USERPRIVATEPAGE_PAGE'),false,false);	
		JToolBarHelper::custom('userspages_export', 'upp_export', 'export', JText::_('JTOOLBAR_EXPORT').' .csv', false, false );
		*/
		
		//sidebar
		if(userprivatepageHelper::joomla_version() >= '3.0'){			
			JHtmlSidebar::setAction('index.php?option=com_userprivatepage&view=userscomments');					
			userprivatepageHelper::addSubmenu('userscomments');		
			$this->get_filters();				
			$this->sidebar = JHtmlSidebar::render();	
		}else{
			$this->sidebar = '';
		}

		parent::display($tpl);
	}
	
	function get_filters(){
	
		$filters = array();
		/*
		$filters[] = array(
			'- '.JText::_('JSELECT').' '.JText::_('JSTATUS').' -',
			'filter_status',
			JHtml::_('select.options', $this->get_options_status(), 'value', 'text', $this->state->get('filter.status'))
		);
		*/
		$filters[] = array(
			'- '.JText::_('JSELECT').' '.JText::_('JLIB_RULES_GROUPS').' -',
			'filter_group_id',
			JHtml::_('select.options', userprivatepageHelper::get_groups(0, 1, 0), 'value', 'text', $this->state->get('filter.group_id'))
		);
		$filters[] = array(
			'- '.JText::_('JSELECT').' '.JText::_('MOD_MENU_COM_USERS_LEVELS').' -',
			'filter_level_id',
			JHtml::_('select.options', $this->get_levels(), 'value', 'text', $this->state->get('filter.level_id'))
		);		
	
		return userprivatepageHelper::get_filters($filters);		
	}	
	
	protected function getSortFields(){
		
		return array(
			'a.name' => JText::_('COM_USERPRIVATEPAGE_NAME'),
			'a.username' => JText::_('COM_USERPRIVATEPAGE_USERNAME'),				
			'a.id' => JText::_('JGRID_HEADING_ID'),
			'unread_comments' => userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_UNREAD')).' '.userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_COMMENTS'))
		);
	}	
	
	function get_levels(){
	
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true);
		$query->select('id AS value, title AS text');
		$query->from('#__viewlevels');		
		$query->order('title');
		$rows = $db->setQuery($query);				
		$rows = $db->loadObjectList();	
		
		return $rows;		
	}
	
	function get_groups_title_order(){
	
		$db = JFactory::getDBO();		
		
		//get groups
		$query = $db->getQuery(true);
		$query->select('a.id AS group_id, a.title AS group_title');
		$query->from('#__usergroups AS a');		
		$query->order('a.title');
		$rows = $db->setQuery($query);				
		$rows = $db->loadObjectList();			
		
		$groups_order = array();
		foreach($rows as $group){			
			$groups_order[] = array($group->group_id, $group->group_title);
		}	
		
		//sort array by order
		$column = array();//reset column if you are using this elsewhere
		foreach($groups_order as $sortarray){
			$column[] = $sortarray[1];	
		}
		$sort_order = SORT_ASC;//define as a var or else ioncube goes mad
		array_multisort($column, $sort_order, $groups_order);	
		
		return $groups_order;
	}
	
	function get_levels_title_order(){
	
		$db = JFactory::getDBO();		
		
		$query = $db->getQuery(true);
		$query->select('a.id AS level_id, a.title AS level_title');
		$query->from('#__viewlevels AS a');		
		$query->order('level_title');
		$rows = $db->setQuery($query);				
		$rows = $db->loadObjectList();	
					
		return $rows;
	}
	
	function get_users_groups($user_id){
	
		$groups = array();
		foreach($this->user_index as $user_group_row){
			if($user_id==$user_group_row->user_id){
				$groups[] = $user_group_row->group_id;
			}
		}
		return $groups;
	}
	
	static function get_userindex($current_users){
	
		$db = JFactory::getDBO();
	
		//only get those users we need for performance
		$user_id_string = '0';		
		foreach($current_users as $users){						
			$user_id_string .= ','.$users->id;			
		}		
		
		$query = $db->getQuery(true);
		$query->select('user_id, group_id');
		$query->from('#__user_usergroup_map');		
		$query->where('user_id IN ('.$user_id_string.')');		
		$rows = $db->setQuery($query);				
		$rows = $db->loadObjectList();	
					
		return $rows;		
	}
	
	static function get_group_level_index(){
	
		$db = JFactory::getDBO();		
		
		$query = $db->getQuery(true);
		$query->select('group_id, level_id, level_title');
		$query->from('#__userprivatepage_usermap');		
		$rows = $db->setQuery($query);				
		$rows = $db->loadObjectList();
				
		return $rows;	
	}
	
	function get_groups_levels($groups){
		$levels = array();		
		foreach($this->group_level_index as $group_level_row){
			if(in_array($group_level_row->group_id, $groups)){
				$levels[] = $group_level_row->level_id;
			}
		}
		$levels = array_unique($levels);
		return $levels;
	}
	
	/*
	function get_options_status(){
	
		$options = array();
		$options[] = JHtml::_('select.option', '1', JText::_('JPUBLISHED'));
		$options[] = JHtml::_('select.option', '8',	JText::_('JUNPUBLISHED'));	
		$options[] = JHtml::_('select.option', '5',	JText::_('COM_USERPRIVATEPAGE_EMPTY'));		
		return $options;
	}
	*/
	
	
}
?>
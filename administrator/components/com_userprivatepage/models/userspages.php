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

class userprivatepageModelUserspages extends JModelList{	

	var $parent_groups;	
	protected $option = 'com_userprivatepage';	
	
	public function __construct($config = array()){
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'name', 'a.name',
				'username', 'a.username',				
				'id', 'a.id'				
			);
		}
		parent::__construct($config);
	}	

	protected function populateState($ordering = NULL, $direction = NULL){
	
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);	
		
		$filter_status = $app->getUserStateFromRequest($this->context.'.filter.level', 'filter_status', null, 'int');
		$this->setState('filter.status', $filter_status);	

		$groupId = $app->getUserStateFromRequest($this->context.'.filter.group', 'filter_group_id', null, 'int');
		$this->setState('filter.group_id', $groupId);
		
		$level_id = $app->getUserStateFromRequest($this->context.'.filter.level', 'filter_level_id', null, 'int');
		$this->setState('filter.level_id', $level_id);	
		
		$filter_preview = $app->getUserStateFromRequest($this->context.'.filter.preview', 'filter_preview', null, 'int');
		$this->setState('filter.preview', $filter_preview);		
		
		// List state information.		
		parent::populateState('a.username', 'asc');
	}
	
	protected function getStoreId($id = ''){
	
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');		
		$id	.= ':'.$this->getState('filter.group_id');
		$id	.= ':'.$this->getState('filter.level_id');
		$id	.= ':'.$this->getState('filter.status');
		$id	.= ':'.$this->getState('filter.preview');

		return parent::getStoreId($id);
	}
	
	protected function getListQuery(){
	
		$this->update_usergroup_levels_map();
		$userspages_ids = $this->get_userspages_ids();		
		
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'				
			)
		);
		$query->from('`#__users` AS a');

		// Join over the group mapping table.
		$query->select('COUNT(map.group_id) AS group_count');
		$query->join('LEFT', '#__user_usergroup_map AS map ON map.user_id = a.id');
		$query->group('a.id');

		// Join over the user groups table.		
		$query->select('GROUP_CONCAT(DISTINCT g2.id SEPARATOR '.$db->Quote("-").') AS group_ids');
		$query->join('LEFT', '#__usergroups AS g2 ON g2.id = map.group_id');
		
		// Join with access levels
		$query->select('GROUP_CONCAT(DISTINCT l.level_id SEPARATOR '.$db->Quote("-").') AS level_ids');		
		$query->join('LEFT', '#__userprivatepage_usermap AS l ON l.group_id = map.group_id ');
		
		//join with the users page
		$query->select('p.published AS published, p.text AS pagetext, p.title AS pagetitle, p.id AS page_id, p.show_title AS show_title');
		$query->join('LEFT', '#__userprivatepage_pages AS p ON p.user_id = a.id');
		
		// Filter the items over the group id if set.
		if ($groupId = $this->getState('filter.group_id')) {
			$query->join('LEFT', '#__user_usergroup_map AS map2 ON map2.user_id = a.id');
			$query->where('map2.group_id = '.(int) $groupId);
		}
		
		// Filter the items over the level id if set.
		if ($level_id = $this->getState('filter.level_id')) {
			$query->join('LEFT', '#__userprivatepage_usermap AS map3 ON map3.group_id = map.group_id');
			$query->where('map3.level_id = '.(int) $level_id);
		}	
		
		// Filter the items over published status if set.
		$filter_status = $this->getState('filter.status');
		if($filter_status){			
			if($filter_status==8 || $filter_status==1){
				//published = 1
				if($filter_status==8){
					//unpublished = 8					
					$filter_status = 0;
				}			
				$query->where('published='.$db->q($filter_status));
			}
			if($filter_status==5){
				//empty = 5							
				$query->where('a.id NOT IN ('.implode(',', $userspages_ids).')');				
			}
		}		

		// Filter the items over the search string if set.
		if ($this->getState('filter.search') != '') {
			// Escape the search token.
			$token	= $db->Quote('%'.$db->escape($this->getState('filter.search')).'%');

			// Compile the different search clauses.
			$searches	= array();
			$searches[]	= 'a.name LIKE '.$token;
			$searches[]	= 'a.username LIKE '.$token;
			$searches[]	= 'a.email LIKE '.$token;
			$searches[]	= 'p.text LIKE '.$token;

			// Add the clauses to the query.
			$query->where('('.implode(' OR ', $searches).')');
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');		
		$query->order($db->escape($orderCol.' '.$orderDirn));
		
		//echo nl2br($query);
		return $query;
	}
	
	function update_usergroup_levels_map(){
	
		$db = JFactory::getDBO();
		
		//empty table
		$db->setQuery("TRUNCATE TABLE #__userprivatepage_usermap ");
		$db->query();			
		
		$accesslevels_array = array();
		
		//get accesslevels/usergroups		
		$query = $db->getQuery(true);
		$query->select('id, title, rules');
		$query->from('#__viewlevels');		
		$rows = $db->setQuery($query);				
		$rows = $db->loadObjectList();
						
		foreach($rows as $accesslevel){				
			$rules = $accesslevel->rules;
			$rules = str_replace('[','',$rules);
			$rules = str_replace(']','',$rules);
			$level_id = $accesslevel->id;
			$level_title = $accesslevel->title;			
			$usergroups_array = explode(',',$rules);			
			$accesslevels_array[] = array($level_id, $level_title, $usergroups_array);						
		}
		
		$query = $db->getQuery(true);
		$query->select('id, parent_id');
		$query->from('#__usergroups');		
		$rows = $db->setQuery($query);				
		$rows = $db->loadObjectList();
					
		foreach($rows as $group){			
			$this->parent_groups = array($group->parent_id);
			$this->get_inherited_groups($group->parent_id, $rows);			
			$levels_inherited = $this->get_levels_from_group($group->id, $accesslevels_array);
			foreach($this->parent_groups as $parent_group){				
				$levels_inherited_temp = $this->get_levels_from_group($parent_group, $accesslevels_array);
				$levels_inherited = array_merge($levels_inherited, $levels_inherited_temp);
			}			
			$levels_inherited = array_unique($levels_inherited);			
			foreach($levels_inherited as $level_inherited){
				$level_title = '';
				for($n = 0; $n < count($accesslevels_array); $n++){			
					if($level_inherited==$accesslevels_array[$n][0]){
						$level_title = $accesslevels_array[$n][1];
					}
				}				
				
				$query = $db->getQuery(true);
				$query->insert('#__userprivatepage_usermap');
				$query->set('group_id='.$db->q($group->id));
				$query->set('level_id='.$db->q($level_inherited));	
				$query->set('level_title='.$db->q($level_title));		
				$db->setQuery((string)$query);
				$db->query();
				
			}
		}			
	}
	
	function get_levels_from_group($group_id, $accesslevels_array){
		$levels = array();
		for($n = 0; $n < count($accesslevels_array); $n++){			
			if(in_array($group_id, $accesslevels_array[$n][2])){
				$levels[] = $accesslevels_array[$n][0];
			}
		}
		return $levels;
	}
	
	function get_inherited_groups($parent_id, $usergroups){			
		if($parent_id){
			foreach($usergroups as $group){	
				if($group->id==$parent_id && $group->parent_id){
					$this->parent_groups[] = $group->parent_id;
					$this->get_inherited_groups($group->parent_id, $usergroups);	
					break;			
				}
			}
		}		
	}
	
	function get_userspages_ids(){
		
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true);
		$query->select('user_id');
		$query->from('#__userprivatepage_pages');		
		$rows = $db->setQuery($query);				
		$rows = $db->loadColumn();
			
		return $rows;
	}
	
}
?>
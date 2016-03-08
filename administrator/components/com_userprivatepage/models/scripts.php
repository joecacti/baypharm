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

class userprivatepageModelScripts extends JModelList{	
	
	protected $option = 'com_userprivatepage';	
	
	public function __construct($config = array()){
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'name', 'a.name',					
				'ordering', 'a.ordering',	
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
		
		// List state information.		
		parent::populateState('a.name', 'asc');
	}
	
	protected function getStoreId($id = ''){
	
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');			

		return parent::getStoreId($id);
	}
	
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
		$query->from('`#__userprivatepage_scripts` AS a');

		// Filter over the search string if set.
		$search = $this->getState('filter.search');
		if(!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('a.name LIKE '.$search);
			}
		}	

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');		
		$query->order($db->escape($orderCol.' '.$orderDirn));
		
		//echo nl2br($query);
		return $query;
	}	
	
	
}
?>
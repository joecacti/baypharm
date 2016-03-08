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

class userprivatepageViewPrevievv extends JViewLegacy{

	function display($tpl = null){	
	
		$db = JFactory::getDBO();
		
		//get controller
		$controller = new userprivatepageController();	
		
		$user_id = JRequest::getVar('user_id', '');		
		$this->assignRef('user_id', $user_id);
		
		//get default text
		$query = $db->getQuery(true);
		$query->select('text_when_empty, text_when_unpub');
		$query->from('#__userprivatepage_config');
		$query->where('id='.$db->q('1'));		
		$rows = $db->setQuery($query, 0, 1);				
		$rows = $db->loadObjectList();
		$text_when_empty = '';	
		$text_when_unpublished = '';		
		foreach($rows as $row){		
			$text_when_empty = $row->text_when_empty;	
			$text_when_unpublished = $row->text_when_unpub;
		}
		
		//get user page
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__userprivatepage_pages');
		$query->where('user_id='.$db->q($user_id));					
		$rows = $db->setQuery($query, 0, 1);				
		$rows = $db->loadObjectList();
		
		$id = 0;
		$title = '';
		$text = $text_when_empty;
		$published = 0;
		foreach($rows as $row){		
			$id = $row->id;	
			if($row->show_title=='1' || ($row->show_title=='2' && userprivatepageHelper::config('show_title'))){				
				$title = $row->title;				
			}
			if($row->published){
				$text = $row->text;	
				$published = 1;
			}else{	
				$text = $text_when_unpublished;
			}
		}
		//process UPP tags first
		if($controller->get_version_type()!='free' || ($controller->get_version_type()=='free' && $published)){	
			if($title){	
				$title = userprivatepageHelper::do_tags($title, $user_id);
			}
			$text = userprivatepageHelper::do_tags($text, $user_id);
		}
		$this->assignRef('title', $title);		
		//process content plugins
		$text = JHTML::_('content.prepare', $text);
		$this->assignRef('text', $text);		

		parent::display($tpl);
	}	
	
}
?>
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

class userprivatepageViewUsercomments extends JViewLegacy{

	protected $items;	
	protected $state;
	protected $pagination;	
	
	function display($tpl = null){	
				
		$db = JFactory::getDBO();	
		$app = JFactory::getApplication();		
		
		//$controller = new userprivatepageController();	
		
		$this->state = $this->get('State');	
		$this->items = $this->get('Items');			
		$this->pagination = $this->get('Pagination');		
		
		//get user id
		$user_id = intval(JRequest::getVar('user_id', ''));	
		$this->assignRef('user_id', $user_id);		
		
		//get username
		$query = $db->getQuery(true);
		$query->select('username, name');
		$query->from('#__users');
		$query->where('id='.$db->q($user_id));
		$rows = $db->setQuery($query, 0, 1);	
		$rows = $db->loadObjectList();	
		$user_name = '';
		$name = '';
		foreach($rows as $row){		
			$user_name = $row->username;
			$name = $row->name;
		}
		$this->assignRef('other_name', $user_name);	
		$this->assignRef('name', $name);			
		
		//comment edit
		$commentedit_id = 0;
		$commentedit_text = '';
		$temp = intval(JRequest::getVar('commentedit', ''));	
		foreach ($this->items as $i => $item){			
			if($item->id==$temp){
				$commentedit_id = $temp;
				$commentedit_text = $item->comment;
				break;
			}
		}
		$this->assignRef('commentedit_id', $commentedit_id);
		$this->assignRef('commentedit_text', $commentedit_text);
		
		//if linking to comment, check if the comment still exists
		$comment_id = intval(JRequest::getVar('comment', ''));
		if($comment_id){
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from('#__userprivatepage_comments');
			$query->where('id='.$db->q($comment_id));			
			$rows = $db->setQuery($query);				
			$rows = $db->loadObjectList();
			$comment_exists = 0;				
			foreach($rows as $row){		
				$comment_exists = 1;
			}
			if(!$comment_exists){
				$url = 'index.php?option=com_userprivatepage&view=usercomments&user_id='.$user_id;
				$message = JText::_('COM_USERPRIVATEPAGE_COMMENT_DELETED');				
				$app->redirect($url, $message);
			}
		}
		
		//mark as read for admin
		$query = $db->getQuery(true);		
		$query->update('#__userprivatepage_comments');
		$query->set('is_read='.$db->q('1'));
		$query->where('user_id='.$db->q($user_id));
		$query->where('is_read='.$db->q('0'));
		$query->where('to_user='.$db->q('0'));
		$db->setQuery((string)$query);
		$db->query();	
		
		//language		
		$lang = JFactory::getLanguage();		
		$lang->load('com_users', JPATH_ADMINISTRATOR, null, false);	
		
		//toolbar				
		JToolBarHelper::apply('usercomment_apply', 'JToolbar_Apply');
		JToolBarHelper::save('usercomment_save', 'JToolbar_Save');		
		JToolBarHelper::cancel('cancel', 'JToolbar_Close');	
		JToolBarHelper::custom('usercomments_delete','delete.png','delete_f2.png',JText::_('JTOOLBAR_DELETE'),false,false);	
		
		//sidebar
		if(userprivatepageHelper::joomla_version() >= '3.0'){			
			JHtmlSidebar::setAction('index.php?option=com_userprivatepage&view=usercomments');					
			userprivatepageHelper::addSubmenu('usercomments');							
			$this->sidebar = JHtmlSidebar::render();	
		}else{
			$this->sidebar = '';
		}
		
		parent::display($tpl);
	}	
	
	
}
?>
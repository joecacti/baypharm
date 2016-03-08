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

	protected $items;	
	protected $state;
	protected $pagination;	

	function display($tpl = null){	
		
		$db = JFactory::getDBO();	
		$app = JFactory::getApplication();			
		
		//get helper
		require_once(JPATH_ROOT.'/administrator/components/com_userprivatepage/helpers/userprivatepage.php');
		
		$this->state = $this->get('State');	
		$this->items = $this->get('Items');			
		$this->pagination = $this->get('Pagination');		
		
		$user = JFactory::getUser();
		$user_id = $user->get('id');		
		
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
		$show_comments = userprivatepageHelper::config('comments');
		foreach($rows as $row){		
			$id = $row->id;	
			if($row->show_title=='1' || ($row->show_title=='2' && userprivatepageHelper::config('show_title'))){				
				$title = $row->title;				
			}	
			if($row->comments=='1' || ($row->comments=='2' && userprivatepageHelper::config('comments'))){				
				$show_comments = 1;				
			}else{
				$show_comments = 0;	
			}				
			if($row->published){
				$text = $row->text;				
				$published = 1;	
			}else{	
				$text = $text_when_unpublished;
			}
		}
		$this->assignRef('show_comments', $show_comments);
		//process UPP tags first
		if(userprivatepageHelper::get_version_type()!='free' || (userprivatepageHelper::get_version_type()=='free' && $published)){	
			if($title){
				$title = userprivatepageHelper::do_tags($title, $user_id);
			}			
			$text = userprivatepageHelper::do_tags($text, $user_id);	
		}
		$this->assignRef('title', $title);			
		//process content plugins				
		$text = JHTML::_('content.prepare', $text);
		$this->assignRef('text', $text);	
		
		//comment edit
		$commentedit_id = 0;
		$commentedit_text = '';		
		$temp = intval(JRequest::getVar('commentedit', ''));	
		foreach ($this->items as $i => $item){			
			if($item->id==$temp){
				$commentedit_id = $temp;
				$commentedit_text = $item->comment;
				if(userprivatepageHelper::config('allow_comment_edit')=='time'){
					$commentedit_allowed = 0;													
					$time_extra = 60*intval(userprivatepageHelper::config('allow_comment_edit_time'));													
					$time_create = strtotime($item->date);																									
					$date = JFactory::getDate();																									
					$now = $date->toSql();													
					$time_current = strtotime($now);													
					if(($time_create+$time_extra) < $time_current){	
						$url = JRoute::_('index.php?option=com_userprivatepage&view=userpage#toolate', false);	
						$message = userprivatepageHelper::config('allow_comment_edit_time').' min '.JText::_('COM_USERPRIVATEPAGE_EDITINGEXPIRED');				
						$app->redirect($url, $message, 'error');
					}
				}
				if(userprivatepageHelper::config('allow_comment_edit')=='until_read' && $item->is_read){
					$url = JRoute::_('index.php?option=com_userprivatepage&view=userpage#adminread', false);	
					$message = JText::_('COM_USERPRIVATEPAGE_ADMIN_READ');				
					$app->redirect($url, $message, 'error');
				}
				break;
			}
		}
		$this->assignRef('commentedit_id', $commentedit_id);
		$this->assignRef('commentedit_text', $commentedit_text);			
				
		//mark as read for user
		$query = $db->getQuery(true);		
		$query->update('#__userprivatepage_comments');
		$query->set('is_read='.$db->q('1'));
		$query->where('user_id='.$user_id);
		$query->where('is_read='.$db->q('0'));
		$query->where('to_user='.$db->q('1'));
		$db->setQuery((string)$query);
		$db->query();

		//if not logged in redirect to login page
		if(!$user_id){	
			$app->enqueueMessage(JText::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'));	
			//make sef
			$app->redirect('index.php?option=com_users&view=login&return=aW5kZXgucGhwP29wdGlvbj1jb21fdXNlcnByaXZhdGVwYWdl');	
		}
		
		$document = JFactory::getDocument();	
		$document->addStyleSheet('components/com_userprivatepage/css/userprivatepage1.css');
		
		parent::display($tpl);
	}	
	
}
?>
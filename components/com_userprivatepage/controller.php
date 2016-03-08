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

jimport('joomla.application.component.controller');

class userprivatepageController extends JControllerLegacy{
	
	function __construct(){	
	
		require_once JPATH_ROOT.'/administrator/components/com_userprivatepage/helpers/userprivatepage.php';			
		parent::__construct();	
		
		// Set a default view if none exists
		if (!JRequest::getVar('view')){			
			JRequest::setVar('view', 'userpage');
		}		
	}
	
	function comment_save(){
	
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();				
			
		// Check for request forgeries 
		JRequest::checkToken() or jexit('Invalid Token');
		
		$user = JFactory::getUser();
		$user_id = $user->get('id');
		//no messing about
		if(!$user_id){
			exit;
		}
		
		//get vars				
		$comment = JRequest::getVar('upp_comment', '', 'post', 'string');	
		$comment = strip_tags($comment);
		$comment = str_replace('>', '', $comment);
		$comment = str_replace('<', '', $comment);
		if($comment==''){
			echo JText::_('JNO').' '.JText::_('COM_USERPRIVATEPAGE_COMMENT');
			exit;
		}		
		$date = JFactory::getDate();	
		$commentedit_id = JRequest::getVar('commentedit_id', 0);			
		
		$out_of_time = 0;
		$admin_read = 0;
		if(userprivatepageHelper::config('comments')){
			
			if($commentedit_id){			
				//edit	
				
				//check if correct user
				$query = $db->getQuery(true);
				$query->select('user_id, date, is_read');
				$query->from('#__userprivatepage_comments');
				$query->where('id='.$db->q($commentedit_id));				
				$rows = $db->setQuery($query);				
				$rows = $db->loadObjectList();
				$item_user = 0;
				$item_date = '';
				$is_read = 0;					
				foreach($rows as $row){		
					$item_user = $row->user_id;	
					$item_date = $row->date;	
					$is_read = $row->is_read;
				}				
				if($user_id!=$item_user){
					exit;
				}
				
				//check if user is allowed to edit				
				if(!userprivatepageHelper::config('allow_comment_edit')){
					exit;
				}
				
				//check if user is editting in time				
				if(userprivatepageHelper::config('allow_comment_edit')=='time'){													
					$time_extra = 60*intval(userprivatepageHelper::config('allow_comment_edit_time'));													
					$time_create = strtotime($item_date);															
					$now = $date->toSql();													
					$time_current = strtotime($now);													
					if(($time_create+$time_extra) < $time_current){
						$out_of_time = 1;
					}
				}	
				
				//check if user is editting before admin read message			
				if(userprivatepageHelper::config('allow_comment_edit')=='until_read' && $is_read){					
					$admin_read = 1;				
				}				
				
				//update
				if(!$out_of_time && !$admin_read){	
					$query = $db->getQuery(true);	
					$query->update('#__userprivatepage_comments');
					$query->set('comment='.$db->q($comment));	
					$query->set('date='.$db->q($date));	
					$query->where('id='.(int)$commentedit_id);
					$db->setQuery((string)$query);
					$db->query();
					
					//notify admin of edit comment
					userprivatepageHelper::notify($commentedit_id, 'admin_edit', $user_id);					
				}
			}else{
				//insert	
				$query = $db->getQuery(true);		
				$query->insert('#__userprivatepage_comments');
				$query->set('user_id='.$db->q($user_id));				
				$query->set('comment='.$db->q($comment));
				$query->set('date='.$db->q($date));		
				$db->setQuery((string)$query);
				$db->query();	
				
				//notify admin of new comment			
				userprivatepageHelper::notify($db->insertid(), 'admin_add', $user_id);				
			}
		}		
		
		//redirect	
		if($out_of_time){
			$message = userprivatepageHelper::config('allow_comment_edit_time').' min '.JText::_('COM_USERPRIVATEPAGE_EDITINGEXPIRED');
			$messagetype = 'error';
		}elseif($admin_read){
			$message = JText::_('COM_USERPRIVATEPAGE_ADMIN_READ');
			$messagetype = 'error';
		}else{
			$message = JText::_('COM_USERPRIVATEPAGE_COMMENT').' '.JText::_('COM_USERPRIVATEPAGE_SAVED');
			$messagetype = 'message';
		}		
		$url = 'index.php?option=com_userprivatepage&view=userpage';						
		$this->setRedirect($url, $message, $messagetype);
	}
	
}
?>
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
	
	public $upp_version = '1.2.1';
	private $upp_version_type = 'free';		
	
	function __construct(){		
		
		require_once JPATH_COMPONENT.'/helpers/userprivatepage.php';							
		parent::__construct();		
	}	

	function display($cachable = false, $urlparams = false){
		
		
		// Set a default view if none exists
		if (!JRequest::getVar('view')){			
			JRequest::setVar('view', 'userspages');
		}
		
		//set title			
		JToolBarHelper::title('User Private Page','upp_icon');			
		
		//display css
		//not via addDocument else the icon is set to 14 px
		if(JRequest::getVar('layout', '')!='csv'){
			echo '<link rel="stylesheet" href="components/com_userprivatepage/css/userprivatepage2.css" type="text/css" />';			
						
			echo '<div class="upp';
			if(userprivatepageHelper::joomla_version() >= '3.0'){
				echo ' joomla3';
			}
			echo '">';	
		}	
		
		//display message if not enabled			
		$this->not_enabled_message();				
		
		if(userprivatepageHelper::joomla_version() >= '3.0'){
			//bootstrap selects
			JHtml::_('bootstrap.tooltip');
			JHtml::_('behavior.multiselect');
			JHtml::_('formbehavior.chosen', 'select');
		}else{	
			//make sure mootools is loaded					
			JHTML::_('behavior.mootools');
			
			// Load the submenu							
			userprivatepageHelper::addSubmenu(JRequest::getWord('view', 'userprivatepage'));				
		}			
		
		parent::display();	
			
		if(JRequest::getVar('layout', '')!='csv'){
			echo '</div>';	
			//display footer
			if(JRequest::getVar('view', '')!='previevv'){					
				$this->display_footer();			
			}	
		}
	}	
	
	function config_save(){
	
		// Check for request forgeries 
		JRequest::checkToken() or jexit('Invalid Token');
		$db = JFactory::getDBO();
		
		$config_array = userprivatepageHelper::get_config_array();
		
		$config_array['enabled'] = JRequest::getVar('enabled', '', 'post');	
		$config_array['show_title'] = JRequest::getVar('show_title', '', 'post');	
		$text_when_empty = JRequest::getVar('text_when_empty', '', 'post', 'string', JREQUEST_ALLOWHTML);
		$text_when_unpub = JRequest::getVar('text_when_unpublished', '', 'post', 'string', JREQUEST_ALLOWHTML);
		$config_array['access_tooltip'] = JRequest::getVar('access_tooltip', '', 'post');	
		$config_array['modal_width'] = JRequest::getVar('modal_width', '', 'post');
		$config_array['modal_height'] = JRequest::getVar('modal_height', '', 'post');	
		$config_array['version_checker'] = JRequest::getVar('version_checker', '', 'post');	
		$config_array['comments'] = JRequest::getVar('comments', '', 'post');	
		$config_array['bb_quote'] = JRequest::getVar('bb_quote', '', 'post');		
		$config_array['bb_image'] = JRequest::getVar('bb_image', '', 'post');
		$config_array['icons_style'] = JRequest::getVar('icons_style', '', 'post');	
		$config_array['smilies'] = '';
		$smilies = userprivatepageHelper::get_smilies();
		for($n = 0; $n < count($smilies); $n++){
			if(JRequest::getVar('smilies_'.$smilies[$n][0], '', 'post')){
				$config_array['smilies'] = $config_array['smilies'].' '.$smilies[$n][0];
			}
		}	
		$config_array['comments_date_format'] = JRequest::getVar('comments_date_format', '', 'post');	
		$config_array['allow_comment_edit'] = JRequest::getVar('allow_comment_edit', '', 'post');	
		$config_array['allow_comment_edit_time'] = JRequest::getVar('allow_comment_edit_time', '', 'post', 'int');	
		$config_array['icons_dir_default'] = JRequest::getVar('icons_dir_default', '', 'post');					
		$config_array['icons_dir'] = JRequest::getVar('icons_dir', '', 'post');			
		$config_array['notify_admin_mail'] = JRequest::getVar('notify_admin_mail', '', 'post');
		$config_array['notify_admin_messaging'] = JRequest::getVar('notify_admin_messaging', '', 'post');
		$config_array['notify_admin_mail_edit'] = JRequest::getVar('notify_admin_mail_edit', '', 'post');
		$config_array['notify_admin_messaging_edit'] = JRequest::getVar('notify_admin_messaging_edit', '', 'post');
		$config_array['notify_user'] = JRequest::getVar('notify_user', '', 'post');
		$notifymessage = JRequest::getVar('notifymessage', '', 'post', 'string', JREQUEST_ALLOWHTML);
		$notifymessage_admin_add = JRequest::getVar('notifymessage_admin_add', '', 'post', 'string', JREQUEST_ALLOWHTML);
		$notifymessage_admin_edit = JRequest::getVar('notifymessage_admin_edit', '', 'post', 'string', JREQUEST_ALLOWHTML);	
		$config_array['subject_add'] = JRequest::getVar('subject_add', '', 'post');
		$config_array['subject_edit'] = JRequest::getVar('subject_edit', '', 'post');
		$config_array['subject_user'] = JRequest::getVar('subject_user', '', 'post');
		$config_array['notify_admins'] = JRequest::getVar('notify_admins', '', 'post');
		$config_array['show_when_read'] = JRequest::getVar('show_when_read', '', 'post');		
		
		$registry = new JRegistry;
		$registry->loadArray($config_array);
		$config_string = $registry->toString();			
			
		//update config
		$query = $db->getQuery(true);		
		$query->update('#__userprivatepage_config');
		$query->set('config='.$db->q($config_string));	
		$query->set('text_when_empty='.$db->q($text_when_empty));	
		$query->set('text_when_unpub='.$db->q($text_when_unpub));	
		$query->set('notifymessage='.$db->q($notifymessage));
		$query->set('notifymessage_admin_add='.$db->q($notifymessage_admin_add));
		$query->set('notifymessage_admin_edit='.$db->q($notifymessage_admin_edit));				
		$query->where('id='.$db->q('1'));
		$db->setQuery((string)$query);
		$db->query();		
		
		//redirect	
		if(JRequest::getVar('apply', '')){
			$url = 'index.php?option=com_userprivatepage&view=configuration';
		}else{			
			$url = 'index.php?option=com_userprivatepage&view=userspages';
		}	
		$message = userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_CONFIGURATION')).' '.JText::_('COM_USERPRIVATEPAGE_SAVED');		
		$this->setRedirect($url, $message);		
	}			

	function display_footer(){				
		echo '<div class="smallgrey" id="pi_footer">';		
		echo '<table>';
		echo '<tr>';
		echo '<td class="text_right">';
		echo '<a href="http://www.pages-and-items.com/extensions/user-private-page" target="_blank">User-Private-Page</a>';
		echo '</td>';
		echo '<td class="five_pix">';
		echo '&copy;';
		echo '</td>';
		echo '<td>';
		echo '2014 Carsten Engel';		
		echo '</td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td class="text_right">';
		echo userprivatepageHelper::low(JText::_('JVERSION'));
		echo '</td>';
		echo '<td class="five_pix">';
		echo '=';
		echo '</td>';
		echo '<td>';
		echo $this->upp_version.' ('.$this->upp_version_type.' '.userprivatepageHelper::low(JText::_('JVERSION')).')';
		if($this->upp_version_type!='trial'){
			echo ' <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="blank">GNU/GPL License</a>';
		}
		echo '</td>';
		echo '</tr>';
		//version checker
		if(userprivatepageHelper::config('version_checker')){
			echo '<tr>';
			echo '<td class="text_right">';
			echo JText::_('COM_USERPRIVATEPAGE_LATEST_VERSION');
			echo '</td>';
			echo '<td class="five_pix">';
			echo '=';
			echo '</td>';
			echo '<td>';
			$app = JFactory::getApplication();
			$latest_version_message = $app->getUserState( "com_userprivatepage.latest_version_message", '');
			if($latest_version_message==''){
				$latest_version_message = JText::_('COM_USERPRIVATEPAGE_VERSION_CHECKER_NOT_AVAILABLE');
				$url = 'http://www.pages-and-items.com/latest_version.php?extension=userprivatepage';		
				$file_object = @fopen($url, "r");		
				if($file_object == TRUE){
					$version = fread($file_object, 1000);
					$latest_version_message = $version;
					if($this->upp_version!=$version){
						$latest_version_message .= ' <span style="color: red;">'.JText::_('COM_USERPRIVATEPAGE_NEWER_VERSION').'</span>';
						if($this->upp_version_type=='pro'){
							$download_url = 'http://www.pages-and-items.com/my-extensions';
						}elseif($this->upp_version_type=='trial'){
							$download_url = 'http://engelweb.nl/trialversions/';
						}else{
							$download_url = 'http://www.pages-and-items.com/extensions/user-private-page';
						}
						$latest_version_message .= ' <a href="'.$download_url.'" target="_blank">'.JText::_('COM_USERPRIVATEPAGE_DOWNLOAD').'</a>';
						if($this->upp_version_type!='pro'){
							$latest_version_message .= ' <a href="index.php?option=com_installer&view=update">'.userprivatepageHelper::low(JText::_('JLIB_INSTALLER_UPDATE')).'</a>';
						}
					}else{
						$latest_version_message .= ' <span class="pi_green">'.JText::_('COM_USERPRIVATEPAGE_IS_LATEST_VERSION').'</span>';
					}
					fclose($file_object);
				}				
				$app->setUserState( "com_userprivatepage.latest_version_message", $latest_version_message );
			}
			echo $latest_version_message;
			echo '</td>';
			echo '</tr>';
		}		
		echo '<tr>';
		echo '<td class="text_right" colspan="2">';
		echo userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_REVIEW_B')); 
		echo '</td>';		
		echo '<td>';				
		echo '<a href="http://extensions.joomla.org/extensions/extension/access-a-security/site-access/user-private-page';
		if($this->upp_version_type=='pro'){
			echo '-pro';
		}
		echo '" target="_blank">';
		echo 'Joomla! Extensions Directory</a>';
		echo '</td>';		
		echo '</tr>';					
		echo '</table>';		
		echo '</div>';	
	}		
	
	function ajax_version_checker(){
		$message = JText::_('COM_USERPRIVATEPAGE_VERSION_CHECKER_NOT_AVAILABLE');	
		$url = 'http://www.pages-and-items.com/latest_version.php?extension=userprivatepage';		
		$file_object = @fopen($url, "r");		
		if($file_object == TRUE){
			$version = fread($file_object, 1000);
			$message = JText::_('COM_USERPRIVATEPAGE_LATEST_VERSION').' = '.$version;
			if($this->upp_version!=$version){
				$message .= '<div><span class="pi_red">'.JText::_('COM_USERPRIVATEPAGE_NEWER_VERSION').'</span>.</div>';
				if($this->upp_version_type=='pro'){
					$download_url = 'http://www.pages-and-items.com/my-extensions';				
				}else{
					$download_url = 'http://www.pages-and-items.com/extensions/user-private-page';
				}
				$message .= '<div><a href="'.$download_url.'" target="_blank">'.JText::_('COM_USERPRIVATEPAGE_DOWNLOAD').'</a>';
				if($this->upp_version_type!='pro'){
					$message .= '<br /><a href="index.php?option=com_installer&view=update">'.userprivatepageHelper::low(JText::_('JLIB_INSTALLER_UPDATE')).'</a>';
				}
				$message .= '</div>';	
			}else{
				$message .= '<div><span class="pi_green">'.JText::_('COM_USERPRIVATEPAGE_IS_LATEST_VERSION').'</span>.</div>';
			}
			fclose($file_object);
		}		
		echo $message;
		exit;
	}
	
	function not_enabled_message(){
		if(!userprivatepageHelper::config('enabled')){
			$lang = JFactory::getLanguage();
			$lang->load('com_installer', JPATH_ADMINISTRATOR, null, false);
			echo '<p class="pi_warning">'.JText::_('COM_INSTALLER_TYPE_COMPONENT').' '.userprivatepageHelper::low(JText::_('JDISABLED')).'. '.JText::_('COM_USERPRIVATEPAGE_ENABLE_THIS_IN').' <a href="index.php?option=com_userprivatepage&view=configuration">'.userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_CONFIGURATION')).'</a>.</p>';
		}
	}			
	
	public function get_version_type(){
	
		//so that private var is available for templates
		return $this->upp_version_type;
	}	
	
	function userspages_empty(){
		
		$db = JFactory::getDBO();
		
		// Check for request forgeries 
		JRequest::checkToken() or jexit('Invalid Token');			
		
		$cid = JRequest::getVar('cid', null, 'post', 'array');			
		
		userprivatepageHelper::check_if_array($cid);
		
		if (count($cid)){			
			//delete page(s)			
			$query = $db->getQuery(true);
			$query->delete();
			$query->from('#__userprivatepage_pages');
			$query->where('user_id IN (' . implode(',', $cid) . ')');
			$db->setQuery((string)$query);
			$db->query();
		}		
		
		$message = JText::_('COM_USERPRIVATEPAGE_PAGES').' '.JText::_('COM_USERPRIVATEPAGE_EMPTIED');
		$this->setRedirect("index.php?option=com_userprivatepage&view=userspages", $message);
	}
	
	function userspages_publish(){
		
		$db = JFactory::getDBO();
		
		// Check for request forgeries 
		JRequest::checkToken() or jexit('Invalid Token');			
		
		$cid = JRequest::getVar('cid', null, 'post', 'array');			
		
		userprivatepageHelper::check_if_array($cid);
		
		if (count($cid)){			
			//publish page(s)			
			$query = $db->getQuery(true);			
			$query->update('#__userprivatepage_pages');
			$query->set('published=1');
			$query->where('user_id IN (' . implode(',', $cid) . ')');
			$db->setQuery((string)$query);
			$db->query();
		}
		
		$message = JText::_('COM_USERPRIVATEPAGE_PAGES').' '.userprivatepageHelper::low(JText::_('JPUBLISHED'));
		$this->setRedirect("index.php?option=com_userprivatepage&view=userspages", $message);
	}
	
	function userspages_unpublish(){
		
		$db = JFactory::getDBO();
		
		// Check for request forgeries 
		JRequest::checkToken() or jexit('Invalid Token');			
		
		$cid = JRequest::getVar('cid', null, 'post', 'array');				
		
		userprivatepageHelper::check_if_array($cid);
		
		if (count($cid)){			
			//publish page(s)			
			$query = $db->getQuery(true);			
			$query->update('#__userprivatepage_pages');
			$query->set('published=0');
			$query->where('user_id IN (' . implode(',', $cid) . ')');
			$db->setQuery((string)$query);
			$db->query();
		}		
		
		$message = JText::_('COM_USERPRIVATEPAGE_PAGES').' '.userprivatepageHelper::low(JText::_('JUNPUBLISHED'));
		$this->setRedirect('index.php?option=com_userprivatepage&view=userspages', $message);
	}
	
	function userpage_unpublish(){
	
		$db = JFactory::getDBO();		
		
		// Check for request forgeries 
		JRequest::checkToken() or jexit('Invalid Token');	
		
		$user_id = intval(JRequest::getVar('user_id', 0, 'post'));
		
		$query = $db->getQuery(true);		
		$query->update('#__userprivatepage_pages');
		$query->set('published=0');				
		$query->where('user_id='.(int)$user_id);
		$db->setQuery((string)$query);
		$db->query();
		
		//redirect
		$url = 'index.php?option=com_userprivatepage&view=userspages';				
		$this->setRedirect($url, userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_PAGE')).' '.userprivatepageHelper::low(JText::_('JUNPUBLISHED')));
	}
	
	function userpage_publish(){
	
		$db = JFactory::getDBO();		
		
		// Check for request forgeries 
		JRequest::checkToken() or jexit('Invalid Token');	
		
		$user_id = intval(JRequest::getVar('user_id', 0, 'post'));
		
		$query = $db->getQuery(true);		
		$query->update('#__userprivatepage_pages');
		$query->set('published=1');				
		$query->where('user_id='.(int)$user_id);
		$db->setQuery((string)$query);
		$db->query();
		
		//redirect
		$url = 'index.php?option=com_userprivatepage&view=userspages';						
		$this->setRedirect($url, userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_PAGE')).' '.userprivatepageHelper::low(JText::_('JPUBLISHED')));
	}
	
	function userpage_save(){
	
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();				
			
		// Check for request forgeries 
		JRequest::checkToken() or jexit('Invalid Token');
		
		//get vars
		$id = intval(JRequest::getVar('page_id', 0, 'post'));
		$user_id = intval(JRequest::getVar('user_id', 0, 'post'));
		$groups = userprivatepageHelper::get_groups($user_id, 0, $id);	
		$title = strip_tags(JRequest::getVar('page_title', '', 'post'));		
		$text = JRequest::getVar('page_text', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$published = intval(JRequest::getVar('published', '', 'post'));	
		$show_title = intval(JRequest::getVar('show_title', '', 'post'));	
		$comments = intval(JRequest::getVar('comments', '', 'post'));					
	
		if($id==0){
			//new 					
			$query = $db->getQuery(true);
			$query->insert('#__userprivatepage_pages');
			$query->set('user_id='.$db->q($user_id));
			$query->set('title='.$db->q($title));
			$query->set('text='.$db->q($text));
			$query->set('published='.$published);	
			$query->set('show_title='.$show_title);		
			$query->set('comments='.$comments);		
		}else{
			//edit			
			$query = $db->getQuery(true);		
			$query->update('#__userprivatepage_pages');
			$query->set('user_id='.$db->q($user_id));
			$query->set('title='.$db->q($title));
			$query->set('text='.$db->q($text));
			$query->set('published='.$published);
			$query->set('show_title='.$show_title);	
			$query->set('comments='.$comments);									
			$query->where('id='.(int)$id);					
		}	
		$db->setQuery((string)$query);
		$db->query();	
		
		//redirect	
		if(JRequest::getVar('apply', '')){
			$url = 'index.php?option=com_userprivatepage&view=userpage&user_id='.$user_id;
		}else{			
			$url = 'index.php?option=com_userprivatepage&view=userspages';
		}					
		$this->setRedirect($url, userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_PAGE')).' '.JText::_('COM_USERPRIVATEPAGE_SAVED'));
	}
	
	function script_save(){
	
		$db = JFactory::getDBO();		
	
		// Check for request forgeries 
		JRequest::checkToken() or jexit('Invalid Token');
		
		$script_id = intval(JRequest::getVar('script_id', ''));		
		$script_name = JRequest::getVar('script_name', '');	
		$value = JRequest::getVar('script_code','','post','string', JREQUEST_ALLOWRAW);	
		$value = str_replace('=','[equal]',$value);
		$new_line = '
';
		$value = str_replace($new_line,'[newline]',$value);		
		
		if($script_id){
			//update			
			$query = $db->getQuery(true);		
			$query->update('#__userprivatepage_scripts');
			$query->set('name='.$db->q($script_name));
			$query->set('value='.$db->q($value));			
			$query->where('id='.(int)$script_id);
			$db->setQuery((string)$query);
			$db->query();
		}else{
			//insert						
			$query = $db->getQuery(true);
			$query->insert('#__userprivatepage_scripts');
			$query->set('name='.$db->q($script_name));
			$query->set('value='.$db->q($value));			
			$db->setQuery((string)$query);
			$db->query();
			
			//get new id
			$script_id = $db->insertid(); 
		}
		
		//redirect
		$url = 'index.php?option=com_userprivatepage&view=scripts';
		if(JRequest::getVar('apply', 0)){
			$url = 'index.php?option=com_userprivatepage&view=script&id='.$script_id;
		}
		$message = userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_SCRIPT')).' '.JText::_('COM_USERPRIVATEPAGE_SAVED');
		$this->setRedirect($url, $message);
	}	
	
	function scripts_delete(){	
	
		$db = JFactory::getDBO();	
		
		// Check for request forgeries 
		JRequest::checkToken() or jexit('Invalid Token');			
		
		$cid = JRequest::getVar('cid', null, 'post', 'array');		
		
		userprivatepageHelper::check_if_array($cid);
		
		if (count($cid)){
			$ids = implode(',', $cid);	
			
			//delete dynamic redirects			
			$query = $db->getQuery(true);
			$query->delete();
			$query->from('#__userprivatepage_scripts');			
			$query->where('id IN ('.$ids.')');
			$db->setQuery((string)$query);
			$db->query();
		}
		$message = userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_SCRIPTS')).' '.JText::_('COM_USERPRIVATEPAGE_DELETED');
		$this->setRedirect("index.php?option=com_userprivatepage&view=scripts", $message);
	}	
	
	function save_order_scripts(){
	
		$db = JFactory::getDBO();	
		
		$script_order = JRequest::getVar('order', array(), 'post', 'array');
		$script_ids = JRequest::getVar('script_id', array(), 'post', 'array');
		$order_ids = JRequest::getVar('order_id', array(), 'post', 'array');		
		for($n = 0; $n < count($script_ids); $n++){		
			$temp_order = $script_order[$n];
			$temp_id = $script_ids[$n];			
			
			//update order					
			$query = $db->getQuery(true);		
			$query->update('#__userprivatepage_scripts');
			$query->set('ordering='.$db->q($temp_order));					
			$query->where('id='.(int)$temp_id);
			$db->setQuery((string)$query);
			$db->query();			
		}	
		$url = 'index.php?option=com_userprivatepage&view=scripts';
		$message = userprivatepageHelper::low(JText::_('JGLOBAL_FIELD_FIELD_ORDERING_LABEL')).' '.JText::_('COM_USERPRIVATEPAGE_SAVED');
		$this->setRedirect($url, $message);
	}	
	
	function save_order_ajax_scripts(){
	
		$db = JFactory::getDBO();
		
		JRequest::checkToken() or jexit('Invalid Token');
		
		$cid = $this->input->post->get('cid', null, 'array');
		$order = $this->input->post->get('order', null, 'array');
		
		if(count($cid)){			
			for($n = 0; $n < count($cid); $n++){
				//do update
				$query = $db->getQuery(true);		
				$query->update('#__userprivatepage_scripts');				
				$query->set('ordering='.(int)$order[$n]);
				$query->where('id='.(int)$cid[$n]);
				$db->setQuery((string)$query);
				$db->query();
			}			
			echo "1";
		}	
		
		// Close the application
		JFactory::getApplication()->close();
	}
	
	function tab_session_save(){
	
		$app = JFactory::getApplication();		
		$id = JRequest::getVar('id', '');
		$active = JRequest::getVar('active', '');			
		$app->setUserState("com_userprivatepage.tab_".$id, $active);
	}
	
	function usercomment_save(){
	
		$db = JFactory::getDBO();	
		$app = JFactory::getApplication();
		
		// Check for request forgeries 
		JRequest::checkToken() or jexit('Invalid Token');	
		
		//get vars				
		$comment = JRequest::getVar('upp_comment', '', 'post', 'string');	
		$comment = strip_tags($comment);
		if($comment==''){
			echo JText::_('JNO').' '.userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_COMMENT'));
			exit;
		}		
		$date = JFactory::getDate();		
		$user_id = JRequest::getVar('user_id', '');	
		$commentedit_id = JRequest::getVar('commentedit_id', 0);
			
		$query = $db->getQuery(true);
		if($commentedit_id){
			//edit			
			$query->update('#__userprivatepage_comments');
			$query->set('comment='.$db->q($comment));		
			$query->where('id='.(int)$commentedit_id);
		}else{
			//insert			
			$query->insert('#__userprivatepage_comments');
			$query->set('user_id='.$db->q($user_id));	
			$query->set('to_user='.$db->q('1'));
			$query->set('comment='.$db->q($comment));
			$query->set('date='.$db->q($date));				
			
					
		}
		$db->setQuery((string)$query);
		$db->query();	
		
		//notify user
		if(!$commentedit_id){
			userprivatepageHelper::notify($db->insertid(), 'notify_user', $user_id);
		}
			
		//redirect	
		$url = 'index.php?option=com_userprivatepage&view=userscomments';	
		if(JRequest::getVar('apply', 0)){
			$url = 'index.php?option=com_userprivatepage&view=usercomments&user_id='.$user_id;	
		}								
		$this->setRedirect($url, JText::_('COM_USERPRIVATEPAGE_COMMENT').' '.JText::_('COM_USERPRIVATEPAGE_SAVED'));		
		
	}
	
	function usercomments_delete(){	
	
		$db = JFactory::getDBO();	
		
		// Check for request forgeries 
		JRequest::checkToken() or jexit('Invalid Token');			
		
		$cid = JRequest::getVar('cid', null, 'post', 'array');	
		$user_id = JRequest::getVar('user_id', '');		
		
		userprivatepageHelper::check_if_array($cid);
		
		if (count($cid)){
			$ids = implode(',', $cid);	
			
			//delete comments			
			$query = $db->getQuery(true);
			$query->delete();
			$query->from('#__userprivatepage_comments');			
			$query->where('id IN ('.$ids.')');
			$db->setQuery((string)$query);
			$db->query();
		}
		$message = userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_COMMENTS')).' '.JText::_('COM_USERPRIVATEPAGE_DELETED');
		$this->setRedirect("index.php?option=com_userprivatepage&view=usercomments&user_id=".$user_id, $message);
	}	
		

}
?>
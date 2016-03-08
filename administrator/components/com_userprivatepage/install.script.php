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

class com_userprivatepageInstallerScript {

	function preflight($type, $parent){
	
		$db = JFactory::getDBO();
		
		//if there is a leftover of a previous install, take it out.
		//http://forum.joomla.org/viewtopic.php?f=578&t=594153		
		
		$query = $db->getQuery(true);
		$query->delete();
		$query->from('#__assets');
		$query->where('name='.$db->quote('com_userprivatepage'));
		$query->where('title='.$db->quote('com_userprivatepage'));		
		$db->setQuery($query);
		$db->query();	
		
	} 

	public function postflight($type, $parent){
		
		$db = JFactory::getDBO();			
		$app = JFactory::getApplication();			
				
		$db->setQuery("CREATE TABLE IF NOT EXISTS #__userprivatepage_config (
		  `id` int(1) NOT NULL AUTO_INCREMENT,
		  `config` text NOT NULL,
		  `text_when_empty` text NOT NULL,
		  `text_when_unpub` text NOT NULL,
		  `notifymessage` text NOT NULL,
		  `notifymessage_admin_add` text NOT NULL,
		  `notifymessage_admin_edit` text NOT NULL,
		  PRIMARY KEY (`id`)
		)");
		$db->query();			
		
		//config columns default data
		$notifymessage = '<p>Hello {username},</p>
<p>The administrator of {domain} has added a comment on your private page.</p>
<p><a href="{url_to_comment}" target="_blank">read the comment</a></p>
<p>This is an automated message.</p>';	
		$notifymessage_admin_add = '<p>Hello administrator,</p>
<p>User {username} ({name}) has added a comment on his/her private page on {domain}.</p>
<p><a href="{url_to_comment}" target="_blank">read the comment</a></p>
<p>This is an automated message.</p>';
		$notifymessage_admin_edit = '<p>Hello administrator,</p>
<p>User {username} ({name}) has edited a comment on his/her private page on {domain}.</p>
<p><a href="{url_to_comment}" target="_blank">read the comment</a></p>
<p>This is an automated message.</p>';
		$subject_add = 'User {username} ({name}) has added a comment on his/her private page on {domain}';
		$subject_edit = 'User {username} ({name}) has edited a comment on his/her private page on {domain}';
		$subject_user = 'The administrator of {domain} has added a comment on your private page';
		
		//get admin id's assuming default admin groups 7 & 8
		$notify_admins = '';
		$query = $db->getQuery(true);
		$query->select('user_id');
		$query->from('#__user_usergroup_map');
		$query->where('(group_id='.$db->q('7').' OR group_id='.$db->q('8').')');	
		$query->order('user_id');
		$rows = $db->setQuery($query);				
		$rows = $db->loadObjectList();			
		foreach($rows as $row){	
			if($notify_admins!=''){
				$notify_admins .= ',';	
			}	
			$notify_admins .= $row->user_id;	
		}		
		
		$db->setQuery("SHOW COLUMNS FROM #__userprivatepage_config ");
		$columns = $db->loadColumn();	
			
		//added in version 1.1.0		
		if(!in_array('text_when_unpub', $columns)){
			$db->setQuery("ALTER TABLE #__userprivatepage_config ADD ".$db->qn('text_when_unpub')." TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ");			
			$db->query();	
			//get text_when_empty
			$query = $db->getQuery(true);
			$query->select('text_when_empty');
			$query->from('#__userprivatepage_config');
			$query->where('id='.$db->q('1'));			
			$rows = $db->setQuery($query);				
			$rows = $db->loadObjectList();
			$text_when_empty = 'coming soon';				
			foreach($rows as $row){		
				$text_when_empty = $row->text_when_empty;	
			}	
			$query = $db->getQuery(true);
			//update text_when_unpub	
			$query->update('#__userprivatepage_config');
			$query->set('text_when_unpub='.$db->q($text_when_empty));				
			$query->where('id='.$db->q('1'));
			$db->setQuery((string)$query);
			$db->query();
		}	
		
		//added in version 1.2.0		
		if(!in_array('notifymessage', $columns)){
			//add new column
			$db->setQuery("ALTER TABLE #__userprivatepage_config ADD ".$db->qn('notifymessage')." TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD ".$db->qn('notifymessage_admin_add')." TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD ".$db->qn('notifymessage_admin_edit')." TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ");			
			$db->query();	
			//update notifymessage	
			$query = $db->getQuery(true);		
			$query->update('#__userprivatepage_config');
			$query->set('notifymessage='.$db->q($notifymessage));	
			$query->set('notifymessage_admin_add='.$db->q($notifymessage_admin_add));
			$query->set('notifymessage_admin_edit='.$db->q($notifymessage_admin_edit));				
			$query->where('id='.$db->q('1'));
			$db->setQuery((string)$query);
			$db->query();
		}		
		
		$db->setQuery("CREATE TABLE IF NOT EXISTS #__userprivatepage_pages (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `user_id` int(11) NOT NULL,
		  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
		  `text` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
		  `published` int(1) NOT NULL,
		  `show_title` int(1) NOT NULL DEFAULT '2',
		  `can_edit` int(1) NOT NULL DEFAULT '2',
		  PRIMARY KEY (`id`)
		)");
		$db->query();	
		
		$db->setQuery("SHOW COLUMNS FROM #__userprivatepage_pages ");
		$columns = $db->loadColumn();	
		
		//added in version 1.1.0		
		if(!in_array('show_title', $columns)){
			$db->setQuery("ALTER TABLE #__userprivatepage_pages ADD ".$db->qn('show_title')." int(1) NOT NULL DEFAULT '2' ");			
			$db->query();			
		}	
		
		//added in version 1.2.0	
		if(!in_array('comments', $columns)){
			$db->setQuery("ALTER TABLE #__userprivatepage_pages ADD ".$db->qn('comments')." int(1) NOT NULL DEFAULT '2' ");			
			$db->query();			
		}		

		$db->setQuery("CREATE TABLE IF NOT EXISTS #__userprivatepage_scripts (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
			`value` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
			`ordering` int(11) NOT NULL,
			PRIMARY KEY (`id`)
		)");
		$db->query();
		
		$db->setQuery("CREATE TABLE IF NOT EXISTS #__userprivatepage_usermap (
		  `id` int(11) NOT NULL auto_increment,
		  `group_id` int(11) NOT NULL,
		  `level_id` int(11) NOT NULL,
		  `level_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
		  PRIMARY KEY (`id`)
		)");
		$db->query();
		
		$db->setQuery("CREATE TABLE IF NOT EXISTS #__userprivatepage_comments (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `user_id` int(11) NOT NULL,
		  `to_user` int(1) NOT NULL,
		  `comment` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
		  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `is_read` int(1) NOT NULL DEFAULT '0',		 
		  PRIMARY KEY (`id`)
		)");
		$db->query();			
		
		//check if config is empty
		$upp_config = '';		
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__userprivatepage_config');
		$query->where('id='.$db->q('1'));		
		$rows = $db->setQuery($query, 0, 1);			
		$rows = $db->loadObjectList();
			
		foreach($rows as $row){
			$upp_config = $row->config;			
		}
		
		if($upp_config==''){	
			//no config so assuming fresh install	
		
			//fresh config
			$configuration = '{"enabled":"1"';
			$configuration .= ',"version_checker":"1"';
			$configuration .= ',"show_title":"1"';
			$configuration .= ',"access_tooltip":"0"';				
			$configuration .= ',"modal_width":"600"';
			$configuration .= ',"modal_height":"500"';
			$configuration .= ',"comments":"1"';
			$configuration .= ',"bb_quote":"true"';			
			$configuration .= ',"bb_image":"true"';
			$configuration .= ',"icons_style":""';
			$configuration .= ',"smilies":" smile wink laughing blink ermm"';	
			$configuration .= ',"comments_date_format":"Y-m-d H:i"';
			$configuration .= ',"allow_comment_edit":"until_read"';
			$configuration .= ',"allow_comment_edit_time":"10"';
			$configuration .= ',"icons_dir_default":"1"';			
			$configuration .= ',"icons_dir":""';			
			$configuration .= ',"notify_admin_mail":"1"';
			$configuration .= ',"notify_admin_messaging":""';
			$configuration .= ',"notify_admin_mail_edit":"1"';
			$configuration .= ',"notify_admin_messaging_edit":""';
			$configuration .= ',"notify_user":"1"';	
			$configuration .= ',"subject_add":"'.$subject_add.'"';	
			$configuration .= ',"subject_edit":"'.$subject_edit.'"';	
			$configuration .= ',"subject_user":"'.$subject_user.'"';
			$configuration .= ',"notify_admins":"'.$notify_admins.'"';	
			$configuration .= ',"show_when_read":"1"';	
			$configuration .= '}';						
			$text_when_empty = 'coming soon';	
			$text_when_unpublished = 'coming soon';				
	
			//insert fresh config			
			$query = $db->getQuery(true);
			$query->insert('#__userprivatepage_config');
			$query->set('id='.$db->q('1'));
			$query->set('config='.$db->q($configuration));	
			$query->set('text_when_empty='.$db->q($text_when_empty));	
			$query->set('text_when_unpub='.$db->q($text_when_unpublished));	
			$query->set('notifymessage='.$db->q($notifymessage));	
			$query->set('notifymessage_admin_add='.$db->q($notifymessage_admin_add));
			$query->set('notifymessage_admin_edit='.$db->q($notifymessage_admin_edit));			
			$db->setQuery((string)$query);
			$db->query();
				
			//insert temp row for pages import
			$query = $db->getQuery(true);
			$query->insert('#__userprivatepage_config');
			$query->set('id='.$db->q('2'));
			$query->set('config='.$db->q(''));			
			$db->setQuery((string)$query);
			$db->query();			
		
		}else{
			//there is a config
			//see if it needs updating	
			$new_config = '';
			$config_needs_updating = 0;	
			
			//added in version 1.1.0
			if(!strpos($upp_config, '"modal_width":"')){
				$new_config .= ',"modal_width":"600"';
				$new_config .= ',"modal_height":"500"';
				$config_needs_updating = 1;	
			}
			
			//added in version 1.2.0	
			if(!strpos($upp_config, '"comments":"')){
				$new_config .= ',"comments":"0"';
				$new_config .= ',"bb_quote":"true"';				
				$new_config .= ',"bb_image":"true"';
				$new_config .= ',"icons_style":""';
				$new_config .= ',"smilies":" smile wink laughing blink ermm"';	
				$new_config .= ',"comments_date_format":"Y-m-d H:i"';
				$new_config .= ',"allow_comment_edit":"0"';	
				$new_config .= ',"allow_comment_edit_time":"10"';
				$new_config .= ',"icons_dir_default":"1"';			
				$new_config .= ',"icons_dir":""';	
				$new_config .= ',"notify_admin_mail":""';
				$new_config .= ',"notify_admin_messaging":""';
				$new_config .= ',"notify_admin_mail_edit":""';
				$new_config .= ',"notify_admin_messaging_edit":""';
				$new_config .= ',"notify_user":""';	
				$new_config .= ',"subject_add":"'.$subject_add.'"';	
				$new_config .= ',"subject_edit":"'.$subject_edit.'"';	
				$new_config .= ',"subject_user":"'.$subject_user.'"';	
				$new_config .= ',"notify_admins":"'.$notify_admins.'"';	
				$new_config .= ',"show_when_read":"1"';					
				$config_needs_updating = 1;				
			}	
			
			if($config_needs_updating){
				$temp = trim($upp_config);
				$config_lenght = strlen($temp);
				$open_ending = substr($temp, 0, $config_lenght-1);				
				$updated_config = $open_ending.$new_config.'}';					
			
				$query = $db->getQuery(true);		
				$query->update('#__userprivatepage_config');
				$query->set('config='.$db->q($updated_config));						
				$query->where('id='.$db->q('1'));
				$db->setQuery((string)$query);
				$db->query();
			}		
			
		}
		
		//check if there are scripts
		$there_are_scripts = 0;
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__userprivatepage_scripts');		
		$rows = $db->setQuery($query);				
		$rows = $db->loadObjectList();
			
		foreach($rows as $row){		
			$there_are_scripts = 1;
		}
		
		//add sample scripts if fresh install
		if(!$there_are_scripts){
			$samples[] = array('[sample] hello UserName','$html [equal] \'Hello \'.$user_name;');
			$samples[] = array('[sample] first 7 days after registration','$timestamp = strtotime($user_register_date);
$extra_time = 7*24*60*60;/*7days*24hours*60min*60sec*/
$end_time = $timestamp+$extra_time;
$now = time();
if($now<$end_time){
   /*is within 7 days after registeration*/
   $html = \'display video\';
}else{
   /*is later then 7 days after registeration*/
    $html = \'sorry, you have no longer access to the video\';
}');
			$samples[] = array('[sample] link to latest article from database','/*latest article*/
$database->setQuery("SELECT id "
." FROM #__content "
." WHERE state=\'1\' "
." ORDER BY created DESC "
);
$rows = $database->loadObjectList();
foreach($rows as $row){	
   $article_id = $row->id;	
   break;
}
$html = \'<a href="index.php?option=com_content&view=article&id=\'.$article_id.\'">\';
$html .= \'latest article\';
$html .= \'</a>\';');
			for($n = 0; $n < count($samples); $n++){
				$temp_name = $samples[$n][0];
				$temp_code = $samples[$n][1];
				$query = $db->getQuery(true);
				$query->insert('#__userprivatepage_scripts');
				$query->set('name='.$db->q($temp_name));
				$query->set('value='.$db->q($temp_code));			
				$db->setQuery((string)$query);
				$db->query();
			}			
		}	
		
		//clean up deprecated files from previous install
		$deprecated_files = array();
		//$deprecated_files[] = JPATH_ROOT.'/administrator/components/com_userprivatepage/models/config.php';		
		$latest_version_css = 2;
		for($n = 1; $n < $latest_version_css; $n++){			
			$deprecated_files[] = JPATH_ROOT.'/administrator/components/com_userprivatepage/css/userprivatepage'.$n.'.css';
		}		
		foreach($deprecated_files as $deprecated_file){
			if(file_exists($deprecated_file)){
				JFile::delete($deprecated_file);
			}
		}		
		
		//fix extension update url
		$update_url = '';			
		$xml_file = JPATH_SITE.'/administrator/components/com_userprivatepage/userprivatepage.xml';
		$version = new JVersion;
		if($version->RELEASE < '3.0'){		
			$xml = JFactory::getXML($xml_file, true);		
		}else{
			$xml = simplexml_load_file($xml_file);
		}
		foreach($xml->children() as $updateservers){			
			foreach($updateservers->children() as $updateserver){				
				$update_url = $updateserver;
			}
		}
		if($update_url){
			$query = $db->getQuery(true);		
			$query->update('#__update_sites');
			$query->set('location='.$db->q($update_url));					
			$query->where('name='.$db->q('com_userprivatepage'));
			$db->setQuery((string)$query);
			$db->query();
		}
				
		//reset version checker session var		
		$app->setUserState( "com_userprivatepage.latest_version_message", '' );		
		
		$this->display_install_page();			
	}		
	
	public function uninstall($installer){
		
		$db = JFactory::getDBO();
		
		//delete tables
		$tables_to_drop = array();			
		$tables_to_drop[] = '#__userprivatepage_comments';
		$tables_to_drop[] = '#__userprivatepage_config';	
		$tables_to_drop[] = '#__userprivatepage_pages';	
		$tables_to_drop[] = '#__userprivatepage_scripts';		
		$tables_to_drop[] = '#__userprivatepage_usermap';		
		for($n = 0; $n < count($tables_to_drop); $n++){
			$query = $db->getQuery(true);
			$query = 'DROP TABLE IF EXISTS '.$db->quoteName($tables_to_drop[$n]);
			$db->setQuery((string)$query);
			$db->query();
		}		
		
		$this->display_uninstall_page();
		
    }
	
	function display_install_page(){
		?>
<div style="width: 1000px; text-align: left; background: url(components/com_userprivatepage/images/userprivatepage_toolbar.png) 10px 0 no-repeat;">
	<h2 style="padding: 10px 0 10px 70px;">User Private Page</h2>	
	<div style="width: 1000px; overflow: hidden;">
		<div style="width: 270px; float: left;">
			<p>
				Thank you for using User-Private-Page.		
			</p>
			<p>
				<input type="button" value="Go to User-Private-Page" onclick="document.location.href='index.php?option=com_userprivatepage';" />				
			</p>
		</div>
		<div style="width: 380px; float: left;">
			<p>
				With User-Private-Page you can create one page per user. You can link to the page with a menu-item (set access to 'registered') which will link to the users own page. No one else has access to that page.	
			</p>			
		</div>
		<div style="width: 330px; float: left;">
			<p>
				Check <a href="http://www.pages-and-items.com" target="_blank">www.pages-and-items.com</a> for:
			<ul>
				<li><a href="http://www.pages-and-items.com/extensions/user-private-page" target="_blank">updates</a></li>
				<li><a href="http://www.pages-and-items.com/extensions/user-private-page/faqs" target="_blank">FAQs</a></li>	
				<li><a href="http://www.pages-and-items.com/forum/46-user-private-page" target="_blank">support forum</a></li>	
				<li><a href="http://www.pages-and-items.com/my-account/email-update-notifications" target="_blank">email notification service for updates and new extensions</a></li>	
				<li><a href="http://www.pages-and-items.com/extensions/user-private-page/update-notifications-for-user-private-page" target="_blank">subscribe to RSS feed update notifications</a></li>	
			</ul>
			</p>
			<p>
				Follow us on <a href="http://twitter.com/PagesAndItems" target="_blank">twitter</a> (only update notifications).		
			</p>
		</div>
	</div>	
</div>
		<?php
	}
	
	function display_uninstall_page(){
		?>
<div style="width: 500px; text-align: left;">
	<h2 style="padding-left: 10px;">User-Private-Page</h2>	
	<p>
		Thank you for having used User-Private-Page.
	</p>
	<p>
		Why did you uninstall User-Private-Page? Missing any features? <a href="http://www.pages-and-items.com/contact" target="_blank">Let us know</a>.		
	</p>	
	<p>
		Check <a href="http://www.pages-and-items.com/" target="_blank">www.pages-and-items.com</a> for:
		<ul>
			<li><a href="http://www.pages-and-items.com/extensions/user-private-page" target="_blank">updates</a></li>
			<li><a href="http://www.pages-and-items.com/extensions/user-private-page/faqs" target="_blank">FAQs</a></li>	
			<li><a href="http://www.pages-and-items.com/forum/46-user-private-page" target="_blank">support forum</a></li>	
			<li><a href="http://www.pages-and-items.com/my-account/email-update-notifications" target="_blank">email notification service for updates and new extensions</a></li>	
			<li><a href="http://www.pages-and-items.com/extensions/user-private-page/update-notifications-for-user-private-page" target="_blank">subscribe to RSS feed update notifications</a></li>			
		</ul>
	</p>	
</div>
		<?php
	}
	
	
}

?>

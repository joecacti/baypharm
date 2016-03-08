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

$checked = 'checked="checked"';

?>
<script language="JavaScript" type="text/javascript">

Joomla.submitbutton = function(task){	
	if(task=='cancel'){			
		document.location.href = 'index.php?option=com_userprivatepage&view=userspages';		
	}else{				
		if(task=='config_apply'){	
			document.adminForm.apply.value = '1';
		}		
		submitform('config_save');
	}
}

function check_latest_version(){
	document.getElementById('version_checker_target').innerHTML = document.getElementById('version_checker_spinner').innerHTML;
	ajax_url = 'index.php?option=com_userprivatepage&task=ajax_version_checker&format=raw';
	var req = new Request.HTML({url:ajax_url, update:'version_checker_target' });	
	req.send();
}

function do_tab_session(id, href){	
	pos_tabname = href.indexOf("#");
	href_length = href.length;
	tabname = href.substring(pos_tabname+1, href_length);	
	var JNC_jQuery = jQuery.noConflict();	
	ajax_url = "index.php?option=com_userprivatepage&task=tab_session_save&format=raw&id="+id+"&active="+tabname;		
	JNC_jQuery.ajax({	  
		url: ajax_url	   
	});	
}

</script>
<form action="" method="post" name="adminForm" id="adminForm">
	<?php 
	echo userprivatepageHelper::start_sidebar($this->sidebar);
	$tabs = array('options', 'defaulttexts', 'comments');//compatibility with j2.5
	userprivatepageHelper::tab_set_start('upp_config', 'options', 1, $tabs); 
	userprivatepageHelper::tab_add('upp_config', 'options', JText::_('JOPTIONS')); 
	?>
	<fieldset class="adminform pi_wrapper_nice">	
		<legend class="pi_legend"><?php echo JText::_('JSTATUS'); ?></legend>			
		<table class="adminlist pi_table">				
			<tr>
				<td class="pi_nowrap" style="width: 250px;">
					<label class="hasTip required"><?php echo JText::_('JSTATUS'); ?> User-Private-Page</label>
				</td>
				<td>
					<label>
						<input type="radio" name="enabled" value="1" <?php if(userprivatepageHelper::config('enabled')){echo $checked;}?> />
						<?php echo userprivatepageHelper::low(JText::_('JENABLED')); ?>
					</label>	
					<br /><br />
					<label>			
						<input type="radio" name="enabled" value="0" <?php if(!userprivatepageHelper::config('enabled')){echo $checked;}?> />
						<?php echo userprivatepageHelper::low(JText::_('JDISABLED')); ?>
					</label>
				</td>									
			</tr>								
		</table>				
	</fieldset>			
	<fieldset class="adminform pi_wrapper_nice">
		<legend class="pi_legend"><?php echo JText::_('COM_TEMPLATES_HEADING_STYLE'); ?></legend>
		<table class="adminlist pi_table">			
			<tr>		
				<td class="pi_tdpadbotnone" style="width: 250px;">
					<?php echo JText::_('COM_USERS_USERS_MULTIPLE_GROUPS').' / '.JText::_('COM_USERPRIVATEPAGE_LEVELS').' '.userprivatepageHelper::low(JText::_('COM_TEMPLATES_HEADING_STYLE')); ?>
				</td>
				<td class="pi_tdpadbotnone" style="width: 250px;">
					<label style="white-space: nowrap;"><input type="radio" name="access_tooltip" id="access_tooltip_no" value="0" class="radio" <?php if(!userprivatepageHelper::config('access_tooltip')){echo $checked;} ?> /> <?php echo userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_WITHOUT')); ?> tooltip</label>											
				</td>
				<td class="pi_tdpadbotnone">
					<label for="access_tooltip_no"><img src="components/com_userprivatepage/images/accesstooltip_no.png" class="pi_imgborder" alt="without tooltip" /></label>
				</td>
			</tr>
			<tr>		
				<td>					
				</td>
				<td>
					<label style="white-space: nowrap;"><input type="radio" name="access_tooltip" id="access_tooltip_yes" value="1" class="radio" <?php if(userprivatepageHelper::config('access_tooltip')){echo $checked;} ?> /> <?php echo userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_WITH')); ?> tooltip</label>
				</td>
				<td>
					<label for="access_tooltip_yes"><img src="components/com_userprivatepage/images/accesstooltip_yes.png" class="pi_imgborder" alt="with tooltip" /></label>					
				</td>
			</tr>
			<tr>		
				<td>
					<?php 
						echo JText::_('JBROWSERTARGET_MODAL');
						echo ' '.userprivatepageHelper::low(JText::_('COM_CACHE_SIZE'));
						echo ' '.userprivatepageHelper::low(JText::_('JGLOBAL_PREVIEW'));
					?>					
				</td>
				<td>
					<?php echo userprivatepageHelper::low(JText::_('JGLOBAL_WIDTH')); ?>
					<input name="modal_width" value="<?php echo userprivatepageHelper::config('modal_width'); ?>" style="width: 30px; text-align: right;" />px 
					&nbsp;<?php echo userprivatepageHelper::low(JText::_('COM_WRAPPER_FIELD_HEIGHT_LABEL')); ?>
					<input name="modal_height" value="<?php echo userprivatepageHelper::config('modal_height'); ?>" style="width: 30px; text-align: right;" />px
				</td>
				<td>									
				</td>
			</tr>
		</table>				
	</fieldset>
	<fieldset class="adminform pi_wrapper_nice">
		<legend class="pi_legend"><?php echo JText::_('JVERSION'); ?></legend>
		<table class="adminlist pi_table">	
			<tr>		
				<td class="pi_nowrap" style="width: 250px;">
					<?php echo JText::_('JVERSION'); ?>	
				</td>
				<td style="width: 250px;">
					<?php echo $this->controller->upp_version.' ('.$this->controller->get_version_type().' '.userprivatepageHelper::low(JText::_('JVERSION')).')'; ?>
				</td>
				<td>
					<input type="button" value="<?php echo JText::_('COM_USERPRIVATEPAGE_CHECK_LATEST_VERSION'); ?>" onclick="check_latest_version();" />					
					<div id="version_checker_target"></div>	
					<span id="version_checker_spinner"><img src="components/com_userprivatepage/images/processing.gif" alt="processing" /></span>				
				</td>
			</tr>	
			<tr>		
				<td>
					<?php echo JText::_('COM_USERPRIVATEPAGE_VERSION_CHECKER'); ?>	
				</td>
				<td>
					<label><input type="checkbox" class="checkbox" name="version_checker" value="1" <?php if(userprivatepageHelper::config('version_checker')){echo 'checked="checked"';} ?> /> <?php echo userprivatepageHelper::low(JText::_('JENABLED')); ?></label>
				</td>
				<td>
					<?php 
						echo JText::_('COM_USERPRIVATEPAGE_VERSION_CHECKER_INFO_A').' ';
						echo 'User-Private-Page ';
						echo JText::_('COM_USERPRIVATEPAGE_VERSION_CHECKER_INFO_B');
					?>.				
				</td>
			</tr>			
		</table>
	</fieldset>
	<?php 
	userprivatepageHelper::tab_end(); 	
	$label = JText::_('JDEFAULT').' '.JText::_('COM_USERPRIVATEPAGE_TEXTS');
	userprivatepageHelper::tab_add('upp_config', 'defaulttexts', $label); 
	?>
	<fieldset class="adminform pi_wrapper_nice">	
		<legend class="pi_legend"><?php echo $label; ?></legend>						
		<table class="adminlist pi_table">				
			<tr>
				<td style="width: 250px;">
					<?php 
						echo JText::_('JGLOBAL_SHOW_TITLE_LABEL');
					?>
				</td>
				<td class="rol_nowrap" style="width: 550px;">
					<label>
						<input type="radio" name="show_title" value="1" class="radio" <?php if(userprivatepageHelper::config('show_title')){echo $checked;} ?> />
						<?php echo JText::_('JYES'); ?>
					</label>
					<label>
						<input type="radio" name="show_title" value="0" class="radio" <?php if(!userprivatepageHelper::config('show_title')){echo $checked;} ?> />
						<?php echo JText::_('JNO'); ?>
					</label>
				</td>				
				<td>
				</td>					
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_USERPRIVATEPAGE_TEXT_WHEN_EMPTY'); ?>					
				</td>
				<td>						
					<?php 
						echo JText::_('COM_USERPRIVATEPAGE_TEXT_WHEN_EMPTY_B');
						$editor = JFactory::getEditor();						
						echo $editor->display('text_when_empty', $this->text_when_empty, '100%', '100px', 60, 5, true);						
					?>	
				</td>
				<td>
					<?php echo JText::_('COM_USERPRIVATEPAGE_TAGS').' '.JText::_('COM_USERPRIVATEPAGE_INTEXT'); ?>:
					<br />
					{username}
					<br />
					{name}
					<br />
					{email}
					<br />
					{registerDate d-m-Y h:i:s}
					<br />
					{lastvisitDate d-m-Y h:i:s}
					<br />
					{user_id}
					<br />
					{script 2}
					<br />					
					<?php 											
						if($this->controller->get_version_type()=='free'){			
							echo '<span class="pi_warning">';			
							echo JText::_('COM_USERPRIVATEPAGE_FREETAGS');
							echo '</span><br />';
						}
						echo userprivatepageHelper::low(JText::_('JALL'));
						echo ' '.userprivatepageHelper::low(JText::_('JGLOBAL_FIELDSET_CONTENT'));
						echo ' '.userprivatepageHelper::low(JText::_('COM_INSTALLER_TYPE_PLUGIN'));
						echo ' '.JText::_('COM_USERPRIVATEPAGE_CODES');
					?>
				</td>									
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_USERPRIVATEPAGE_TEXT_WHEN_UNPUBLISHED'); ?>					
				</td>
				<td>						
					<?php 
						echo JText::_('COM_USERPRIVATEPAGE_TEXT_WHEN_UNPUBLISHED_B');											
						echo $editor->display('text_when_unpublished', $this->text_when_unpublished, '100%', '100px', 60, 5, true);						
					?>	
				</td>
				<td>
					<?php echo JText::_('COM_USERPRIVATEPAGE_TAGS').' '.JText::_('COM_USERPRIVATEPAGE_INTEXT'); ?>:
					<br />
					{username}
					<br />
					{name}
					<br />
					{email}
					<br />
					{registerDate d-m-Y h:i:s}
					<br />
					{lastvisitDate d-m-Y h:i:s}
					<br />
					{user_id}
					<br />
					{script 2}
					<br />					
					<?php 											
						if($this->controller->get_version_type()=='free'){			
							echo '<span class="pi_warning">';			
							echo JText::_('COM_USERPRIVATEPAGE_FREETAGS');
							echo '</span><br />';
						}
						echo userprivatepageHelper::low(JText::_('JALL'));
						echo ' '.userprivatepageHelper::low(JText::_('JGLOBAL_FIELDSET_CONTENT'));
						echo ' '.userprivatepageHelper::low(JText::_('COM_INSTALLER_TYPE_PLUGIN'));
						echo ' '.JText::_('COM_USERPRIVATEPAGE_CODES');
					?>
				</td>									
			</tr>
		</table>				
	</fieldset>	
	<?php 
	userprivatepageHelper::tab_end(); 	
	$label = JText::_('COM_USERPRIVATEPAGE_COMMENTS');
	userprivatepageHelper::tab_add('upp_config', 'comments', $label); 
	?>
	<fieldset class="adminform pi_wrapper_nice">	
		<legend class="pi_legend"><?php echo $label; ?></legend>			
		<table class="adminlist pi_table">				
			<tr>		
				<td style="width: 250px;">
					<?php echo JText::_('JSTATUS').' '.$label; ?>
				</td>
				<td style="width: 250px;">					
					<label style="white-space: nowrap;">
						<input type="radio" name="comments" value="1" <?php if(userprivatepageHelper::config('comments')){echo $checked;}?> />
						<?php echo userprivatepageHelper::low(JText::_('JENABLED')); ?>
					</label>
					<br />
					<label style="white-space: nowrap;">		
						<input type="radio" name="comments" value="0" <?php if(!userprivatepageHelper::config('comments')){echo $checked;}?> />
						<?php echo userprivatepageHelper::low(JText::_('JDISABLED')); ?>
					</label>							
				</td>
				<td>
					<img src="components/com_userprivatepage/images/comments.png" class="pi_imgborder" alt="with comments" />
				</td>
			</tr>
			<tr>		
				<td>
					BB <?php echo JText::_('COM_USERPRIVATEPAGE_CODES'); ?>					
				</td>
				<td colspan="2">					
					<label style="white-space: nowrap;">		
						<input type="checkbox" class="checkbox" name="bb_quote" value="true" <?php if(userprivatepageHelper::config('bb_quote')){echo $checked;}?> />
						[quote][/quote]
					</label>					
					<br />
					<label style="white-space: nowrap;">		
						<input type="checkbox" class="checkbox" name="bb_image" value="true" <?php if(userprivatepageHelper::config('bb_image')){echo $checked;}?> />
						[image][/image]
					</label>
					<br />
					<?php 
					$smilies = userprivatepageHelper::get_smilies();
					for($n = 0; $n < count($smilies); $n++){						
					?>						
						<label>		
							<input type="checkbox" class="checkbox" name="smilies_<?php echo $smilies[$n][0]; ?>" value="true" <?php if(strpos(userprivatepageHelper::config('smilies'), $smilies[$n][0])){echo $checked;}?> />
							<img src="../components/com_userprivatepage/images/<?php echo $smilies[$n][0]; ?><?php echo userprivatepageHelper::config('icons_style'); ?>.png" alt="<?php echo $smilies[$n][0]; ?>" title="<?php echo $smilies[$n][0]; ?>" /> 
						</label>
					<?php 
					}
					?>
					
				</td>				
			</tr>	
			<tr>		
				<td>
					<?php echo JText::_('COM_CONTACT_FIELD_VALUE_ICONS').' '.userprivatepageHelper::low(JText::_('COM_TEMPLATES_HEADING_STYLE')); ?>					
				</td>
				<td>					
					<label style="white-space: nowrap;">
						<input type="radio" name="icons_style" value="" <?php if(!userprivatepageHelper::config('icons_style')){echo $checked;}?> />
						<img src="../components/com_userprivatepage/images/bb_image.gif" alt="color" title="color" /> 
						<img src="../components/com_userprivatepage/images/smile.png" alt="color" title="color" />
					</label>
					<br />
					<label style="white-space: nowrap;">		
						<input type="radio" name="icons_style" value="-grey" <?php if(userprivatepageHelper::config('icons_style')){echo $checked;}?> />
						<img src="../components/com_userprivatepage/images/bb_image-grey.gif" alt="grey" title="grey" /> 
						<img src="../components/com_userprivatepage/images/smile-grey.png" alt="grey" title="grey" />
					</label>
				</td>
				<td>
					
				</td>
			</tr>	
			<tr>		
				<td>
					<?php echo JText::_('COM_CONTACT_FIELD_VALUE_ICONS').' '.userprivatepageHelper::low(JText::_('COM_ADMIN_DIRECTORY')); ?>					
				</td>
				<td>					
					<label style="white-space: nowrap;">
						<?php 
							$default_dir = 'components/com_userprivatepage/images/';
						?>
						<input type="radio" name="icons_dir_default" value="1" <?php if(userprivatepageHelper::config('icons_dir_default')){echo $checked;}?> />
						<?php echo $default_dir; ?>
						
					</label>
					<br />
					<label style="white-space: nowrap;">		
						<input type="radio" name="icons_dir_default" value="" <?php if(!userprivatepageHelper::config('icons_dir_default')){echo $checked;}?> />
						<input type="text" name="icons_dir" value="<?php echo str_replace('"', '&quot;', userprivatepageHelper::config('icons_dir')); ?>" style="width: 250px;" />
					</label>
				</td>
				<td>
					(<?php echo userprivatepageHelper::low(JText::_('JDEFAULT')); ?>)					
				</td>
			</tr>
			<tr>		
				<td>
					<?php echo JText::_('JDATE').' '.JText::_('COM_USERPRIVATEPAGE_FORMAT'); ?>					
				</td>
				<td>					
					<input type="text" name="comments_date_format" value="<?php echo str_replace('"', '&quot;', userprivatepageHelper::config('comments_date_format')); ?>" />
				</td>
				<td>
					<a href="http://php.net/manual/en/datetime.createfromformat.php#refsect1-datetime.createfromformat-parameters" target="_blank">?</a>
				</td>
			</tr>
			<tr>		
				<td>
					<?php echo JText::_('COM_USERPRIVATEPAGE_WHEN_READ'); ?>					
				</td>
				<td>					
					<label style="white-space: nowrap;">		
						<input type="checkbox" class="checkbox" name="show_when_read" value="true" <?php if(userprivatepageHelper::config('show_when_read')){echo $checked;}?> />
						<?php echo JText::_('COM_USERPRIVATEPAGE_WHEN_READ_B'); ?>
					</label>	
				</td>
				<td>
					<img src="components/com_userprivatepage/images/show-if-read.png" class="pi_imgborder" alt="show the user if the comment has been read by the administrator" />
				</td>
			</tr>
			<tr>		
				<td>
					<?php 
						echo JText::_('COM_USERS_USER_HEADING');
						echo ' '.userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_COMMENT'));
						echo ' '.userprivatepageHelper::low(JText::_('JACTION_EDIT'));
					?>					
				</td>
				<td>
					<label style="white-space: nowrap;">
						<input type="radio" name="allow_comment_edit" value="" <?php if(!userprivatepageHelper::config('allow_comment_edit')){echo $checked;}?> />
						<?php echo userprivatepageHelper::low(JText::_('JNO')); ?>
					</label>
					<br />					
					<label style="white-space: nowrap;">
						<input type="radio" name="allow_comment_edit" value="1" <?php if(userprivatepageHelper::config('allow_comment_edit')=='1'){echo $checked;}?> />
						<?php echo userprivatepageHelper::low(JText::_('JYES')); ?>
					</label>
					<br />					
					<label style="white-space: nowrap;">
						<input type="radio" name="allow_comment_edit" value="until_read" <?php if(userprivatepageHelper::config('allow_comment_edit')=='until_read'){echo $checked;}?> />
						<?php echo userprivatepageHelper::low(JText::_('JYES')).' '.JText::_('COM_USERPRIVATEPAGE_UNTIL_READ'); ?>
					</label>
					<br />					
					<label style="white-space: nowrap;">
						<input type="radio" name="allow_comment_edit" value="time" <?php if(userprivatepageHelper::config('allow_comment_edit')=='time'){echo $checked;}?> />
						<?php echo userprivatepageHelper::low(JText::_('JYES')).' '.JText::_('COM_USERPRIVATEPAGE_WITHIN'); ?>
						<input type="text" name="allow_comment_edit_time" value="<?php echo userprivatepageHelper::config('allow_comment_edit_time'); ?>" style="width: 30px; text-align: right;" /> min
					</label>
				</td>
				<td>					
				</td>
			</tr>	
			<tr>		
				<td>
					<?php 
						echo JText::_('COM_USERPRIVATEPAGE_NOTIFICATIONS');						
					?>					
				</td>
				<td colspan="2">
					<label>		
							<input type="checkbox" class="checkbox" name="notify_user" value="true" <?php if(userprivatepageHelper::config('notify_user')){echo $checked;}?> />
							<?php echo JText::_('COM_USERPRIVATEPAGE_NOTIFY_USER_MAIL'); ?>:
					</label>
					<br />							
					<label style="white-space: nowrap;">
						<?php echo userprivatepageHelper::low(JText::_('COM_USERS_MAIL_FIELD_SUBJECT_LABEL')); ?>: 
						<input type="text" name="subject_user" value="<?php echo str_replace('"', '&quot;', userprivatepageHelper::config('subject_user')); ?>" style="width: 100%;" />
					</label>
					<br />
					<table style="margin-top: 3px;">
						<tr>							
							<td>
								<?php echo $editor->display('notifymessage', $this->notifymessage, '100%', '100px', 60, 5, true); ?>
							</td>
							<td>
								<?php echo JText::_('COM_USERPRIVATEPAGE_TAGS').' '.JText::_('COM_USERPRIVATEPAGE_INTEXT'); ?>:
								<br />
								{username}
								<br />
								{name}
								<br />
								{email}
								<br />
								{registerDate d-m-Y h:i:s}
								<br />
								{lastvisitDate d-m-Y h:i:s}
								<br />
								{user_id}
								<br />
								{script 2}
								<br />	
								{url_to_private_page}
								<br />	
								{url_to_comment}
								<br />
								{domain} 
							</td>
						</tr>					
					</table>
					<br />
					<label>		
							<input type="checkbox" class="checkbox" name="notify_admin_mail" value="true" <?php if(userprivatepageHelper::config('notify_admin_mail')){echo $checked;}?> />
							<?php echo JText::_('COM_USERPRIVATEPAGE_NOTIFY_ADMIN_MAIL'); ?>
					</label>
					<br />
					<label>		
							<input type="checkbox" class="checkbox" name="notify_admin_messaging" value="true" <?php if(userprivatepageHelper::config('notify_admin_messaging')){echo $checked;}?> />
							<?php echo JText::_('COM_USERPRIVATEPAGE_NOTIFY_ADMIN_MESSAGING'); ?>
					</label>
					<br />
					<label style="white-space: nowrap;">
						<?php echo userprivatepageHelper::low(JText::_('COM_USERS_MAIL_FIELD_SUBJECT_LABEL')); ?>: 
						<input type="text" name="subject_add" value="<?php echo str_replace('"', '&quot;', userprivatepageHelper::config('subject_add')); ?>" style="width: 100%;" />
					</label>							
					<br />
					<table style="margin-top: 3px;">
						<tr>							
							<td>
								<?php echo $editor->display('notifymessage_admin_add', $this->notifymessage_admin_add, '100%', '100px', 60, 5, true); ?>
							</td>
							<td>
								<?php echo JText::_('COM_USERPRIVATEPAGE_TAGS').' '.JText::_('COM_USERPRIVATEPAGE_INTEXT'); ?>:
								<br />
								{username}
								<br />
								{name}
								<br />
								{email}
								<br />
								{registerDate d-m-Y h:i:s}
								<br />
								{lastvisitDate d-m-Y h:i:s}
								<br />
								{user_id}
								<br />
								{script 2}
								<br />	
								{url_to_private_page}
								<br />	
								{url_to_comment}
								<br />
								{domain}
							</td>
						</tr>					
					</table>
					<br />
					<label>		
							<input type="checkbox" class="checkbox" name="notify_admin_mail_edit" value="true" <?php if(userprivatepageHelper::config('notify_admin_mail_edit')){echo $checked;}?> />
							<?php echo JText::_('COM_USERPRIVATEPAGE_NOTIFY_ADMIN_MAIL_EDIT'); ?>
					</label>
					<br />
					<label>		
							<input type="checkbox" class="checkbox" name="notify_admin_messaging_edit" value="true" <?php if(userprivatepageHelper::config('notify_admin_messaging_edit')){echo $checked;}?> />
							<?php echo JText::_('COM_USERPRIVATEPAGE_NOTIFY_ADMIN_MESSAGING_EDIT'); ?>
					</label>
					<br />
					<label style="white-space: nowrap;">
						<?php echo userprivatepageHelper::low(JText::_('COM_USERS_MAIL_FIELD_SUBJECT_LABEL')); ?>: 
						<input type="text" name="subject_edit" value="<?php echo str_replace('"', '&quot;', userprivatepageHelper::config('subject_edit')); ?>" style="width: 100%;" />
					</label>
					<br />
					<table style="margin-top: 3px;">
						<tr>							
							<td>
								<?php echo $editor->display('notifymessage_admin_edit', $this->notifymessage_admin_edit, '100%', '100px', 60, 5, true); ?>
							</td>
							<td>
								<?php echo JText::_('COM_USERPRIVATEPAGE_TAGS').' '.JText::_('COM_USERPRIVATEPAGE_INTEXT'); ?>:
								<br />
								{username}
								<br />
								{name}
								<br />
								{email}
								<br />
								{registerDate d-m-Y h:i:s}
								<br />
								{lastvisitDate d-m-Y h:i:s}
								<br />
								{user_id}
								<br />
								{script 2}
								<br />	
								{url_to_private_page}
								<br />	
								{url_to_comment}
								<br />
								{domain}
							</td>
						</tr>					
					</table>
					<br />
					<?php echo JText::_('COM_USERPRIVATEPAGE_NOTIFY_ADMINS'); ?>:
					<br />
					<input type="text" name="notify_admins" value="<?php echo str_replace('"', '&quot;', userprivatepageHelper::config('notify_admins')); ?>" />
					<span style="color: #999;">(<?php echo JText::_('COM_USERPRIVATEPAGE_EXAMPLE')?>: 24 <?php echo JText::_('COM_USERPRIVATEPAGE_OR'); ?> 24,56)</span>
				</td>				
			</tr>												
		</table>				
	</fieldset>	
	<?php 
	userprivatepageHelper::tab_end(); 
	userprivatepageHelper::tab_set_end(); 	
	echo userprivatepageHelper::end_sidebar($this->sidebar);
	?>		
	<input type="hidden" name="task" value="" />	
	<input type="hidden" name="apply" value="" />	
	<?php echo JHtml::_('form.token'); ?>	
</form>

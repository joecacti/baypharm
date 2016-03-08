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

echo '
<style>
body textarea{
	font-size: 13px;
}
</style>
';
?>
<script language="Javascript" type="text/javascript" src="components/com_userprivatepage/edit_area/edit_area_full.js"></script>
<script language="JavaScript" type="text/javascript">

Joomla.submitbutton = function(task){		
	if (task=='cancel'){			
		document.location.href = 'index.php?option=com_userprivatepage&view=scripts';		
	} else {
		if (document.getElementById('script_name').value == '') {			
			alert('<?php echo addslashes(JText::_('JNO').' '.JText::_('COM_USERPRIVATEPAGE_NAME')); ?>');
			return;
		}
		document.getElementById('script_code').value = editAreaLoader.getValue('script_code');
		if (document.getElementById('script_code').value == '') {			
			alert('<?php echo addslashes(JText::_('JNO').' '.JText::_('COM_USERPRIVATEPAGE_SCRIPT')); ?>');
			return;
		}
		if (task=='script_apply'){	
			document.adminForm.apply.value = '1';
		}		
		submitform('script_save');
	}	
}

editAreaLoader.init({
	id: "script_code"	// id of the textarea to transform		
	,start_highlight: true	// if start with highlight
	,allow_resize: "both"
	,allow_toggle: true
	,word_wrap: true
	,language: "en"
	,syntax: "php"	
	,toolbar: "select_font,word_wrap"
	,display: "later"
});

</script>
<form action="" method="post" name="adminForm" id="adminForm">
	<?php 
	echo userprivatepageHelper::start_sidebar($this->sidebar);
	?>		
	<h2 style="padding-left: 10px;"><?php echo JText::_('COM_USERPRIVATEPAGE_SCRIPT').': '.$this->script->name; ?></h2>				
	<fieldset class="adminform pi_wrapper_nice">										
		<table class="adminlist pi_table tabletop">							
			<tr>
				<td class="pi_nowrap" style="width: 250px;">
					<?php
						echo JText::_('COM_USERPRIVATEPAGE_NAME');
					?> *										
				</td>
				<td style="width: 150px;">
					<input type="text" name="script_name" id="script_name" style="width: 450px;" value="<?php echo str_replace('"', '&quot;', $this->script->name);?>" />
				</td>
				<td>&nbsp;
																	
				</td>
			</tr>	
			<tr>
				<td class="pi_nowrap">
					<br />
					<?php
						echo JText::_('COM_USERPRIVATEPAGE_SCRIPT');
					?> *					
				</td>
				<td>
					&lt;?php<br />
					<textarea name="script_code" id="script_code" style="width: 450px;" rows="20" cols="60"><?php echo $this->script->value; ?></textarea>
					<br />
					?&gt;
					<br />					
					<img src="components/com_userprivatepage/images/comment.gif" title="do only use /**/ to comment code" />
					<br />
				</td>
				<td>
					<br />
					<?php
						echo JText::_('COM_USERPRIVATEPAGE_CODE_INFO_A').' $html.<br /><br />';
						echo JText::_('COM_USERPRIVATEPAGE_CODE_INFO_C').'.<br /><br />';
						echo JText::_('COM_USERPRIVATEPAGE_RUNSCRIPT').':<br />';
						
						echo '<input type="text" value="{script ';
						$end = '}" />';
						if($this->script->id){
							echo $this->script->id.$end;
						}else{
							echo '888'.$end;
							echo ' <span style="color: #999;">'.JText::_('COM_USERPRIVATEPAGE_WHEREID').'</span>';
						}						
						echo '<br /><br />';
						echo JText::_('COM_USERPRIVATEPAGE_CODE_INFO_B').':<br />';
						echo '$user_id<br />';
						echo '$user_name<br />';
						echo '$user_username<br />';
						echo '$user_register_date<br />';
						echo '$user_lastvisit_date<br />';
						echo '$user_usergroups <span style="color: #999;">(array)</span><br />';
						echo '$user_accesslevels <span style="color: #999;">(array)</span><br />';
						echo '$database<br />';
						echo '$ip';
						/*<br />$country_code <span style="color: #999;">'.userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_EXAMPLE')).' NL UK US iso-codes</span><br />$language <span style="color: #999;">'.userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_EXAMPLE')).' en-GB nl-NL iso-codes</span><br />$template <span style="color: #999;">'.JText::_('COM_INSTALLER_TYPE_TYPE_TEMPLATE').'</span><br />$device <span style="color: #999;">\'mobile\', \'tablet\', \'desktop\', \'bot\'</span>';
						*/
					?>						
				</td>
			</tr>				
			<tr>
				<td class="pi_nowrap" style="width: 250px;">
					<?php
						echo JText::_('COM_USERPRIVATEPAGE_EXAMPLE');
					?>
					 1
				</td>
				<td>
					<textarea name="example_1" style="width: 450px;" rows="1" cols="60">
$html = 'Hello '.$username;</textarea>
					<br />						
				</td>
				<td>
					Hello UserName											
				</td>
			</tr>	
			<tr>
				<td class="pi_nowrap" style="width: 250px;">
					<?php
						echo JText::_('COM_USERPRIVATEPAGE_EXAMPLE');
					?>
					 2
				</td>
				<td>
					<textarea name="example_2" style="width: 450px;" rows="8" cols="60">
$timestamp = strtotime($user_register_date);
$extra_time = 7*24*60*60;//7days*24hours*60min*60sec
$end_time = $timestamp+$extra_time;
$now = time();
if($now<$end_time){
   //is within 7 days after registeration
   $html = 'display video';
}else{
   //is later then 7 days after registeration
    $html = 'sorry, you have no longer access to the video';
}</textarea>
					<br />						
				</td>
				<td>
					First 7 days after registration											
				</td>
			</tr>
			<tr>
				<td class="pi_nowrap" style="width: 250px;">
					<?php
						echo JText::_('COM_USERPRIVATEPAGE_EXAMPLE');
					?>
					 3
				</td>
				<td>
					<textarea name="example_3" style="width: 450px;" rows="8" cols="60">
//latest article
$database->setQuery("SELECT id "
." FROM #__content "
." WHERE state='1' "
." ORDER BY created DESC "
);
$rows = $database->loadObjectList();
foreach($rows as $row){	
   $article_id = $row->id;	
   break;
}
$html = '<a href="index.php?option=com_content&view=article&id='.$article_id.'">';
$html .= 'latest article';
$html .= '</a>';</textarea>
					<br />						
				</td>
				<td>
					Link to latest article from database											
				</td>
			</tr>	
			<tr>
				<td class="pi_nowrap" style="width: 250px;">
					<?php
						echo JText::_('COM_USERPRIVATEPAGE_EXAMPLE');
					?>
					 4
				</td>
				<td>
					<textarea name="example_4" style="width: 450px;" rows="8" cols="60">
$hours_offset = "-2";//set your time zone here
$hour = date("G", time()-($hours_offset*60*60));
if((9 < $hour) && ($hour < 17)){
   //office hours between 9 and 5
   $html = 'You can phone us now';
}else{   
   $html = 'You can phone us tomorrow between 9-17';
}</textarea>
					<br />						
				</td>
				<td>
					<?php
						echo JText::_('COM_USERPRIVATEPAGE_TIMEBASED').' '.userprivatepageHelper::low(JText::_('JSHOW')).' / '.userprivatepageHelper::low(JText::_('JHIDE')).'.';							
					?>											
				</td>
			</tr>				
		</table>				
	</fieldset>
	<?php 
	echo userprivatepageHelper::end_sidebar($this->sidebar);
	?>	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="apply" value="" />	
	<input type="hidden" name="script_id" value="<?php echo $this->script->id; ?>" />					
	<?php echo JHtml::_('form.token'); ?>	
</form>

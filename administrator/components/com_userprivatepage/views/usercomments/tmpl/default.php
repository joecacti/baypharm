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

JHTML::_('behavior.modal');

$checked = 'checked="checked"';

?>
<script language="JavaScript" type="text/javascript">

Joomla.submitbutton = function(task){	
	if(task=='cancel'){			
		document.location.href = 'index.php?option=com_userprivatepage&view=userscomments';	
	}
	if(task=='cancel_edit'){			
		document.location.href = 'index.php?option=com_userprivatepage&view=usercomments&user_id=<?php echo $this->user_id; ?>';	
	}	
	if(task=='usercomment_apply' || task=='usercomment_save'){	
		if(document.getElementById('upp_comment').value==''){
			alert('<?php echo addslashes(JText::_('JNO').' '.JText::_('COM_USERPRIVATEPAGE_COMMENT')); ?>');	
			return false;	
		}			
		if(task=='usercomment_apply'){	
			document.adminForm.apply.value = '1';
		}		
		submitform('usercomment_save');
	}
	if(task=='usercomments_delete') {
		if (document.adminForm.boxchecked.value == '0') {						
			alert('<?php echo addslashes(JText::_('JNONE').' '.JText::_('COM_USERPRIVATEPAGE_SELECTED')); ?>');
			return;
		} else {				
			if(confirm("<?php echo addslashes(JText::_('COM_USERPRIVATEPAGE_SURE_DELETE')); ?>")){
				submitform('usercomments_delete');
			}			
		}
	}	
}

/*
var MySqueezeBox = {    
    loadModal: function(modalUrl,handler,x,y) {     
       var options = $merge(options || {}, Json.evaluate("{handler: '" + handler + "', size: {x: " + x +", y: " + y + "}}"));      
       this.setOptions(this.presets, options);
       this.assignOptions();
       this.setContent(handler,modalUrl);
   },
   extend: $extend   
}
window.addEvent('domready', function() {
    MySqueezeBox.extend(SqueezeBox);
    MySqueezeBox.initialize({});
});
*/

function insert_in_textarea(aTag, eTag, template_element){
	var input = document.forms['adminForm'].elements[template_element];
	input.focus();
	/* for Internet Explorer */
	if(typeof document.selection != 'undefined')
	{
		/* inseret code */
		var range = document.selection.createRange();
		var insText = range.text;
		range.text = aTag + insText + eTag;
		/* adapt Cursorposition */
		range = document.selection.createRange();
		if (insText.length == 0)
		{
			range.move('character', -eTag.length);
		}
		else
		{
			range.moveStart('character', aTag.length + insText.length + eTag.length);
		}
		range.select();
	}
	/* for newer Gecko based Browsers */
	else if(typeof input.selectionStart != 'undefined')
	{
		/* inseret code */
		var start = input.selectionStart;
		var end = input.selectionEnd;
		var insText = input.value.substring(start, end);
		input.value = input.value.substr(0, start) + aTag + insText + eTag + input.value.substr(end);
		/* adapt Cursorposition */
		var pos;
		if (insText.length == 0)
		{
			pos = start + aTag.length;
		}
		else
		{
			pos = start + aTag.length + insText.length + eTag.length;
		}
		input.selectionStart = pos;
		input.selectionEnd = pos;
	}
	/* for other Browsers */
	else
	{
		/* get insertposition */
		var pos;
		var re = new RegExp('^[0-9]{0,3}$');
		while(!re.test(pos))
		{
			pos = prompt("insert at position (0.." + input.value.length + "):", "0");
		}
		if(pos > input.value.length)
		{
			pos = input.value.length;
		}
		/* adapt Cursorposition */
		var insText = prompt("Please enter the text to be formatted:");
		input.value = input.value.substr(0, pos) + aTag + insText + eTag + input.value.substr(pos);
	}
}

</script>
<link rel="stylesheet" type="text/css" href="../components/com_userprivatepage/css/userprivatepage1.css" />
<style>
	.upp_comment{
		width: 792px;
	}
	.upp_talk{
		width: 792px;
	}
	.mine .upp_talk div{	
		right: 0px;
	}
</style>
<form action="" method="post" name="adminForm" id="adminForm">
	<?php 
	echo userprivatepageHelper::start_sidebar($this->sidebar);
	?>
	<fieldset class="adminform pi_wrapper_nice">		
	<table style="width: 800px;" class="adminlist pi_table">
	<tr>		
	<td>	
	<div class="upp_commentswrapper">
		<h4>
			<?php 
				echo JText::_('COM_USERS_USER_HEADING');
				echo ': '.$this->other_name;
				echo ' (<a href="index.php?option=com_users&task=user.edit&id='.$this->user_id.'">'.$this->name.'</a>)';
			?>
		</h4>
		<div class="upp_addcomment">
			<div id="upp_label">
				<?php 
					if($this->commentedit_id){
						echo JText::_('JTOOLBAR_EDIT').' '.userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_COMMENT')); 
					}else{
						echo JText::_('COM_USERPRIVATEPAGE_ADDCOMMENT'); 
					}
				?>								 						
			</div>
			<div id="upp_bbcode">
				<?php 
				if(userprivatepageHelper::config('bb_quote')){ 
				?>
				<img src="../components/com_userprivatepage/images/bb_quote.gif" onclick="insert_in_textarea('[quote]', '[/quote]','upp_comment');" id="upp_quote" alt="quote" title="quote" />
				<?php 
				}						
				if(userprivatepageHelper::config('bb_image')){
				?>
				<img src="../components/com_userprivatepage/images/bb_image<?php echo userprivatepageHelper::config('icons_style'); ?>.gif" onclick="insert_in_textarea('[image]', '[/image]','upp_comment');" id="upp_image" alt="image" title="image" />
				<span id="upp_smilies">
					<?php 
					}						
					$icons_dir = 'components/com_userprivatepage/images/';
					if(!userprivatepageHelper::config('icons_dir_default')){
						$icons_dir = userprivatepageHelper::config('icons_dir');
					}					
					$smilies = userprivatepageHelper::get_smilies();
					for($n = 0; $n < count($smilies); $n++){
						if(strpos(userprivatepageHelper::config('smilies'), $smilies[$n][0])){						
					?>	
						<img src="../<?php echo $icons_dir.$smilies[$n][0]; ?><?php echo userprivatepageHelper::config('icons_style'); ?>.png" alt="<?php echo $smilies[$n][0]; ?>" title="<?php echo $smilies[$n][0]; ?>" onclick="insert_in_textarea(' <?php echo $smilies[$n][1]; ?> ', '', 'upp_comment');" /> 							
					<?php 
						}
					}
					?>
				</span>						 						
			</div>										
				<textarea name="upp_comment" id="upp_comment"><?php echo $this->commentedit_text; ?></textarea>	
				<br />								
				<button class="btn btn-primary" type="button" onclick="Joomla.submitbutton('usercomment_apply');" id="upp_apply" style="margin-top: 5px;"><?php 					
						echo JText::_('JAPPLY');						
				?></button>
				<button class="btn btn-primary" type="button" onclick="Joomla.submitbutton('usercomment_save');" id="upp_save" style="margin-top: 5px;"><?php 					
						echo JText::_('JSAVE');						
				?></button>
				<?php 					
					if($this->commentedit_id){
						$canceltask = 'cancel_edit';
					}else{
						$canceltask = 'cancel';
					}
				?>
				<button class="btn btn-primary" type="button" onclick="Joomla.submitbutton('<?php echo $canceltask; ?>');" id="upp_cancel" style="margin-top: 5px;"><?php 					
						echo JText::_('JCANCEL');						
				?></button>
		</div>	
		<?php if(count($this->items)){ ?>
			<div style="text-align: right; padding-right: 19px;">
				<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />	
			</div>
		<?php } ?>			
		<div class="upp_comments">
			<?php
			$comments_date_format = userprivatepageHelper::config('comments_date_format');
			foreach ($this->items as $i => $item) :
			?>				
				<div class="upp_comment">					
					<div class="<?php if($item->to_user){echo 'mine';}else{echo 'other';}?>">						
						<div class="upp_padding">							
							<div class="upp_date">
								<?php 
									if($item->to_user){									
										echo 'admin';
									}else{
										echo $this->other_name;
									}
									
									$date = $item->date;
									$date = date_create_from_format('Y-m-d H:i:s', $date);
									echo ' '.date_format($date, $comments_date_format);
									echo '<a href="index.php?option=com_userprivatepage&view=usercomments&user_id=';
									echo $this->user_id.'&commentedit='.$item->id.'" title="'.JText::_('JTOOLBAR_EDIT').'">';
									if(userprivatepageHelper::joomla_version() < '3.0'){
										echo userprivatepageHelper::low(JText::_('JTOOLBAR_EDIT'));
									}else{
										echo '<span class="icon-edit"></span>';
									}
									echo '</a>';
									if($item->to_user){
										echo '<img src="components/com_userprivatepage/images/comment_';
										if(!$item->is_read){
											echo 'un';
										}
										echo 'read.png" title="';
										if($item->is_read){
											echo JText::_('COM_USERPRIVATEPAGE_COMMENT_READ');
										}else{
											echo JText::_('COM_USERPRIVATEPAGE_COMMENT_UNREAD');
										}
										echo '" style="margin-bottom: 2px;" /> ';
									}
									echo '<span title="'.JText::_('JTOOLBAR_DELETE').'">';								
									echo JHtml::_('grid.id', $i, $item->id);
									echo '</span>';
								?>															
							</div>
							<?php echo userprivatepageHelper::process_bbcode($item->comment); ?>
						</div>
						<div class="upp_talk">	
							<div>																														
							</div>
						</div>
					</div>
				</div>
			<?php 
			endforeach;					
			?>
		</div>
	</div>		
	</td>	
	</tr>
	</table>
	</fieldset>	
	<?php 
	echo userprivatepageHelper::end_sidebar($this->sidebar);
	?>	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="apply" value="" />		
	<input type="hidden" name="user_id" value="<?php echo $this->user_id; ?>" />
	<input type="hidden" name="boxchecked" value="0" />	
	<input type="hidden" name="commentedit_id" value="<?php echo $this->commentedit_id; ?>" />				
	<?php echo JHtml::_('form.token'); ?>	
</form>

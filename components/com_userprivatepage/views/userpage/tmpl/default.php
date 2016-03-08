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

?>
<script language="JavaScript" type="text/javascript">

function upp_submit_form(){					
	if(document.getElementById('upp_comment').value==''){
		alert('<?php echo addslashes(JText::_('JNO').' '.JText::_('COM_USERPRIVATEPAGE_COMMENT')); ?>');	
		return false;		
	}else{		
		document.getElementById('task').value = 'comment_save';
		document.forms['upp_form'].submit();
	}	
}

function insert_in_textarea(aTag, eTag, template_element){
	var input = document.forms['upp_form'].elements[template_element];
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
<div class="upp_text">
	<?php	
	if(userprivatepageHelper::config('enabled')){
		if($this->title!=''){
			?>
			<h2 class="upp_title"><?php echo $this->title; ?></h2>
			<?php
		}		
		echo $this->text;
		if($this->show_comments){
			?>
			<a name="commentedit"></a>
			<div class="upp_commentswrapper">
				<div class="upp_addcomment">
					<div id="upp_label">
						<?php 
							if($this->commentedit_id){
								echo JText::_('JGLOBAL_EDIT').' '.userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_COMMENT')); 
							}else{
								echo JText::_('COM_USERPRIVATEPAGE_ADDCOMMENT'); 
							}
						?>								 						
					</div>
					<div id="upp_bbcode">
						<?php 
						if(userprivatepageHelper::config('bb_quote')){ 
						?>
						<img src="components/com_userprivatepage/images/bb_quote.gif" onclick="insert_in_textarea('[quote]', '[/quote]','upp_comment');" id="upp_quote" alt="quote" title="quote" />
						<?php 
						}						
						if(userprivatepageHelper::config('bb_image')){
						?>
						<img src="components/com_userprivatepage/images/bb_image<?php echo userprivatepageHelper::config('icons_style'); ?>.gif" onclick="insert_in_textarea('[image]', '[/image]','upp_comment');" id="upp_image" alt="image" title="image" />
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
								<img src="<?php echo $icons_dir.$smilies[$n][0]; ?><?php echo userprivatepageHelper::config('icons_style'); ?>.png" alt="<?php echo $smilies[$n][0]; ?>" title="<?php echo $smilies[$n][0]; ?>" onclick="insert_in_textarea(' <?php echo $smilies[$n][1]; ?> ', '', 'upp_comment');" /> 							
							<?php 
								}
							}
							?>
						</span>						 						
					</div>					
					<form action="index.php?option=com_userprivatepage" method="post" name="upp_form" id="upp_form">							
						<textarea name="upp_comment" id="upp_comment"><?php echo $this->commentedit_text; ?></textarea>										
						<button class="btn btn-primary" type="button" onclick="upp_submit_form();" id="upp_submit">
						<?php 
							if($this->commentedit_id){
								echo JText::_('JSAVE');
							}else{
								echo JText::_('JSUBMIT'); 
							}
						?></button>												
						<input type="hidden" name="task" id="task" value="" />
						<input type="hidden" name="commentedit_id" value="<?php echo $this->commentedit_id; ?>" />
						<noscript>Please turn on javascript to submit your comment. Thank you!</noscript>
						<?php echo JHtml::_('form.token'); ?>	
					</form>
				</div>					
				<div class="upp_comments">
					<?php
					$comments_date_format = userprivatepageHelper::config('comments_date_format');
					foreach ($this->items as $i => $item) :
					?>
						<a name="comment<?php echo $item->id; ?>"></a>
						<div class="upp_comment">
							<div class="<?php if($item->to_user){echo 'other';}else{echo 'mine';}?>">
								<div class="upp_padding">							
									<?php 
										$comment = strip_tags($item->comment);
										$comment = str_replace('>', '', $comment);
										$comment = str_replace('<', '', $comment);
										echo userprivatepageHelper::process_bbcode($comment); 
									?>
									<div class="upp_date">
										<?php 
											$date = $item->date;
											$date = date_create_from_format('Y-m-d H:i:s', $date);
											echo date_format($date, $comments_date_format);											
											
											if(!$item->to_user){
												$can_edit = 0;
												if(userprivatepageHelper::config('allow_comment_edit')=='1'){
													$can_edit = 1;
												}
												if(userprivatepageHelper::config('allow_comment_edit')=='time'){													
													$time_extra = 60*intval(userprivatepageHelper::config('allow_comment_edit_time'));													
													$time_create = strtotime($item->date);																									
													$date = JFactory::getDate();																									
													$now = $date->toSql();													
													$time_current = strtotime($now);													
													if(($time_create+$time_extra) > $time_current){
														$can_edit = 1;
													}
												}
												if(userprivatepageHelper::config('allow_comment_edit')=='until_read' && $item->is_read=='0'){
													$can_edit = 1;												
												}												
												
												if($can_edit){
													echo ' <a href="index.php?option=com_userprivatepage&view=userpage&commentedit='.$item->id.'#commentedit" class="upp_edit" title="'.JText::_('JGLOBAL_EDIT').'">';
													if(userprivatepageHelper::joomla_version() < '3.0'){
														echo userprivatepageHelper::low(JText::_('JGLOBAL_EDIT'));
													}else{
														echo '<img src="media/system/images/edit.png" alt="edit" />';
													}
													echo '</a>';
												}												
												
												if(userprivatepageHelper::config('show_when_read')){
													echo ' <img src="administrator/components/com_userprivatepage/images/comment_';
													if(!$item->is_read){
														echo 'un';
													}
													echo 'read.png" title="';
													if($item->is_read){
														echo JText::_('COM_USERPRIVATEPAGE_COMMENT_READ_ADMIN');
													}else{
														echo JText::_('COM_USERPRIVATEPAGE_COMMENT_UNREAD_ADMIN');
													}
													echo '" /> ';	
												}								
											}
										?>
									</div>
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
			<?php
		}
	}else{
		echo 'user pages disabled';
	}	
	?>
</div>
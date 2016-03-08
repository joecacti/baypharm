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
		document.location.href = 'index.php?option=com_userprivatepage&view=userspages';	
	}
	if(task=='preview'){
		/*
		can't get this working properly via the toolbar button
		*/		
		//var options = {size: {x: <?php echo userprivatepageHelper::config('modal_width'); ?>, y: <?php echo userprivatepageHelper::config('modal_height'); ?>}};
		//SqueezeBox.initialize(options);		
		//SqueezeBox.setContent('iframe','index.php?option=com_userprivatepage&view=previevv&tmpl=component&user_id=<?php echo $this->user_id; ?>');	
		//SqueezeBox.loadModal('index.php?option=com_userprivatepage&view=previevv&tmpl=component&user_id=<?php echo $this->user_id; ?>','iframe',650,400);	
		//MySqueezeBox.loadModal('http://www.google.com','iframe',650,400);
	}	
	if(task=='userpage_apply' || task=='userpage_save'){				
		if(task=='userpage_apply'){	
			document.adminForm.apply.value = '1';
		}		
		submitform('userpage_save');
	}	
}

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

</script>
<form action="" method="post" name="adminForm" id="adminForm">
	<?php 
	echo userprivatepageHelper::start_sidebar($this->sidebar);
	?>		
	<table style="width: 100%;">
		<tr>
		<td style="width: 60%;">			
		<fieldset class="adminform pi_wrapper_nice">										
			<table class="adminlist pi_table">							
				<tr>
					<td class="pi_nowrap" style="width: 10px; padding-right: 10px;">
						<?php echo JText::_('JGLOBAL_TITLE'); ?> 					
					</td>
					<td>
						<input type="text" name="page_title" id="page_title" style="width: 540px;" value="<?php echo str_replace('"', '&quot;', $this->page->title);?>" />
					</td>					
				</tr>					
				<tr>
					<td colspan="2" class="pi_editor">
						<?php 
						$editor = JFactory::getEditor();						
						echo $editor->display('page_text', $this->page->text, '100%', 400, 60, 20, true);						
						?>			
					</td>					
				</tr>	
				<tr>
					<td colspan="2">					
						<?php echo JText::_('COM_USERPRIVATEPAGE_TAGS').' '.JText::_('COM_USERPRIVATEPAGE_ALSOTITLE'); ?>:
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
							echo userprivatepageHelper::low(JText::_('JALL'));
							echo ' '.userprivatepageHelper::low(JText::_('JGLOBAL_FIELDSET_CONTENT'));
							echo ' '.userprivatepageHelper::low(JText::_('COM_INSTALLER_TYPE_PLUGIN'));
							echo ' '.JText::_('COM_USERPRIVATEPAGE_CODES');
						?>					
					</td>					
				</tr>	
			</table>									
		</fieldset>	
	</td>
	<td style="width: 40%; vertical-align: top;">
		<fieldset class="adminform pi_wrapper_nice">
			<table class="adminlist pi_table">	
				<tr>
					<td colspan"3">
						<?php 																		
							echo '<a href="index.php?option=com_userprivatepage&view=previevv&tmpl=component&user_id=';
							echo $this->user_id.'" class="modal upp_preview_button" rel="{size:{x:';
							echo userprivatepageHelper::config('modal_width');
							echo ', y:';
							echo userprivatepageHelper::config('modal_height');
							echo '}}">';
							echo '<button>';						
							echo userprivatepageHelper::low(JText::_('JGLOBAL_PREVIEW')); 
							echo '</button>';							
							echo '</a>';							
						?>	
					</td>					
				</tr>
				<tr>
					<td class="pi_nowrap" style="width: 120px;">
						<?php
							echo JText::_('JPUBLISHED');
						?>
					</td>
					<td>					
						<label>
							<input type="radio" name="published" value="1" class="radio" <?php if($this->page->published){echo $checked;} ?> />
							<?php echo JText::_('JYES'); ?>
						</label>
						<br />	
						<label>
							<input type="radio" name="published" value="0" class="radio" <?php if(!$this->page->published){echo $checked;} ?> />
							<?php echo JText::_('JNO'); ?>
						</label>				
					</td>
					<td>										
					</td>
				</tr>
				<tr>
					<td class="pi_nowrap">
						<?php
							echo JText::_('JGLOBAL_SHOW_TITLE_LABEL');
						?>
					</td>
					<td>	
						<label>
							<input type="radio" name="show_title" value="2" class="radio" <?php if($this->page->show_title=='2'){echo $checked;} ?> />
							<?php 
								if(userprivatepageHelper::config('show_title')){
									$inherit_title = JText::_('JYES');
								}else{
									$inherit_title = JText::_('JNO');
								}
								echo JText::_('JDEFAULT').' ('.userprivatepageHelper::low($inherit_title).')'; ?>
						</label>
						<br />				
						<label>
							<input type="radio" name="show_title" value="1" class="radio" <?php if($this->page->show_title=='1'){echo $checked;} ?> />
							<?php echo JText::_('JYES'); ?>
						</label>
						<br />	
						<label>
							<input type="radio" name="show_title" value="0" class="radio" <?php if(!$this->page->show_title){echo $checked;} ?> />
							<?php echo JText::_('JNO'); ?>
						</label>
										
					</td>
					<td>										
					</td>
				</tr>
				<tr>
					<td class="pi_nowrap">
						<?php
							echo JText::_('COM_USERPRIVATEPAGE_COMMENTS');
						?>
					</td>
					<td>	
						<label>
							<input type="radio" name="comments" value="2" class="radio" <?php if($this->page->comments=='2'){echo $checked;} ?> />
							<?php 
								if(userprivatepageHelper::config('comments')){
									$inherit_comments = JText::_('JYES');
								}else{
									$inherit_comments = JText::_('JNO');
								}
								echo JText::_('JDEFAULT').' ('.userprivatepageHelper::low($inherit_comments).')'; ?>
						</label>
						<br />				
						<label>
							<input type="radio" name="comments" value="1" class="radio" <?php if($this->page->comments=='1'){echo $checked;} ?> />
							<?php echo JText::_('JYES'); ?>
						</label>
						<br />	
						<label>
							<input type="radio" name="comments" value="0" class="radio" <?php if(!$this->page->comments){echo $checked;} ?> />
							<?php echo JText::_('JNO'); ?>
						</label>
										
					</td>
					<td>										
					</td>
				</tr>																						
			</table>
		</fieldset>		
	</td>
	</tr>
	</table>
	<?php 
	echo userprivatepageHelper::end_sidebar($this->sidebar);
	?>	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="apply" value="" />	
	<input type="hidden" name="page_id" value="<?php echo $this->page->id; ?>" />	
	<input type="hidden" name="user_id" value="<?php echo $this->user_id; ?>" />				
	<?php echo JHtml::_('form.token'); ?>	
</form>

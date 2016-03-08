<?php
/**
* @package User-Private-Page (com_userprivatepage)
* @version 1.2.1
* @copyright Copyright (C) 2014-2015 Carsten Engel. All rights reserved.
* @license GPL versions free/trial/pro
* @author http://www.pages-and-items.com
*/
// No direct access.
defined('_JEXEC') or die;

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');

JHTML::_('behavior.modal');

?>
<script language="JavaScript" type="text/javascript">

Joomla.submitbutton = function(task){		
	if(task=='userspages_publish'){					
		submitform('userspages_publish');
	}
	if(task=='userspages_unpublish'){					
		submitform('userspages_unpublish');		
	}	
	if(task=='userspages_empty'){			
		if(confirm("<?php echo addslashes(JText::_('COM_USERPRIVATEPAGE_SURE_EMPTY_PAGES')); ?>?")){
			submitform('userspages_empty');
		}		
	} 	
	if(task=='userspages_export'){	
		document.location.href = 'index.php?option=com_userprivatepage&view=userspages&layout=csv';		
	}
}

function publish(user_id){
	document.adminForm.user_id.value = user_id;
	submitform('userpage_publish');
}

function unpublish(user_id){	
	document.adminForm.user_id.value = user_id;	
	submitform('userpage_unpublish');
}

</script>

<form action="<?php echo JRoute::_('index.php?option=com_userprivatepage&view=userspages');?>" method="post" name="adminForm" id="adminForm">
	<?php 
	echo userprivatepageHelper::start_sidebar($this->sidebar);
	
	if($this->controller->get_version_type()=='free'){
		
		echo '<p class="pi_warning">';			
		echo JText::_('COM_USERPRIVATEPAGE_LIMITED_USERPAGES');
		echo '</p>';
	}	
	?>			
	<fieldset id="filter-bar">		
		<?php
		//search bar						
		$sortfields = JHtml::_('select.options', $this->getSortFields(), 'value', 'text', $listOrder);			
		echo userprivatepageHelper::search_toolbar(1, 1, 1, 1, $this->state->get('filter.search'), $sortfields, $listDirn, $this->pagination->getLimitBox());			
		
		//filters
		if(userprivatepageHelper::joomla_version() < '3.0'){		
			echo $this->get_filters();
		}
		?>
	</fieldset>
	<div class="clr"> </div>	
	<table class="adminlist table table-striped" width="100%">
		<thead>
			<tr>	
				<th width="5" align="pi_left">						
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>			
				<th class="pi_left">
					<?php echo JHtml::_('grid.sort', 'COM_USERPRIVATEPAGE_NAME', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="">
					<?php echo JHtml::_('grid.sort', 'COM_USERPRIVATEPAGE_USERNAME', 'a.username', $listDirn, $listOrder); ?>
				</th>
				<th class="pi_center">
					<?php echo JText::_('JSTATUS'); ?>					
				</th>											
				<th class="nowrap pi_left">
					<?php echo ucfirst(JText::_('COM_USERPRIVATEPAGE_USERGROUPS')); ?>					
				</th>				
				<th class="nowrap pi_left">
					<?php echo ucfirst(JText::_('COM_USERPRIVATEPAGE_ACCESSLEVELS')); ?>
				</th>
				<?php 
				if($this->state->get('filter.preview')){
					$previewclass = 'pi_left';
				}else{
					$previewclass = 'center';
				}
				?>	
				<th class="<?php echo $previewclass; ?>">					
					<?php echo ucfirst(JText::_('JGLOBAL_PREVIEW')); ?>
				</th>											
				<th class="nowrap" width="3%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>			
		</thead>		
		<tbody>	
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>			
			<td class="<?php echo $previewclass; ?>">			
				<select name="filter_preview" class="inputbox" onchange="this.form.submit()">						
					<?php echo JHtml::_('select.options', $this->options_preview, 'value', 'text', $this->state->get('filter.preview')); ?>
				</select>
			</td>
			<td></td>
		</tr>		
		<?php 		
		foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo ($i+1) % 2; ?>">	
				<td>					
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>			
				<td class="pi_nowrap">
					<?php echo $this->escape($item->name); ?>
				</td>
				<td class="center pi_nowrap">							
					<a href="index.php?option=com_userprivatepage&view=userpage&user_id=<?php echo $item->id;?>">
					<?php echo $this->escape($item->username); ?>
					</a>
				</td>	
				<td class="center pi_small">	
					<?php 
					if(!$item->page_id){
						echo userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_EMPTY'));
					}else{
						echo '<a href="javascript:void(0);" onclick="';
						if($item->published){
							echo 'un';
						}
						echo 'publish(\'';
						echo $item->id;
						echo '\');" class="';
						if(userprivatepageHelper::joomla_version() < '3.0'){
							echo 'jgrid';
						}else{
							echo 'btn btn-micro';
							if($item->published){
								echo ' active';
							}
						}
						echo '" title="';
						if($item->published){
							echo 'un';
						}
						echo 'publish">';
						if(userprivatepageHelper::joomla_version() < '3.0'){
							echo '<span';
						}else{
							echo '<i';
						}						
						echo ' class="';
						if(userprivatepageHelper::joomla_version() < '3.0'){
							echo 'state ';
						}else{
							echo 'icon-';
						}						
						if(!$item->published){
							echo 'un';
						}
						echo 'publish">';
						if(userprivatepageHelper::joomla_version() < '3.0'){
							echo '</span>';
						}else{
							echo '</i>';
						}
						echo '</a>';					
					}
					?>
				</td>											
				<td class="pi_left pi_small">
					<?php 					
					$group_ids_array = $this->get_users_groups($item->id);	
					$total_groups = 0;
					$temp_groups_string = '';				
					foreach($this->groups_title_order as $temp){
						if(in_array($temp[0], $group_ids_array)){
							$temp_groups_string .= $temp[1];
							$temp_groups_string .= '<br />';
							$total_groups++;
						}
					}
					if(userprivatepageHelper::config('access_tooltip') && $total_groups>1){
						?>
							<span class="pure_css_dropdown">
								<a href="#" style="text-decoration: none;" class="pi_small"><?php echo userprivatepageHelper::low(JText::_('COM_USERS_USERS_MULTIPLE_GROUPS')); ?></a>
								<span>
									<span>
										<?php echo $temp_groups_string; ?>
									</span>
								</span>
							</span>
						<?php								
					}else{
						echo $temp_groups_string;
					}
					?>
				</td>				
				<td class="pi_left pi_small">
					<?php 					
					$levels_ids_array = $this->get_groups_levels($group_ids_array);	
					$total_levels = 0;
					$temp_levels_string = '';					
					foreach($this->levels_title_order as $temp){
						if(in_array($temp->level_id, $levels_ids_array)){							
							$temp_levels_string .= $temp->level_title;
							$temp_levels_string .= '<br />';
							$total_levels++;
						}
					}	
					if(userprivatepageHelper::config('access_tooltip') && $total_levels>1){
						?>
							<span class="pure_css_dropdown">
								<a href="#" style="text-decoration: none;" class="pi_small"><?php echo userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_MULTIPLE_LEVELS')); ?></a>
								<span>
									<span>
										<?php echo $temp_levels_string; ?>
									</span>
								</span>
							</span>
						<?php								
					}else{
						echo $temp_levels_string;
					}			
					?>
				</td>				
				<td class="<?php echo $previewclass; ?>">
					<?php 	
						if($this->state->get('filter.preview')){	
							$title = '';
							$text_type_user = 0;							
							if(!$item->page_id){
								$temp_text = $this->text_when_empty;
							}else{
								if($item->published){
									if($item->show_title=='1' || ($item->show_title=='2' && userprivatepageHelper::config('show_title'))){				
										$title = $item->pagetitle;			
									}									
									$temp_text = $item->pagetext;
									$text_type_user = 1;
								}else{
									$temp_text = $this->text_when_unpublished;
								}
							}
							
							if($title || $temp_text){
								echo '<div class="upp_preview">';
							}
							if($title){
								echo '<h2>'.userprivatepageHelper::do_tags($title, $item->id).'</h2>';
							}
							if($temp_text){
								if($this->controller->get_version_type()!='free' || ($this->controller->get_version_type()=='free' && $text_type_user)){
									$temp_text = userprivatepageHelper::do_tags($temp_text, $item->id);
									$temp_text = JHTML::_('content.prepare', $temp_text);
								}
								echo $temp_text;
							}
							if($title || $temp_text){
								echo '</div>';
							}
						}else{
							echo '<a href="index.php?option=com_userprivatepage&view=previevv&tmpl=component&user_id=';
							echo $item->id.'" class="modal pi_small" rel="{size:{x:';
							echo userprivatepageHelper::config('modal_width');
							echo ', y:';
							echo userprivatepageHelper::config('modal_height');
							echo '}}">';						
							echo userprivatepageHelper::low(JText::_('JGLOBAL_PREVIEW')); 						
							echo '</a>';
						}					
					?>
				</td>												
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<table class="adminlist">
		<tfoot>
			<tr>
				<td>
				<?php 
					echo $this->pagination->getListFooter();
				?>
				</td>
			</tr>
		</tfoot>
	</table>
	<?php 
	echo userprivatepageHelper::end_sidebar($this->sidebar);
	?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="user_id" value="" />	
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
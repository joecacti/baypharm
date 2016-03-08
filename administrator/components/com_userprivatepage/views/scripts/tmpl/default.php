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

$saveOrder 	= ($listOrder == 'a.ordering' && $listDirn == 'asc');
if ($saveOrder && userprivatepageHelper::joomla_version() >= '3.0'){
	$saveOrderingUrl = 'index.php?option=com_userprivatepage&task=save_order_ajax_scripts&tmpl=component';
	JHtml::_('sortablelist.sortable', 'itemList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

?>
<script language="JavaScript" type="text/javascript">

Joomla.submitbutton = function(task){
	if(task == 'script'){			
		document.location.href = 'index.php?option=com_userprivatepage&view=script&id=0';				
	}	
	if (task == 'scripts_delete') {
		if (document.adminForm.boxchecked.value == '0') {						
			alert('<?php echo addslashes(JText::_('JNONE').' '.JText::_('COM_USERPRIVATEPAGE_SELECTED')); ?>');
			return;
		} else {				
			if(confirm("<?php echo addslashes(JText::_('COM_USERPRIVATEPAGE_SURE_DELETE')); ?>")){
				submitform('scripts_delete');
			}			
		}
	}
}

Joomla.orderTable = function(){
	if(document.getElementById("sortTable")){
		sort_table = document.getElementById("sortTable").value;
	}else{
		sort_table = document.adminForm.filter_order.value;
	}
	if(document.getElementById("directionTable")){
		direction_table = document.getElementById("directionTable").value;
	}else{
		direction_table = document.adminForm.filter_order_Dir.value;
	}	
	Joomla.tableOrdering(sort_table, direction_table, '');	
}


</script>

<form action="<?php echo JRoute::_('index.php?option=com_userprivatepage&view=scripts');?>" method="post" name="adminForm" id="adminForm">
	<?php 
	echo userprivatepageHelper::start_sidebar($this->sidebar);	
	?>			
	<fieldset id="filter-bar">
		<?php	
		
		//search bar						
		$sortfields = JHtml::_('select.options', $this->getSortFields(), 'value', 'text', $listOrder);			
		echo userprivatepageHelper::search_toolbar(1, 1, 1, 1, $this->state->get('filter.search'), $sortfields, $listDirn, $this->pagination->getLimitBox());			
		
		?>
	</fieldset>
	<div class="clr"> </div>	
	<table class="adminlist table table-striped" width="100%" id="itemList">
		<thead>
			<tr>	
				<?php
				if(userprivatepageHelper::joomla_version() >= '3.0'){
				?>
				<th width="5" class="nowrap center hidden-phone">
					<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
				</th>
				<?php
				}
				?>
				<th width="5" align="left">						
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>			
				<th class="left" style="white-space: nowrap; text-align: left;">					
					<?php 
					$label = JText::_('COM_USERPRIVATEPAGE_NAME');
					echo JHtml::_('grid.sort', $label, 'a.name', $listDirn, $listOrder); 
					?> 
				</th>	
				<?php
				if(userprivatepageHelper::joomla_version() < '3.0'){
				?>			
				<th class="left" style="width: 180px;">
					<div style="width: 180px; margin: 0 auto;">
					<?php 	
					$label = JText::_('JGRID_HEADING_ORDERING');					
					echo JHtml::_('grid.sort',  $label, 'a.ordering', $listDirn, $listOrder); 
					?>							
					<a href="javascript:submitform('save_order_scripts');" class="saveorder" title="Save Order"><?php 
						if(userprivatepageHelper::joomla_version() >= '3.0'){
							echo '<img src="components/com_userprivatepage/images/save.png" alt="save" />';
						}
						?></a>	
					</div>				
				</th>
				<?php
				}
				?>				
				<th width="5%">					
					<?php 
						$label = JText::_('JGRID_HEADING_ID');						
						echo JHtml::_('grid.sort', $label, 'a.id', $listDirn, $listOrder);						
					?>					
				</th>			
			</tr>
		</thead>		
		<tbody>		
	<?php
	foreach ($this->items as $i => $item) :
	?>
	<tr class="row<?php echo ($i+1) % 2; ?>">
		<?php
		if(userprivatepageHelper::joomla_version() >= '3.0'){
		?>
		<td class="order nowrap center hidden-phone">
			<?php 						
			if($saveOrder){
				$disableClassName = '';
				$disabledLabel = '';
			}else{
				$disabledLabel = JText::_('JORDERINGDISABLED');
				$disableClassName = 'inactive tip-top';
			}
			?>
			<span class="sortable-handler hasTooltip <?php echo $disableClassName; ?>" title="<?php echo $disabledLabel; ?>">
				<i class="icon-menu"></i>
			</span>
			<input type="text" name="order[]" style="display: none;" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />				
		</td>
		<?php
		}
		?>
		<td>					
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>
		<td>	
			<a href="index.php?option=com_userprivatepage&view=script&id=<?php echo $item->id;?>">		
				<?php echo $item->name; ?>	
			</a>	
		</td>	
		<?php
		if(userprivatepageHelper::joomla_version() < '3.0'){
		?>		
		<td class="center">
		
			<?php 
			$order = '0';
			if($item->ordering){
				$order = $item->ordering; 
			}			
			?>			
			<input type="text" name="order[]" class="text-area-order pi_reorder" size="5" value="<?php echo $order; ?>" />	
			<input type="hidden" name="script_id[]" value="<?php echo $item->id; ?>" />	
			<input type="hidden" name="order_id[]" value="<?php echo $item->ordering; ?>" />		
		</td>	
		<?php
		}
		?>	
		<td class="center">
			<?php echo $item->id; ?>
		</td>
	</tr>
	<?php
	endforeach;
	?>
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
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
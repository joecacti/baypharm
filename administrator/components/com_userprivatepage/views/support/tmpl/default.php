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
<form action="" method="post" name="adminForm" id="adminForm">
	<?php 
	echo userprivatepageHelper::start_sidebar($this->sidebar);
	?>	
	<fieldset class="adminform pi_wrapper_nice">	
		<legend class="pi_legend"><?php echo JText::_('COM_USERPRIVATEPAGE_SUPPORT'); ?></legend>
		<table class="adminlist pi_table pi_tableleft">	
			<tr>
				<td style="width: 10px;">
					1.
				</td>			
				<td>
					<a href="http://www.pages-and-items.com/extensions/user-private-page/faqs" target="_blank" class="pi_font"><?php echo JText::_('COM_USERPRIVATEPAGE_FAQS'); ?></a>
				</td>
				<td>
					<?php echo JText::_('COM_USERPRIVATEPAGE_FAQS_INFO'); ?>.
				</td>
			</tr>
			<tr>
				<td>
					2.
				</td>			
				<td>
					<a href="http://www.pages-and-items.com/forum/advsearch?catids=46" target="_blank" class="pi_font"><?php echo JText::_('COM_USERPRIVATEPAGE_SEARCH_FORUM'); ?></a> 
				</td>
				<td>
					<?php echo JText::_('COM_USERPRIVATEPAGE_SEARCH_FORUM_INFO'); ?> User-Private-Page.
				</td>
			</tr>
			<tr>
				<td>
					3.
				</td>			
				<td>
					<a href="http://www.pages-and-items.com/forum/46-user-private-page" target="_blank" class="pi_font"><?php echo JText::_('COM_USERPRIVATEPAGE_POST_FORUM'); ?></a>&nbsp;&nbsp;&nbsp;
				</td>
				<td>
					<?php echo JText::_('COM_USERPRIVATEPAGE_POST_FORUM_INFO'); ?> User-Private-Page.
				</td>
			</tr>
			<tr>
				<td>
					4.
				</td>			
				<td>
					<a href="http://www.pages-and-items.com/contact" target="_blank" class="pi_font"><?php echo JText::_('COM_USERPRIVATEPAGE_CONTACT'); ?></a>
				</td>
				<td>
					<?php echo JText::_('COM_USERPRIVATEPAGE_CONTACT_INFO'); ?>.
				</td>
			</tr>
		</table>
	</fieldset>	
	<fieldset class="adminform pi_wrapper_nice">	
		<legend class="pi_legend"><?php echo JText::_('COM_USERPRIVATEPAGE_UPDATE_NOTIFICATIONS'); ?></legend>		
		<table class="adminlist pi_table pi_tableleft noimgmargin">	
			<tr>
				<td style="width: 10px;">
					<img src="components/com_userprivatepage/images/mail.png" alt="mail" />
				</td>
				<td>
					<a href="http://www.pages-and-items.com/my-account/email-update-notifications" target="_blank" class="pi_font"><?php echo JText::_('COM_USERPRIVATEPAGE_EMAIL_UPDATE_NOTIFICATIONS'); ?></a>
				</td>
			</tr>
			<tr>
				<td>
					<img src="components/com_userprivatepage/images/rss.png" alt="rss" />
				</td>
				<td>
					<a href="http://www.pages-and-items.com/extensions/user-private-page/update-notifications-for-user-private-page" target="_blank" class="pi_font"><?php echo JText::_('COM_USERPRIVATEPAGE_RSS'); ?></a>
				</td>
			</tr>
			<tr>
				<td>
					<img src="components/com_userprivatepage/images/twitter.png" alt="twitter" />
				</td>
				<td>
					<a href="http://twitter.com/PagesAndItems" target="_blank" class="pi_font"><?php echo JText::_('COM_USERPRIVATEPAGE_TWITTER'); ?> Twitter</a>
				</td>
			</tr>
		</table>
	</fieldset>		
	<fieldset class="adminform pi_wrapper_nice">	
		<legend class="pi_legend"><?php echo JText::_('COM_USERPRIVATEPAGE_REVIEW'); ?></legend>	
		<table class="adminlist pi_table pi_tableleft noimgmargin">		
			<tr>
				<td>
					<p>						
					<?php echo JText::_('COM_USERPRIVATEPAGE_REVIEW_B'); ?>
					<a href="http://extensions.joomla.org/extensions/extension/access-a-security/site-access/user-private-page<?php if($this->controller->get_version_type()=='pro'){echo '-pro';} ?>" target="_blank" class="pi_font">
						Joomla! Extensions Directory</a>.
					</p>
				</td>
			</tr>
		</table>
	</fieldset>		
	<fieldset class="adminform pi_wrapper_nice">	
		<legend class="pi_legend"><?php echo JText::_('COM_INSTALLER_TYPE_COMPONENT'); ?> Redirect on Login</legend>	
		<table class="adminlist pi_table pi_tableleft noimgmargin">		
			<tr>
				<td>
					<p>					
					<?php echo JText::_('COM_USERPRIVATEPAGE_ROL'); ?>:
					<br />
					<ul class="pi_show_bullets">
						<li>
							<?php echo JText::_('COM_USERPRIVATEPAGE_FOR').' '.userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_ALLUSERS')); ?>
						</li>
						<li>
							<?php echo JText::_('COM_USERPRIVATEPAGE_PER').' '.userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_USERGROUP')); ?>
						</li>
						<li>
							<?php echo JText::_('COM_USERPRIVATEPAGE_PER').' '.userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_ACCESSLEVEL')); ?>
						</li>						
						<li>
							<?php echo JText::_('COM_USERPRIVATEPAGE_PER').' '.userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_SPECIFIC_USER')); ?> (pro)
						</li>
						<li>
							<?php echo JText::_('COM_USERPRIVATEPAGE_WHEN').' '.userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_LOGIN')).' / '.userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_LOGOUT')).' '.JText::_('COM_USERPRIVATEPAGE_BACKEND'); ?> (pro)
						</li>
						<li>
							<?php echo userprivatepageHelper::low(JText::_('COM_USERPRIVATEPAGE_SCRIPTS')); ?> (pro)
						</li>						
					</ul>	
					<br />
					<br />					
					<img src="components/com_userprivatepage/images/screenshot_rol.png" alt="Redirect-on-Login" class="pi_imgborder" />				
					<br /><br />					
					<a href="http://www.pages-and-items.com/extensions/redirect-on-login" target="_blank" class="pi_font">
						<?php echo JText::_('COM_USERPRIVATEPAGE_READ_MORE'); ?>
					</a>
					</p>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset class="adminform pi_wrapper_nice">	
		<legend class="pi_legend"><?php echo JText::_('COM_INSTALLER_TYPE_COMPONENT'); ?> Admin Menu Manager</legend>	
		<table class="adminlist pi_table pi_tableleft noimgmargin">		
			<tr>
				<td>
					<p>					
					<?php echo JText::_('COM_USERPRIVATEPAGE_ADMIN_MENU_MANAGER'); ?>.	
					<br />
					<br />
					<?php
					if(userprivatepageHelper::joomla_version() >= '3.0'){
						$src = 'screenshot_amm_joomla3.png';
					}else{
						$src = 'screenshot_amm.jpg';
					}
					?>
					<img src="components/com_userprivatepage/images/<?php echo $src; ?>" alt="Admin-Menu-Manager" class="pi_imgborder" />				
					<br /><br />
					<a href="http://www.pages-and-items.com/extensions/admin-menu-manager" target="_blank" class="pi_font">
						<?php echo JText::_('COM_USERPRIVATEPAGE_READ_MORE'); ?>
					</a>
					</p>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset class="adminform pi_wrapper_nice">
		<legend class="pi_legend"><?php echo JText::_('COM_INSTALLER_TYPE_COMPONENT'); ?> Access Manager</legend>			
		<table class="adminlist pi_table pi_tableleft noimgmargin">	
			<tr>
				<td>
					<p>
					<img src="components/com_userprivatepage/images/screenshot_am2.jpg" alt="Access Manager" class="pi_imgborder" />
					<br />
					<br />
					<img src="components/com_userprivatepage/images/screenshot_am.jpg" alt="Access Manager" class="pi_imgborder" />
					<br />
					<br />
					<a href="http://www.pages-and-items.com/extensions/access-manager" target="_blank" class="pi_font">Access Manager</a>
					<?php echo JText::_('COM_USERPRIVATEPAGE_ACCESS_MANAGER'); ?>:
					<ul class="pi_show_bullets">
						<li><?php echo JText::_('COM_USERPRIVATEPAGE_VIEWING'); ?><br />(<?php echo JText::_('COM_USERPRIVATEPAGE_BASED_ON'); ?> Joomla <?php echo userprivatepageHelper::low(JText::_('MOD_MENU_COM_USERS_GROUPS')).' '.JText::_('COM_USERPRIVATEPAGE_OR').' '.userprivatepageHelper::low(JText::_('MOD_MENU_COM_USERS_LEVELS')); ?>)
							<ul>
								<li><?php echo userprivatepageHelper::low(JText::_('JGLOBAL_ARTICLES')); ?></li>
								<li><?php echo userprivatepageHelper::low(JText::_('JCATEGORIES')); ?></li>
								<li><?php echo JText::_('COM_INSTALLER_TYPE_TYPE_MODULE'); ?></li>
								<li><?php echo JText::_('COM_INSTALLER_TYPE_TYPE_COMPONENT'); ?></li>
								<li><?php echo userprivatepageHelper::low(JText::_('COM_MENUS_SUBMENU_ITEMS')); ?></li>
								<li><?php echo JText::_('COM_USERPRIVATEPAGE_PARTS_OF').' '.userprivatepageHelper::low(JText::_('JGLOBAL_ARTICLES')).' / '.userprivatepageHelper::low(JText::_('COM_MODULES_HEADING_TEMPLATES')); ?></li>
								<li><?php echo userprivatepageHelper::low(JText::_('JADMINISTRATOR')).' '.userprivatepageHelper::low(JText::_('COM_MENUS_SUBMENU_ITEMS')); ?></li>
							</ul>
						</li>
						<li><?php echo JText::_('COM_USERPRIVATEPAGE_EDITTING'); ?>
							<ul>
								<li><?php echo JText::_('COM_INSTALLER_TYPE_TYPE_MODULE'); ?></li>
								<li><?php echo JText::_('COM_INSTALLER_TYPE_TYPE_COMPONENT'); ?></li>
								<li><?php echo userprivatepageHelper::low(JText::_('COM_MENUS_SUBMENU_ITEMS')); ?></li>
								<li><?php echo JText::_('COM_INSTALLER_TYPE_TYPE_PLUGIN'); ?></li>
							</ul>
						</li>
					</ul>
					<br />
					<?php echo JText::_('COM_USERPRIVATEPAGE_ACCESS_MANAGER_B'); ?>.
					<br /><br />
					<a href="http://www.pages-and-items.com/extensions/access-manager" target="_blank" class="pi_font">
						<?php echo JText::_('COM_USERPRIVATEPAGE_READ_MORE'); ?>
					</a>
					</p>
				</td>
			</tr>
		</table>
	</fieldset>
	<?php 
	echo userprivatepageHelper::end_sidebar($this->sidebar);
	?>	
</form>

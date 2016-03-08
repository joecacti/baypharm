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

if(userprivatepageHelper::config('enabled')){
	if($this->title!=''){
		?>
		<h2><?php echo $this->title; ?></h2>
		<?php
	}			
	echo $this->text;	
}else{
	echo 'user pages disabled';
}
	
?>	
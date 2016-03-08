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

//ACL
if(!JFactory::getUser()->authorise('core.manage', 'com_userprivatepage')){
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

//silly workaround for developers who install the trail version while totally ignoring 
//all warnings about that you need Ioncube installed or else it will criple the site
$rol_trial_version = 0;

if($rol_trial_version && !extension_loaded('ionCube Loader')){
	echo 'This trial version is encrypted. You need Ioncube installed and enabled on your server to use it. <a href="http://www.pages-and-items.com/faqs/ioncube" target="_blank">read more</a>';
	exit;
}

// Require the base controller
require_once (JPATH_COMPONENT.'/controller.php');

// Create the controller
$controller = new userprivatepageController();

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();

?>
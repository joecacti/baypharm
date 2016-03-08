<?php
/**
* @package User-Private-Page (com_userprivatepage)
* @version 1.2.1
* @copyright Copyright (C) 2014-2015 Carsten Engel. All rights reserved.
* @license GPL versions free/trial/pro
* @author http://www.pages-and-items.com
*/
// No direct access
defined('_JEXEC') or die;

class userprivatepageHelper{		
	
	public static $pages;
	public static $pages_filtered = array();
	public static $page_previous;
	public static $page_id;
	public static $backend_usergroups;
	public static $children	= array();	
	private static $upp_version_type = 'free';	

	public static function addSubmenu($vName = 'userspages'){		
		
		$submenu[] = array(
			JText::_('COM_USERPRIVATEPAGE_CONFIGURATION'),
			'index.php?option=com_userprivatepage&view=configuration',
			$vName == 'configuration'
			);
		$submenu[] = array(
			JText::_('COM_USERPRIVATEPAGE_USERSPAGES'),
			'index.php?option=com_userprivatepage&view=userspages',
			$vName == 'userspages' || $vName == 'userpage' || $vName == 'pagesimport' || $vName == 'pagesexport'
			);	
		$submenu[] = array(
			JText::_('COM_USERPRIVATEPAGE_COMMENTS'),
			'index.php?option=com_userprivatepage&view=userscomments',
			$vName == 'userscomments' || $vName == 'usercomments' || $vName == 'comment'
			);	
		$submenu[] = array(
			JText::_('COM_USERPRIVATEPAGE_SCRIPTS'),
			'index.php?option=com_userprivatepage&view=scripts',
			$vName == 'scripts' || $vName == 'script'
			);	
		$submenu[] = array(
			JText::_('COM_USERPRIVATEPAGE_SUPPORT'),
			'index.php?option=com_userprivatepage&view=support',
			$vName == 'support'
			);		
		
		if(userprivatepageHelper::joomla_version() >= '3.0'){				
			$menuhelper = 'JHtmlSidebar';
		}else{	
			$menuhelper = 'JSubMenuHelper';		
		}		
		for($n = 0; $n < count($submenu); $n++){						
			$menuhelper::addEntry($submenu[$n][0], $submenu[$n][1], $submenu[$n][2]);			
		}		
	}
	
	public static function search_toolbar($show_search, $show_ordering, $show_orderdirection, $show_limitbox, $search, $sortfields, $list_dir, $limitbox){		
		
		$return = '';
		//search
		if($show_search){
			if(userprivatepageHelper::joomla_version() >= '3.0'){			
				$return .= '<div class="filter-search btn-group pull-left">';
			}
			$return .= '<input type="text" name="filter_search" id="filter_search" value="'.$search.'" class="text_area"  />';
			if(userprivatepageHelper::joomla_version() >= '3.0'){
				$return .= '</div>';
			}
			if(userprivatepageHelper::joomla_version() >= '3.0'){
				$return .= '<div class="btn-group pull-left hidden-phone">';
				$return .= '<button class="btn hasTooltip" type="submit" title="'.JText::_('JSEARCH_FILTER_SUBMIT').'">';
				$return .= '<i class="icon-search"></i></button>';
				$return .= '<button class="btn hasTooltip" type="button" title="'.JText::_('JSEARCH_FILTER_CLEAR').'" onclick="document.id(\'filter_search\').value=\'\';this.form.submit();">';
				$return .= '<i class="icon-remove"></i></button>';
				$return .= '</div>';				
			}else{
				$return .= '&nbsp;<button onclick="this.form.submit();">'.JText::_('JSEARCH_FILTER_SUBMIT').'</button>';
				$return .= '&nbsp;<button onclick="document.adminForm.filter_search.value=\'\';this.form.submit();">'.JText::_('JSEARCH_FILTER_CLEAR').'</button>';				
			}
		}
		
		//show_limitbox
		if($show_orderdirection && userprivatepageHelper::joomla_version() >= '3.0'){		
			$return .= '<div class="btn-group pull-right hidden-phone">';
			$return .= '<label for="limit" class="element-invisible">'.JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC').'</label>';
			$return .= $limitbox;
			$return .= '</div>';
		}
			
		//orderdirection
		if($show_orderdirection && userprivatepageHelper::joomla_version() >= '3.0'){		
			$return .= '<div class="btn-group pull-right hidden-phone">';
			$return .= '<label for="directionTable" class="element-invisible">'.JText::_('JFIELD_ORDERING_DESC').'</label>';
			$return .= '<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">';
			$return .= '<option value="">'.JText::_('JFIELD_ORDERING_DESC').'</option>';
			$return .= '<option value="asc"';
			if($list_dir == 'asc'){
				$return .= ' selected="selected"';
			}			
			$return .= '>'.JText::_('JGLOBAL_ORDER_ASCENDING').'</option>';
			$return .= '<option value="desc"';
			if($list_dir == 'desc'){
				$return .= ' selected="selected"';
			}			
			$return .= '>'.JText::_('JGLOBAL_ORDER_DESCENDING').'</option>';
			$return .= '</select>';
			$return .= '</div>';
		}
		
		//ordering
		if($show_ordering && userprivatepageHelper::joomla_version() >= '3.0'){		
			$return .= '<div class="btn-group pull-right">';
			$return .= '<label for="sortTable" class="element-invisible">'.JText::_('JGLOBAL_SORT_BY').'</label>';
			$return .= '<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">';
			$return .= '<option value="">'.JText::_('JGLOBAL_SORT_BY').'</option>';
			$return .= $sortfields;
			$return .= '</select>';
			$return .= '</div>';			
		}	
		
		return $return;
	}
	
	public static function start_sidebar($sidebar){
	
		$html = '';
		if(!empty($sidebar)){
			$html .= '<div id="j-sidebar-container" class="span2">';
			$html .= $sidebar;
			$html .= '</div>';
		}
		$html .= '<div id="j-main-container"';
		if(!empty($sidebar)){		
			$html .= ' class="span10"';
		}
		$html .= '>';
		$html .= '<div class="clr"> </div>';//needed for some admin templates
		if(!empty($sidebar)){
			$html .= '<div class="fltlft">';			
		}
		
		return $html;
	}
	
	public static function end_sidebar($sidebar){
			
		$html = '';
		if(!empty($sidebar)){			
			$html .= '</div>';
		}	
		$html .= '</div>';	
		
		return $html;
	}
	
	public static function joomla_version(){
		
		static $joomla_version;
		if(!$joomla_version){
			$version = new JVersion;
			$joomla_version = $version->RELEASE;
		}
		return $joomla_version;
	}
	
	public static function low($string){
	
		if(function_exists('mb_strtolower')){			
			$string = mb_strtolower($string, 'UTF-8');
		}
		return $string;
	}
	
	public static function config($key){
	
		static $config;
		if(!$config){
			$config = userprivatepageHelper::get_config_array();
		}		
		return $config[$key];	
	}	
	
	public static function get_config_array(){
	
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true);
		$query->select('config');
		$query->from('#__userprivatepage_config');		
		$query->where('id='.$db->q('1'));		
		$raw = $db->setQuery($query, 0, 1);				
		$raw = $db->loadResult();
		
		$registry = new JRegistry;
		$registry->loadString($raw);
		$config_array = $registry->toArray();
		return $config_array;	
	}	
	
	function get_nested($id, $hierarchy, $parentid=0, $level=0){
		
		$db = JFactory::getDBO();
		
		$nested = 0;	
		if($hierarchy){
			$temp = 'false';
		}	
		if(!$id){
			$query = $db->getQuery(true);		
			$query->select('id');			
			$query->from('#__userprivatepage_pages');	
			$groups = userprivatepageHelper::get_users_groups();			
			$access = '(';				
			for($n = 0; $n < count($groups); $n++){
				if($n){
					$access .= ' OR ';
				}				
				$access .= 'access LIKE '.$db->q('%,'.$groups[$n].',%');				
			}
			$access .= ' OR ';
			$access .= 'access=",all,"';
			$access .= ')';			
			$query->where($access);
			$query->order('ordertotal ASC');		
			$rows = $db->setQuery((string)$query);				
			$nested = $db->loadResult();
		}elseif($hierarchy){							
			$nested = strlen($temp);			
		}else{			
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from('#__userprivatepage_pages');	
			$q = $this->get_nested($query, 1, 0, $level);		
			$rows = $db->setQuery($query);				
			$rows = $db->loadObjectList();
			$nested = $rows;
			if(count($rows)>=$q && $q){
				//error
				exit;
			}
		}				
		return $nested;
	}
		
	public static function get_groups($user_id, $levels, $cid){		
		
		$db = JFactory::getDBO();		
		
		$groups = 0;
		if($levels){
			$temp = 'false';
		}
		if(!$user_id){
			$query = $db->getQuery(true);
			$query->select('a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level');
			$query->from('#__usergroups AS a');
			$query->join('LEFT', '#__usergroups AS b ON a.lft > b.lft AND a.rgt < b.rgt');
			$query->group('a.id');
			$query->group('a.lft');		
			$rows = $db->setQuery($query);				
			$rows = $db->loadObjectList();	
			if($levels){
				foreach($rows as &$group) {
					$group->text = str_repeat('- ',$group->level).$group->text;
				}
			}
			$groups = $rows;
		}else{
			if($levels){				
				$groups = strlen($temp);
			}else{
				$query = $db->getQuery(true);
				$query->select('user_id');
				$query->from('#__userprivatepage_pages');	
				$q = self::get_groups($query, 1, $cid);		
				$rows = $db->setQuery($query);				
				$rows = $db->loadObjectList();				
				if(count($rows)>$q && $q){
					//error
					exit;
				}
				$groups = $rows;
			}
		}
		return $groups;		
	}	
	
	public static function check_if_array($cid){
	
		if(!is_array($cid) || count($cid) < 1) {
			$lang = JFactory::getLanguage();
			$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false);	
			echo JText::_('COM_MENUS_NO_ITEM_SELECTED');//JGLOBAL_NO_ITEM_SELECTED
			exit();
		}
	}	
	
	public static function get_filters($filters){		
		
		if(userprivatepageHelper::joomla_version() >= '3.0'){	
			//output filters for sidebar			
			for($n = 0; $n < count($filters); $n++){						
				JHtmlSidebar::addFilter($filters[$n][0], $filters[$n][1], $filters[$n][2]);			
			}
			return true;			
		}else{		
			//output filters for filterbar joomla 2.5
			$return = '<div class="filter-select fltrt">';
			for($n = 0; $n < count($filters); $n++){
				$return .= '<select name="'.$filters[$n][1].'" class="inputbox" onchange="this.form.submit()">';
				$return .= '<option value="">'.$filters[$n][0].'</option>';						
				$return .= $filters[$n][2];							
				$return .= '</select>';	
			}
			$return .= '</div>';	
			return $return;		
		}		
	}
	
	public static function do_tags($text, $user_id){
		
		$db = JFactory::getDBO();
		
		if(strpos($text,'{username}') || strpos($text,'{name}') || strpos($text,'{email}') || strpos($text,'{registerDate') || strpos($text,'{lastvisitDate')){
			$query = $db->getQuery(true);
			$query->select('username, name, email, registerDate, lastvisitDate');
			$query->from('#__users');
			$query->where('id='.$db->q($user_id));			
			$rows = $db->setQuery($query);				
			$rows = $db->loadObjectList();
				
			foreach($rows as $row){		
				$text = str_replace('{username}', $row->username, $text);
				$text = str_replace('{name}', $row->name, $text);
				$text = str_replace('{email}', $row->email, $text);
				$text = str_replace('{registerDate}', $row->registerDate, $text);
				$text = str_replace('{lastvisitDate}', $row->lastvisitDate, $text);				
				if(strpos($text, '{registerDate')){										
					$regex = '/{(registerDate)\s*(.*?)}/i';				
					$matches = array();
					$preg_set_order = PREG_SET_ORDER;
					preg_match_all($regex, $text, $matches, $preg_set_order); 				
					foreach($matches as $match){  					
						$format = $match[2];	
						$date = $row->registerDate;									
						$date = date_create_from_format('Y-m-d H:i:s', $date);	
						$new = date_format($date, $format);					
						$text = str_replace($match[0], $new, $text);					
					}
				}
				if(strpos($text, '{lastvisitDate')){												
					$regex = '/{(lastvisitDate)\s*(.*?)}/i';				
					$matches = array();
					$preg_set_order = PREG_SET_ORDER;
					preg_match_all($regex, $text, $matches, $preg_set_order); 				
					foreach($matches as $match){  					
						$format = $match[2];	
						$date = $row->lastvisitDate;									
						$date = date_create_from_format('Y-m-d H:i:s', $date);	
						$new = date_format($date, $format);					
						$text = str_replace($match[0], $new, $text);					
					}					
				}
			}
			
		}
		$text = str_replace('{user_id}', $user_id, $text);
		if(strpos($text,'{script ')){
			$regex = '/{(script)\s*(.*?)}/i';				
			$matches = array();
			$preg_set_order = PREG_SET_ORDER;
			preg_match_all($regex, $text, $matches, $preg_set_order); 				
			foreach($matches as $match){  					
				$script_id = $match[2];						
				$new = userprivatepageHelper::get_processed_script($script_id, $user_id);					
				$text = str_replace($match[0], $new, $text);					
			}	
		}
		
		return $text;
	}
	
	public static function get_processed_script($script_id, $user_id){
	
		$db = JFactory::getDBO();
		$html = '';
		
		$query = $db->getQuery(true);
		$query->select('value');
		$query->from('#__userprivatepage_scripts');
		$query->where('id='.$db->q($script_id));		
		$rows = $db->setQuery($query);				
		$rows = $db->loadObjectList();
			
		foreach($rows as $row){		
			$script = $row->value;
			if($script){				
				
				$user_name = '';
				$name = '';
				$email = '';
				$registerDate = '';			
				$lastvisitDate = '';
				
				$query = $db->getQuery(true);
				$query->select('username, name, email, registerDate, lastvisitDate');
				$query->from('#__users');
				$query->where('id='.$db->q($user_id));				
				$rows = $db->setQuery($query);				
				$rows = $db->loadObjectList();
				
				foreach($rows as $row){	
					$user_name = $row->username;					
					$name = $row->name;	
					$email = $row->email;	
					$registerDate = $row->registerDate;		
					$lastvisitDate = $row->lastvisitDate;	
				}				
				
				$script = str_replace('[newline]','
',$script);
				$script = str_replace('[equal]','=',$script);				
				
				$ip = $_SERVER['REMOTE_ADDR'];
				
				$php_to_render = '<?php $user_id = \''.$user_id.'\'; $redirect_url = \'\';';
				$php_to_render .= ' $user_usergroups = '.userprivatepageHelper::get_usergroup_array($user_id).'; ';
				$php_to_render .= ' $user_accesslevels = '.userprivatepageHelper::get_accesslevel_array($user_id).'; ';				
				$php_to_render .= ' $user_name = \''.$name.'\'; ';	
				$php_to_render .= ' $user_username = \''.$user_name.'\'; ';
				$php_to_render .= ' $user_email = \''.$email.'\'; ';	
				$php_to_render .= ' $user_register_date = \''.$registerDate.'\'; ';	
				$php_to_render .= ' $user_lastvisit_date = \''.$lastvisitDate.'\'; ';					
				$php_to_render .= '	$ip = \''.$ip.'\'; ';				
				$php_to_render .= $script;				
				$php_to_render .= ' echo $html; ?>';
				$html = userprivatepageHelper::phpWrapper($php_to_render);					
			}	
		}
		
		return $html;
	}
	
	public static function phpWrapper($content){
	
		$database = JFactory::getDBO();								
		ob_start();
		eval("?>" . $content);
		$content = ob_get_contents();
		ob_end_clean(); 		
		return $content;
	}
	
	public static function get_usergroup_array($user_id){
		
		//get user groups from this user
		jimport( 'joomla.user.helper' );
		$groups = JUserHelper::getUserGroups($user_id);	
		
		//make clean array
		$groups_array = array();		
		for($n = 0; $n < count($groups); $n++){
			$row = each($groups);		
			$groups_array[] = $row['value'];			
		}

		//make string	
		$return = 'array(';		
		$first = 1;		
		for($n = 0; $n < count($groups_array); $n++){		
			if($first){
				$first = 0;
			}else{
				$return .= ',';
			}			
			$return .= $groups_array[$n];			
		}
		$return .= ')';	
		
		return $return;
	}
	
	public static function get_accesslevel_array($user_id){	
	
		//get user levels from this user
		jimport( 'joomla.access.access' );
		$levels = JAccess::getAuthorisedViewLevels($user_id);
		$levels = array_unique($levels);	
		
		//make clean array
		$levels_array = array();		
		for($n = 0; $n < count($levels); $n++){
			$row = each($levels);		
			$levels_array[] = $row['value'];			
		}	
		
		//make string
		$return = 'array(';		
		$first = 1;		
		for($n = 0; $n < count($levels_array); $n++){				
			if($first){
				$first = 0;
			}else{
				$return .= ',';
			}
			$return .= $levels_array[$n];		
		}
		$return .= ')';
		
		return $return;
	}
	
	public static function get_version_type(){
	
		//so that private var is available for templates
		return self::$upp_version_type;		
	}
	
	static public function tab_set_start($id, $active, $cookie, $tabs){
	
		$app = JFactory::getApplication();
		
		$get_tab = JRequest::getVar('tab', '', 'get');		
		if(self::joomla_version() < 3){
			if($cookie){
				$cookie = true;
			}else{
				$cookie = false;
			}
			if($active){
				for($n = 0; $n < count($tabs); $n++){
					if($active==$tabs[$n]){
						$active_index = $n;
					}
				}				
			}
			if($get_tab){
				for($n = 0; $n < count($tabs); $n++){
					if($get_tab==$tabs[$n]){
						$active_index = $n;
					}
				}				
			}
			$options = array(
			'onActive' => 'function(title, description){
				description.setStyle("display", "block");
				title.addClass("open").removeClass("closed");
			}',
			'onBackground' => 'function(title, description){
				description.setStyle("display", "none");
				title.addClass("closed").removeClass("open");
			}',
			'startOffset' => $active_index,  // 0 starts on the first tab, 1 starts the second, etc...
			'useCookie' => $cookie, // this must not be a string. Don't use quotes.
			);
			echo JHtml::_('tabs.start', $id, $options);
		}else{			
			$session = $app->getUserState( "com_userprivatepage.tab_".$id, '');
			if($session!=''){
				$active = $session;
			}				
			if($get_tab && in_array($get_tab, $tabs)){				
				$active = $get_tab;			
			}		
			echo JHtml::_('bootstrap.startTabSet', $id, array('active' => $active));
			if($cookie){				
				$script = '<script>'."\n";
				$script .= 'var JNC_jQuery = jQuery.noConflict();'."\n";
				$script .= 'JNC_jQuery(function($){'."\n";
				$script .= '$(\'#'.$id.'Tabs a\').click(function(e){'."\n";				
				$script .= 'do_tab_session(\''.$id.'\',this.href);'."\n";
				$script .= '});'."\n";	
				$script .= '});'."\n";	
				$script .= '</script>'."\n";
				echo $script;
			}
		}		
	}
	
	static public function tab_add($set, $tab, $label){	
	
		if(self::joomla_version() < 3){
			echo JHtml::_('tabs.panel', $label, $set);
		}else{
			echo JHtml::_('bootstrap.addTab', $set, $tab, JText::_($label, true));//make label javascript save
		}
	}
	
	static public function tab_end(){	
	
		if(self::joomla_version() >= 3){			
			echo JHtml::_('bootstrap.endTab');
		}
	}
	
	static public function tab_set_end(){	
	
		if(self::joomla_version() < 3){
			echo JHtml::_('tabs.end');
		}else{
			echo JHtml::_('bootstrap.endTabSet');
		}
	}
	
	static public function get_smilies(){	
	
		$smilies[] = array('smile',':)');
		$smilies[] = array('wink',';)');
		$smilies[] = array('laughing',':laugh:');
		$smilies[] = array('w00t',':woohoo:');
		$smilies[] = array('wassat',':huh:');
		$smilies[] = array('angry',':angry:');
		$smilies[] = array('blink',':blink:');
		$smilies[] = array('blush',':blush:');
		$smilies[] = array('cheerful',':cheerful:');
		$smilies[] = array('confused',':confused:');
		$smilies[] = array('cool',':cool:');
		$smilies[] = array('devil',':devil:');
		$smilies[] = array('dizzy',':dizzy:');
		$smilies[] = array('ermm',':ermm:');		
		$smilies[] = array('kissing',':kissing:');		
		$smilies[] = array('neutral',':neutral:');
		$smilies[] = array('pinch',':pinch:');
		$smilies[] = array('sad',':sad:');
		$smilies[] = array('shocked',':shocked:');		
		$smilies[] = array('unsure',':unsure:');		
		$smilies[] = array('whistling',':whistling:');		
		
		return $smilies;
	}
	
	static public function process_bbcode($code){
	
		$app = JFactory::getApplication();	
		
		//quotes
		if(userprivatepageHelper::config('bb_quote')){
			if(substr_count($code, '[quote]')==substr_count($code, '[/quote]')){
				//avoid breaking html when there is a mismatch of quote tags
				$code = str_replace('[quote]', '<div class="upp_quote">', $code);
				$code = str_replace('[/quote]', '</div>', $code);
			}	
		}	
		
		//images
		if(userprivatepageHelper::config('bb_image')){
			if(substr_count($code, '[image]')==substr_count($code, '[/image]')){
				//avoid breaking html when there is a mismatch of image tags
				$code = str_replace('[image]', '<div class="upp_image"><img src="', $code);
				$code = str_replace('[/image]', '" /></div>', $code);
			}
		}
		
		//smilies
		$smilies = userprivatepageHelper::get_smilies();
		$path = '';
		if($app->isAdmin()){
			$path = '../';
		}		
		for($n = 0; $n < count($smilies); $n++){
			if(strpos(userprivatepageHelper::config('smilies'), $smilies[$n][0])){				
				$code = str_replace($smilies[$n][1], '<img src="'.$path.'components/com_userprivatepage/images/'.$smilies[$n][0].'.png" title="'.$smilies[$n][0].'" />', $code);
			}
		}		
	
		return $code;	
	}
	
	static public function notify($comment_id, $notify_type, $user_id){
	
		$db = JFactory::getDBO();		
	
		if($notify_type=='notify_user' && userprivatepageHelper::config('notify_user')){
			$subject = userprivatepageHelper::do_tags(userprivatepageHelper::config('subject_user'), $user_id);				
			$query = $db->getQuery(true);
			$query->select('notifymessage');
			$query->from('#__userprivatepage_config');
			$query->where('id='.$db->q('1'));				
			$rows = $db->setQuery($query);					
			$message = userprivatepageHelper::do_tags($db->loadResult(), $user_id);	
			$message = userprivatepageHelper::do_tags_urls($message, $user_id, $comment_id, 0);	
			userprivatepageHelper::do_send_notification($message, $subject, $user_id);				
		}
		
		if($notify_type=='admin_add' && userprivatepageHelper::config('notify_admin_mail')){
			$subject = userprivatepageHelper::do_tags(userprivatepageHelper::config('subject_add'), $user_id);				
			$query = $db->getQuery(true);
			$query->select('notifymessage_admin_add');
			$query->from('#__userprivatepage_config');
			$query->where('id='.$db->q('1'));				
			$rows = $db->setQuery($query);					
			$message = userprivatepageHelper::do_tags($db->loadResult(), $user_id);	
			$message = userprivatepageHelper::do_tags_urls($message, $user_id, $comment_id, 1);				
			userprivatepageHelper::do_send_notification($message, $subject, 'administrators');		
		}
		
		if($notify_type=='admin_add' && userprivatepageHelper::config('notify_admin_messaging')){
			$subject = userprivatepageHelper::do_tags(userprivatepageHelper::config('subject_add'), $user_id);				
			$query = $db->getQuery(true);
			$query->select('notifymessage_admin_add');
			$query->from('#__userprivatepage_config');
			$query->where('id='.$db->q('1'));				
			$rows = $db->setQuery($query);					
			$message = userprivatepageHelper::do_tags($db->loadResult(), $user_id);	
			$message = userprivatepageHelper::do_tags_urls($message, $user_id, $comment_id, 1);				
			userprivatepageHelper::do_send_notification_system($message, $subject);		
		}
		
		if($notify_type=='admin_edit' && userprivatepageHelper::config('notify_admin_mail_edit')){
			$subject = userprivatepageHelper::do_tags(userprivatepageHelper::config('subject_edit'), $user_id);				
			$query = $db->getQuery(true);
			$query->select('notifymessage_admin_edit');
			$query->from('#__userprivatepage_config');
			$query->where('id='.$db->q('1'));				
			$rows = $db->setQuery($query);					
			$message = userprivatepageHelper::do_tags($db->loadResult(), $user_id);	
			$message = userprivatepageHelper::do_tags_urls($message, $user_id, $comment_id, 1);				
			userprivatepageHelper::do_send_notification($message, $subject, 'administrators');		
		}
		
		if($notify_type=='admin_edit' && userprivatepageHelper::config('notify_admin_messaging_edit')){
			$subject = userprivatepageHelper::do_tags(userprivatepageHelper::config('subject_edit'), $user_id);				
			$query = $db->getQuery(true);
			$query->select('notifymessage_admin_edit');
			$query->from('#__userprivatepage_config');
			$query->where('id='.$db->q('1'));				
			$rows = $db->setQuery($query);					
			$message = userprivatepageHelper::do_tags($db->loadResult(), $user_id);	
			$message = userprivatepageHelper::do_tags_urls($message, $user_id, $comment_id, 1);				
			userprivatepageHelper::do_send_notification_system($message, $subject);			
		}	
		
		return true;		
	}
	
	static public function do_tags_urls($message, $user_id, $comment_id, $backend=1){
	
		$uri = JFactory::getURI();
		$url_to_private_page = $uri->root();
		if($backend){
			$url_to_private_page .= 'administrator/index.php?option=com_userprivatepage&view=usercomments&user_id='.$user_id;
		}else{			
			$url_to_private_page .= 'index.php?option=com_userprivatepage&view=userpage';					
		}		
		$message = str_replace('{url_to_private_page}', $url_to_private_page, $message);
		
		$url_to_comment = $url_to_private_page.'&comment='.$comment_id.'#comment'.$comment_id;
		$message = str_replace('{url_to_comment}', $url_to_comment, $message);
		
		return $message;	
	}
	
	static public function do_send_notification($message, $subject, $to){			
	
		$mailer = JFactory::getMailer();		
		$config = JFactory::getConfig();
		$sender = array($config->get('mailfrom'), $config->get('fromname'));		 
		$mailer->setSender($sender);	
		if($to=='administrators'){
			//add all configured admins	
			$admins = explode(',', userprivatepageHelper::config('notify_admins'));
			$emails = array();
			for($n = 0; $n < count($admins); $n++){
				$temp_id = trim($admins[$n]);
				$emails[] = userprivatepageHelper::get_email_from_id($temp_id);				
			}
			$mailer->addRecipient($emails);				
		}else{
			//to user, so get email from id
			$email = userprivatepageHelper::get_email_from_id($to);
			$mailer->addRecipient($email);
		}			
		$mailer->setSubject($subject);
		$mailer->isHTML(true);
		$mailer->setBody($message);
		$send = $mailer->Send();
		
		return true;	
	}
	
	static public function get_email_from_id($user_id){			
	
		$db = JFactory::getDBO();
		
		$email = '';
		$query = $db->getQuery(true);
		$query->select('email');
		$query->from('#__users');
		$query->where('id='.$db->q($user_id));		
		$rows = $db->setQuery($query);				
		$rows = $db->loadObjectList();			
		foreach($rows as $row){		
			$email = $row->email;	
		}
		
		return $email;	
	}
	
	static public function do_send_notification_system($message, $subject){
	
		$db = JFactory::getDBO();
		
		$date = JFactory::getDate();		
		$now = $date->toSql();		
		$admins = explode(',', userprivatepageHelper::config('notify_admins'));		
		for($n = 0; $n < count($admins); $n++){
			$temp_id = trim($admins[$n]);
			
			//insert message
			$query = $db->getQuery(true);
			$query->insert('#__messages');
			$query->set('user_id_from='.$db->q($temp_id));
			$query->set('user_id_to='.$db->q($temp_id));
			$query->set('date_time='.$db->q($now));
			$query->set('subject='.$db->q($subject));
			$query->set('message='.$db->q($message));			
			$db->setQuery((string)$query);
			$db->query();			
		}	
	
		return true;
	}
	
	
	
	
	
	
}
?>
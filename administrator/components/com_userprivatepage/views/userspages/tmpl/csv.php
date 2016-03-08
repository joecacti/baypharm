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

$delimiter = ',';
$quote = '"';
$newline = "\r\n";
$break = "
";

$out = '';

$out .= $quote.'id'.$quote;	
$out .= $delimiter;
$out .= $quote.'username'.$quote;
$out .= $delimiter;
$out .= $quote.'name'.$quote;
$out .= $delimiter;
$out .= $quote.'email'.$quote;
$out .= $delimiter;
$out .= $quote.'text (code)'.$quote;
$out .= $delimiter;
$out .= $quote.'text (processed code)'.$quote;
$out .= $delimiter;
$out .= $quote.'title (code)'.$quote;
$out .= $delimiter;
$out .= $quote.'title (processed code)'.$quote;
$out .= $delimiter;	
$out .= $quote.'block'.$quote;
$out .= $delimiter;
$out .= $quote.'sendEmail'.$quote;
$out .= $delimiter;
$out .= $quote.'registerDate'.$quote;
$out .= $delimiter;
$out .= $quote.'lastvisitDate'.$quote;
$out .= $delimiter;
$out .= $quote.'users_groups'.$quote;	
$out .= $delimiter;
$out .= $quote.'users_groups_ids'.$quote;	
$out .= $delimiter;
$out .= $quote.'users_levels'.$quote;	
$out .= $delimiter;
$out .= $quote.'users_levels_ids'.$quote;						
$out .= $newline;

for($i=0; $i < count( $this->items ); $i++) {
	$row = $this->items[$i];		
	
	$out .= $quote.$row->id.$quote;	
	$out .= $delimiter;
	$out .= $quote.$row->username.$quote;
	$out .= $delimiter;
	$out .= $quote.$row->name.$quote;
	$out .= $delimiter;
	$out .= $quote.$row->email.$quote;
	$out .= $delimiter;	
	if(isset($this->user_text_index[$row->id])){
		$text = $this->user_text_index[$row->id][0];
	}else{
		$text = $this->text_when_empty;
	}	
	$text = str_replace(',', ';', $text);//else it messes up the csv	
	$out .= $quote.$text.$quote;
	$out .= $delimiter;	
	$text_processed = userprivatepageHelper::do_tags($text, $row->id);
	$text_processed = JHTML::_('content.prepare', $text_processed);
	$out .= $quote.$text_processed.$quote;
	$out .= $delimiter;
	if(isset($this->user_text_index[$row->id])){
		$title = $this->user_text_index[$row->id][1];
		$title_processed = userprivatepageHelper::do_tags($title, $row->id);
	}else{
		$title = '';
		$title_processed = '';
	}
	$out .= $quote.$title.$quote;
	$out .= $delimiter;	
	$out .= $quote.$title_processed.$quote;
	$out .= $delimiter;		
	$out .= $quote.$row->block.$quote;
	$out .= $delimiter;
	$out .= $quote.$row->sendEmail.$quote;
	$out .= $delimiter;
	$out .= $quote.$row->registerDate.$quote;
	$out .= $delimiter;
	$out .= $quote.$row->lastvisitDate.$quote;
	$out .= $delimiter;
			
	$users_groups_string = '';
	$users_groups_ids_string = '';
	$group_ids_array = $this->get_users_groups($row->id);									
	foreach($this->groups_title_order as $temp){
		if(in_array($temp[0], $group_ids_array)){
			if($users_groups_string!=''){
				$users_groups_string .= ',';
				$users_groups_ids_string .= ',';
			}
			$users_groups_string .= $temp[1];
			$users_groups_ids_string .= $temp[0];
		}
	}				
		
	$out .= $quote.$users_groups_string.$quote;	
	$out .= $delimiter;
	$out .= $quote.$users_groups_ids_string.$quote;	
	$out .= $delimiter;
	
	$users_levels_string = '';
	$users_levels_ids_string = '';
	$levels_ids_array = $this->get_groups_levels($group_ids_array);
						
	foreach($this->levels_title_order as $temp){
		if(in_array($temp->level_id, $levels_ids_array)){			
			if($users_levels_string!=''){
				$users_levels_string .= ',';
				$users_levels_ids_string .= ',';
			}
			$users_levels_string .= $temp->level_title;
			$users_levels_ids_string .= $temp->level_id;
		}
	}
	$out .= $quote.$users_levels_string.$quote;	
	$out .= $delimiter;
	$out .= $quote.$users_levels_ids_string.$quote;					
					
	$out .= $newline;
}
//echo $out;
//exit;
$out = chr(255).chr(254).mb_convert_encoding( $out, 'UTF-16LE', 'UTF-8');

@ob_end_clean();
$file_name = 'users_export'.date('YmdHis').'.csv';
@ini_set("zlib.output_compression", "Off");
header("Content-Type: text/comma-separated-values; charset=utf-8");
header("Content-Disposition: attachment;filename=\"$file_name\"");
header("Content-Transfer-Encoding: 8bit");
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: private");
header("Content-Length: ".strlen($out));
echo $out;
exit;


?>
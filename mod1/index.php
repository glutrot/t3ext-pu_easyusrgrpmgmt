<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Patric Ueschner <news4patric@freenet.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Module 'UserGroupMgmt' for the 'pu_easyusrgrpmgmt' extension.
 *
 * @author	Patric Ueschner <news4patric@freenet.de>
 */



	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require ("conf.php");
require ($BACK_PATH."init.php");
require ($BACK_PATH."template.php");
$LANG->includeLLFile("EXT:pu_easyusrgrpmgmt/mod1/locallang.xml");
require_once (PATH_t3lib."class.t3lib_scbase.php");
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]

class tx_pueasyusrgrpmgmt_module1 extends t3lib_SCbase {
	var $pageinfo;

	/**
	 * Initializes the Module
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		parent::init();

		/*
		if (t3lib_div::_GP("clear_all_cache"))	{
			$this->include_once[]=PATH_t3lib."class.t3lib_tcemain.php";
		}
		*/
	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	function menuConfig()	{
		global $LANG;
		$this->MOD_MENU = Array (
			"function" => Array (
				"1" => $LANG->getLL("function1"),
			)
		);
		parent::menuConfig();
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 *
	 * @return	[type]		...
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		if (($this->id && $access) || ($BE_USER->user["admin"] && !$this->id))	{

				// Draw the header.
			$this->doc = t3lib_div::makeInstance("mediumDoc");
			$this->doc->backPath = $BACK_PATH;
			$this->doc->form='<form action="" method="POST">';

				// JavaScript
			$this->doc->JScode = '
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						document.location = URL;
					}
					
					function markCheckbox(current,currObj,checkBoxes,option) {
						obj = document.getElementsByName(currObj);
						checkBoxes = document.getElementsByName(checkBoxes);
						if(option == 0) {
							if(current.checked == true) {
								for(var i = 0; i < obj.length; i++) {
									obj[i].checked = false;
								}
							} else {
								for(var i = 0; i < obj.length; i++) {
									if(obj[i].checked == false) {
										current.checked = false;
										obj[i].checked = true;
									}
								}
							}
						} else if(option == 1) {
							if(current.checked == true) {
								for(var i = 0; i < obj.length; i++) {
									current.checked = true;
									obj[i].checked = false;
								}
							} else {
								for(var i = 0; i < obj.length; i++) {
									checked_count = checkBoxes.length;
									for(var j = 0; j < checkBoxes.length; j++) {
										if(checkBoxes[j].checked == false){
											checked_count--;
										}
									}
									if(checked_count == 0)
										obj[i].checked = true;
								}
							}
						} else if(option == 2) {
							for(var i = 0; i < checkBoxes.length; i++) {
								checkBoxes[i].checked = true;
							}
						}
					}
				</script>
			';
			$this->doc->postCode='
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = 0;
				</script>
			';

			$headerSection = $this->doc->getHeader("pages",$this->pageinfo,$this->pageinfo["_thePath"])."<br />".$LANG->sL("LLL:EXT:lang/locallang_core.xml:labels.path").": ".t3lib_div::fixed_lgd_pre($this->pageinfo["_thePath"],50);

			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->section("",$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,"SET[function]",$this->MOD_SETTINGS["function"],$this->MOD_MENU["function"])));
			$this->content.=$this->doc->divider(5);

			// Render content:
			$this->moduleContent();

			$this->content.=$this->doc->spacer(10);
		} else {
				// If no access or if ID == zero

			$this->doc = t3lib_div::makeInstance("mediumDoc");
			$this->doc->backPath = $BACK_PATH;

			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	void
	 */
	function printContent()	{

		$this->content.=$this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Generates the module content
	 *
	 * @return	void
	 */
	function moduleContent()	{
		global $LANG;
		switch((string)$this->MOD_SETTINGS["function"])	{
			case 1:
				if($_POST['SET']['function'] == 1 && $_POST['SET']['submit'] == $LANG->getLL("submit_button")) {
					$this->updateUserGroupAssignment($_POST['assign'], $_POST['update_only'], $this->id);
				}
				$fe_users = $this->getUserGroupData('fe_users','uid,username,name,usergroup',$this->id);
				$fe_groups = $this->getUserGroupData('fe_groups','uid,title',$this->id);
				$content = $this->addStats(array('fe_groups' => count($fe_groups),'fe_users' => count($fe_users)));
				if($fe_users && $fe_groups) {
					$content .= $this->genUserGroupMatrix($fe_users,$fe_groups);
				} else {
					$content .= $LANG->getLL("no_feusers_groups").'<br/><br/>';
				}
				$content .= $this->addCreateLinks(array('fe_groups','fe_users'));
				$this->content.=$this->doc->section($LANG->getLL("user_group_matrix"),$content,0,1);
			break;
		}
	}
	
	function genUserGroupMatrix($userArray, $groupArray) {
		global $LANG,$BACK_PATH;
		$content = '<form name="pu_easyusrgrpmgmt_form" method="POST"><table border="0" cellpadding="5" cellspacing="1" class="usrgrptable">
						<thead><tr>
							<th class="header">&nbsp;</th>';
		for($i=0;$i<count($groupArray);$i++) {
			$content .= '<th class="header" nowrap>'.$groupArray[$i]['title'].$this->addEditLink($groupArray[$i], 'fe_groups').'</th>';
		}
		$content .= '<th class="header_a"><img src="'.$BACK_PATH.t3lib_extMgm::extRelPath('pu_easyusrgrpmgmt').$LANG->getLL("remove_from_groups_img").'" alt="remove" title="'.$LANG->getLL("remove_from_groups").'"></th>
					 <th class="header_a"><img src="'.$BACK_PATH.t3lib_extMgm::extRelPath('pu_easyusrgrpmgmt').$LANG->getLL("update_only_img").'" alt="update" title="'.$LANG->getLL("update_only").'"></th>
				</tr></thead><tbody>';
		for($j=0;$j<count($userArray);$j++) {
			$class = ($j%2!=0)?'td1':'td2';
			$usergroup_joined = explode(',',$userArray[$j]['usergroup']);
			$content .= '<tr>
							<td class="'.$class.'_a" nowrap>'.(!empty($userArray[$j]['name'])?$userArray[$j]['name'].$this->addEditLink($userArray[$j], 'fe_users'):
							$userArray[$j]['username'].$this->addEditLink($userArray[$j], 'fe_users').' <span style="font-size:8;font-style:italic;">[username]</span>').'</td>';
			for($i=0;$i<count($groupArray);$i++) {
				$checked = '';
				$usergroup_joined = explode(',',$userArray[$j]['usergroup']);
				if(in_array($groupArray[$i]['uid'],$usergroup_joined)) {
					$checked = ' checked="checked"';
				}
				$content .= '<td class="'.$class.'">
								<input onClick="markCheckbox(this,\'assign['.$userArray[$j]['uid'].']\',\'assign['.$userArray[$j]['uid'].'][]\',1);
                   markCheckbox(this,\'assign['.$userArray[$j]['uid'].']\',\'update_only['.$userArray[$j]['uid'].']\',2);" 
                   type="checkbox"'.$checked.' name="assign['.$userArray[$j]['uid'].'][]" value="'.$groupArray[$i]['uid'].'">
							</td>';
			}
			$checked = '';
			if(empty($userArray[$j]['usergroup'])) {
				$checked = ' checked="checked"';
			}
			$content .= '<td class="'.$class.'"><input onClick="markCheckbox(this,\'assign['.$userArray[$j]['uid'].'][]\',\'assign['.$userArray[$j]['uid'].']\',0);
                markCheckbox(this,\'assign['.$userArray[$j]['uid'].'][]\',\'update_only['.$userArray[$j]['uid'].']\',2);" 
							type="checkbox"'.$checked.' name="assign['.$userArray[$j]['uid'].']" value=""></td>
						<td class="'.$class.'"><input type="checkbox" name="update_only['.$userArray[$j]['uid'].']" value="'.$userArray[$j]['uid'].'"></td>
					</tr>';
		}
		$content .= '</tbody><tfoot><tr>
						<td class="footer" colspan="'.(count($groupArray)+3).'"><input type="submit" name="SET[submit]" value="'.$LANG->getLL("submit_button").'"> 
						<input type="reset" name="SET[reset]" value="'.$LANG->getLL("reset_button").'"></td>
					</tr></tfoot>';
		$content .= '</table></form><br/>';
		return $content;
	}
	
	function getUserGroupData($table, $fields, $pid=0) {
		$output = array();
		$where = 'pid="'.$pid.'" AND NOT deleted';
		$orderby = ($table == 'fe_users')?'name,username':'title';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $table, $where,'',$orderby);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$output[] = $row;
		}
		return $output;
	}
	
	function updateUserGroupAssignment($data, $updonly, $pid=0) {
		if(is_array($data)) {
			foreach ($data as $key => $value) {
				if(!in_array($key, $updonly) && !empty($updonly))
					continue;
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid','fe_users','uid="'.$key.'" AND pid="'.$pid.'"');
				if(count($res) == 1) {
					$fe_groups_uids = array();
					foreach($value as $v) {
						$res = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid','fe_groups','uid="'.$v.'" AND pid="'.$pid.'"');
						if(count($res) == 1) {
							$fe_groups_uids[] = $v;
						}
					}
					$fe_groups_uids = array_unique($fe_groups_uids);
					$updateArray = array('usergroup' => implode(',',$fe_groups_uids));
					$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid="'.$key.'" AND pid="'.$pid.'"', $updateArray);
				}
			}
			return true;
		}
		return false;
	}
	
	function addEditLink($dataArray, $option) {
		global $BACK_PATH, $LANG;
		$titleTag = ($option == 'fe_users')?((!empty($dataArray['name'])?$dataArray['name']:$dataArray['username'])):($dataArray['title']);
		$link = '<a href="#" onClick="jumpToUrl(\''.$BACK_PATH.'alt_doc.php?returnUrl='.
					t3lib_extMgm::extRelPath('pu_easyusrgrpmgmt').'mod1/index.php%3Fid%3D'.$this->id.
					'&edit['.$option.']['.$dataArray['uid'].']=edit\');">
							<img src="'.$BACK_PATH.t3lib_extMgm::extRelPath('pu_easyusrgrpmgmt').$LANG->getLL("edit_link_img").'" alt="edit" 
							 title="'.$LANG->getLL("edit_link_".$option).' '.$titleTag.'">
						</a>';
		return $link;
	}
		
	function addCreateLinks($options=array()) {
		global $BACK_PATH, $LANG;
		$links = array();
		foreach($options as $option) {
			$links[] = '<a href="#" onClick="jumpToUrl(\''.$BACK_PATH.'alt_doc.php?returnUrl='.
						t3lib_extMgm::extRelPath('pu_easyusrgrpmgmt').'mod1/index.php%3Fid%3D'.$this->id.
						'&edit['.$option.']['.$this->id.']=new\');">
								<img src="'.$BACK_PATH.t3lib_extMgm::extRelPath('pu_easyusrgrpmgmt').$LANG->getLL("create_link_img").'" alt="create" 
								 title="'.$LANG->getLL("create_link_".$option).'"> '.$LANG->getLL("create_link_".$option).'</a>';
		}
		return implode('<br/>', $links);
	}

	function addStats($dataArray=array()) {
		global $LANG;
		$stats = array();
		foreach($dataArray as $key => $value) {
			$stats[] = $LANG->getLL("count_datarows_".$key).' <b>'.$value.'</b>';
		}
		return implode('<br/>', $stats).'<br/><br/>';
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pu_easyusrgrpmgmt/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pu_easyusrgrpmgmt/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_pueasyusrgrpmgmt_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>

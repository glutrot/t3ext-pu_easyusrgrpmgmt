<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=="BE")	{
		
	$TBE_STYLES['stylesheet2'] = t3lib_extMgm::extRelPath($_EXTKEY).'mod1/lib/styles.css';
	
	t3lib_extMgm::addModule("web","txpueasyusrgrpmgmtM1","",t3lib_extMgm::extPath($_EXTKEY)."mod1/");
}
?>
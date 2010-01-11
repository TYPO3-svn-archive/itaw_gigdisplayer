<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::allowTableOnStandardPages("tx_itawgigdisplayer_main");


t3lib_extMgm::addToInsertRecords("tx_itawgigdisplayer_main");

$TCA["tx_itawgigdisplayer_main"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:itaw_gigdisplayer/locallang_db.xml:tx_itawgigdisplayer_main',
		'label' => 'date',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'versioningWS' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		"default_sortby" => "ORDER BY crdate DESC",
		"delete" => "deleted",
		"enablecolumns" => Array (
			"disabled" => "hidden",
			"fe_group" => "fe_group",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_itawgigdisplayer_main.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, fe_group, date, location, location_address, location_city, info, flyer",
	)
);


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'pi_flexform';

t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1','FILE:EXT:'.$_EXTKEY.'/flexform_ds.xml');
t3lib_extMgm::addPlugin(Array('LLL:EXT:itaw_gigdisplayer/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');


t3lib_extMgm::addStaticFile($_EXTKEY,'pi1/static/','ITAW Gigdisplayer');
?>

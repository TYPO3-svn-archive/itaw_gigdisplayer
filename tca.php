<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_itawgigdisplayer_main"] = Array (
	"ctrl" => $TCA["tx_itawgigdisplayer_main"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,fe_group,date,location,location_address,country,location_city,url,info,flyer"
	),
	"feInterface" => $TCA["tx_itawgigdisplayer_main"]["feInterface"],
	"columns" => Array (
		'sys_language_uid' => array (		
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages',-1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value',0)
				)
			)
		),
		'l18n_parent' => Array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
				),
				'foreign_table' => 'tx_itawgigdisplayer_main',
				'foreign_table_where' => 'AND tx_itawgigdisplayer_main.pid=###CURRENT_PID### AND tx_itawgigdisplayer_main.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => Array (		
			'config' => Array (
				'type' => 'passthrough'
			)
		),
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"fe_group" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.fe_group",
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("", 0),
					Array("LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login", -1),
					Array("LLL:EXT:lang/locallang_general.xml:LGL.any_login", -2),
					Array("LLL:EXT:lang/locallang_general.xml:LGL.usergroups", "--div--")
				),
				"foreign_table" => "fe_groups"
			)
		),
		"date" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:itaw_gigdisplayer/locallang_db.xml:tx_itawgigdisplayer_main.date",		
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"checkbox" => "0",
				"default" => "0"
			)
		),
		"location" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:itaw_gigdisplayer/locallang_db.xml:tx_itawgigdisplayer_main.location",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required",
			)
		),
		"location_address" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:itaw_gigdisplayer/locallang_db.xml:tx_itawgigdisplayer_main.location_address",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"country" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:itaw_gigdisplayer/locallang_db.xml:tx_itawgigdisplayer_main.country",		
			"config" => Array (
				"type" => "input",	
				"size" => "5",	
				"max" => "3",
			)
		),
		"location_city" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:itaw_gigdisplayer/locallang_db.xml:tx_itawgigdisplayer_main.location_city",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"url" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:itaw_gigdisplayer/locallang_db.xml:tx_itawgigdisplayer_main.url",		
			"config" => Array (
				"type" => "input",
				"size" => "15",
				"max" => "255",
				"checkbox" => "",
				"eval" => "trim",
				"wizards" => Array(
					"_PADDING" => 2,
					"link" => Array(
						"type" => "popup",
						"title" => "Link",
						"icon" => "link_popup.gif",
						"script" => "browse_links.php?mode=wizard",
						"JSopenParams" => "height=300,width=500,status=0,menubar=0,scrollbars=1"
					)
				)
			)
		),
		"info" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:itaw_gigdisplayer/locallang_db.xml:tx_itawgigdisplayer_main.info",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => Array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
		"flyer" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:itaw_gigdisplayer/locallang_db.xml:tx_itawgigdisplayer_main.flyer",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => $GLOBALS["TYPO3_CONF_VARS"]["GFX"]["imagefile_ext"],	
				"max_size" => 500,	
				"uploadfolder" => "uploads/tx_itawgigdisplayer",
				"show_thumbs" => 1,	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, date, location, location_address, country, location_city, url, info;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts], flyer")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "fe_group")
	)
);
?>
<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
$tempColumns = Array (
	"tx_rest_restkey" => Array (
		"exclude" => 1,
		"label" => "LLL:EXT:rest/locallang_db.xml:be_users.tx_rest_restkey",
		"config" => Array (
			"type" => "input",
			"size" => "30",
			"checkbox" => "",
			"eval" => "trim,password",
		)
	),
);


t3lib_div::loadTCA("be_users");
t3lib_extMgm::addTCAcolumns("be_users",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes("be_users","tx_rest_restkey;;;;1-1-1");

$tempColumns = Array (
	"tx_rest_restkey" => Array (
		"exclude" => 1,
		"label" => "LLL:EXT:rest/locallang_db.xml:fe_users.tx_rest_restkey",
		"config" => Array (
			"type" => "input",
			"size" => "30",
			"checkbox" => "",
			"eval" => "trim,password",
		)
	),
);


t3lib_div::loadTCA("fe_users");
t3lib_extMgm::addTCAcolumns("fe_users",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes("fe_users","tx_rest_restkey;;;;1-1-1");

?>
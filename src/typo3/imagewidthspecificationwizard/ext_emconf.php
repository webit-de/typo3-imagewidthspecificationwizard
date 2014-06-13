<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "imagewidthspecificationwizard".
 *
 * Auto generated 13-06-2014 15:17
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Wizard to specify the field imagewidth in contentelements',
	'description' => 'This backendextension attaches a selectfield to contentelements like textpic or images to provide a set of default values for the width of an image (eg.: fullsize, half of the content, small teaser etc.).',
	'category' => 'be',
	'shy' => 0,
	'version' => '0.3.0',
	'dependencies' => 'cms',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Dan Untenzu',
	'author_email' => 'untenzu@webit.de',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.5.0-6.2.99',
			'cms' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:9:{s:9:"ChangeLog";s:4:"47ce";s:49:"class.tx_imagewidthspecificationwizard_wizard.php";s:4:"cdb8";s:12:"ext_icon.gif";s:4:"ee32";s:17:"ext_localconf.php";s:4:"1e5e";s:14:"ext_tables.php";s:4:"a9f9";s:13:"locallang.xml";s:4:"06be";s:16:"pageTSconfig.txt";s:4:"f1bb";s:14:"doc/manual.pdf";s:4:"b0f2";s:14:"doc/manual.sxw";s:4:"8bec";}',
	'suggests' => array(
	),
);

?>
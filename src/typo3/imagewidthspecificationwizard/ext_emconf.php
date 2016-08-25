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
	'title' => 'Wizard for imagewidth field',
	'description' => 'This backendextension attaches a selectfield to contentelements like textpic or images to provide a set of default values for the width of an image (eg.: fullsize, half of the content, small teaser etc.).',
	'category' => 'be',
	'author' => 'Dan Untenzu',
	'author_email' => 'untenzu@webit.de',
	'author_company' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'clearCacheOnLoad' => 1,
	'version' => '0.3.1',
	'constraints' => array(
		'depends' => array(
			'typo3' => '6.2.0-6.2.99',
			'cms' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);

?>
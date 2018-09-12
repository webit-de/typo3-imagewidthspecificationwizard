<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Create wizard configuration
$wizardConfig = array(
    'type' => 'userFunc',
    'userFunc' => 'WebitDe\Imagewidthspecificationwizard\UserFunctions\ImageWidthSpecificationWizard->main'
);

$TCA['tt_content']['columns']['imagewidth']['config']['wizards']['tx_imagewidthspecificationwizard_widthselection'] = $wizardConfig;

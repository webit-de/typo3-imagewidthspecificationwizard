<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(function () {
    // Create wizard configuration
    $GLOBALS['TCA']['tt_content']['columns']['imagewidth']['config']['wizards']['tx_imagewidthspecificationwizard_widthselection'] = [
        'type' => 'userFunc',
        'userFunc' => 'WebitDe\Imagewidthspecificationwizard\UserFunctions\ImageWidthSpecificationWizard->main'
    ];
});

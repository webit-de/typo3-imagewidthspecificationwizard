<?php

namespace WebitDe\Imagewidthspecificationwizard\UserFunctions;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) Dan Untenzu <untenzu@webit.de>, webit! Gesellschaft für neue Medien mbH
 *  (c) 2018 Lidia Demin <demin@webit.de>, webit! Gesellschaft für neue Medien mbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class which adds a wizard to image width field of the tt_content table
 *
 * @author Dan Untenzu <untenzu@webit.de>
 * @package TYPO3
 * @subpackage tx_imagewidthspecificationwizard
 */
class ImageWidthSpecificationWizard
{
    public $extKey = 'imagewidthspecificationwizard'; // The extension key.

    /**
     * Remember whether the current imagewidth eqals one of the preconfigured sizes
     * @var bool
     */
    protected $fieldsMatch = false;

    /**
     * Generate the HTML-code for the wizard attached next to every imagewidth-field within
     * the TYPO3 backend
     *
     * @param array Parameter array for 'userFunc' wizard type
     * @param object Parent object
     *
     * @return string Returns HTML for the wizard
     */
    public function main(&$params, &$pObj)
    {
        // fetch TSconfig/UserTSConfig for current page
        $modTSconfig = BackendUtility::getModTSconfig($params['row']['pid'], 'tx_' . $this->extKey);
        ksort($modTSconfig['properties']['sizes.'], SORT_NUMERIC);

        // generate a unique id for the wizard field
        $collide = substr(md5($params['md5ID'] . 'wizard_' . $this->extKey), 0, 10);

        $this->fieldsMatch = false;
        $options = $this->getOptions($modTSconfig, $params['row']['imagewidth']);
        // force script to hide the image width field if this is defined in TSconfig
        if (true === $modTSconfig['properties']['ownValueDisabled']) {
            $modTSconfig['properties']['hideFieldOnMatch'] = true;
            $this->fieldsMatch = true;
        }
        $JSonchange = $this->getJSonchange(
            $params['row']['uid'],
            $params['itemName'],
            $modTSconfig['properties']['hideFieldOnMatch'],
            $modTSconfig['properties']['ownValueDisabled']
        );

        $content = $this->getSelectfield(
            $params['itemName'],
            $collide,
            $JSonchange,
            $options
        );

        $JSpost = $this->getJSpost(
            $params['itemName'],
            $modTSconfig['properties']['hideFieldOnMatch'],
            $this->fieldsMatch
        );
        $content .= '<script>' . $JSpost . '</script>';

        return $content;
    }

    /**
     * Generate option tags for select field with values defined in TSconfig
     *
     * @param array The TSconfig of the current page
     * @param string The current image width of the content element
     *
     * @return string HTML string with all needed option tags of the select field
     */
    public function getOptions($modTSconfig, $imagewidth)
    {
        $options = '';

        if (true === (empty($modTSconfig['properties']))) {
            $options = '<option value="--div--">'
                . LocalizationUtility::translate('tt_content.tx_imagewidthspecificationwizard.configurationneeded', $this->extKey)
                . '</option>';

            return $options;
        }

        // generate list of predefined values
        foreach ($modTSconfig['properties']['sizes.'] as $size => $description) {
            $selected = '';
            // option is »selected« if field image width equals current size in loop
            if ($size === $imagewidth) {
                $this->fieldsMatch = true;
                $selected = ' selected="selected"';
            }

            $options .= '<option value="' . $size . '" ' . $selected . '>' .
                $this->getLabel($description) . '</option>';
        }

        // prepend option to use an individual value (»--div--« is working as a flag for JavaScript)
        if (false === $modTSconfig['properties']['ownValueDisabled'] ||
            true === $modTSconfig['properties']['ownValueDisabled'] &&
            (false === $this->fieldsMatch && !empty($imagewidth))) {
            $selected = (false === $this->fieldsMatch && !empty($imagewidth)) ? ' selected="selected"' : '';
            $options = '<option value="--div--"' . $selected . '>'
                . $this->getLabel($modTSconfig['properties']['ownValueLabel'])
                . '</option>' . $options;
        }
        // prepend option to use no value / clear the field imagewidth (»0« triggers the JavaScript the clear the field automatically)
        if (false === $modTSconfig['properties']['noValueDisabled']) {
            $options = '<option value="0">'
                . $this->getLabel($modTSconfig['properties']['noValueLabel'])
                . '</option>' . $options;
        }

        $this->fieldsMatch = (false === $this->fieldsMatch && empty($imagewidth)) ? true : $this->fieldsMatch;

        return $options;
    }

    /**
     * Generate JavaScript for the onchange-handler of the select field
     * The field image width is supposed to be change every time a the user is selecting
     * a different value in the wizard
     *
     * @param string The uid of the current content element
     * @param string The distinct name of the image width field in the current content element
     * @param bool Hide the image width field if a configured value/width is selected (TSconfig)
     * @param bool Allow the possibility to use an individual value for the image width
     *
     * @return string Returns the valid JavaScript for the onchange attribute
     */
    public function getJSonchange($uid, $fieldName, $hideField, $ownValueDisabled)
    {
        // Double escape JavaScript since its located inside of HTML-tag attributes wraped with quotes
        $content =
            // user has selected something else than "own value" (flag --div--) → change
            // the value of the imagewidth field
            'if (this.options[this.selectedIndex].value != \'--div--\') {
                document.editform[\'' . $fieldName . '\'].value = this.options[this.selectedIndex].value;' .
            // if configuration says so then the imagewidth field is shielded
            ($hideField ?
                'document.getElementsByName(\'' . $fieldName . '\')[0].parentNode.style.display = \'none\';'
                : ''
            ) .
            '}' .
            // if configuration allows own values, than the original imagewidth field is restored
            ((!$ownValueDisabled) ?
                'else {
                    document.getElementsByName(\'' . $fieldName . '\')[0].parentNode.style.display = \'inline\';
                }' : ''
            ) .
            'typo3form.fieldGet(\'data[tt_content][' . $uid . '][imagewidth]\', \'int\', \'\', 1, \'0\');
            TBE_EDITOR.fieldChanged(\'tt_content\',\'' . $uid . '\', \'imagewidth\', \'data[tt_content][' . $uid . '][imagewidth]\');
            ';

        return $content;
    }

    /**
     * Generate the HTML for the select field
     *
     * @param string The distinct name of the image width field in the current content element
     * @param string Unique id for the wizard field
     * @param string JavaScript for the onchange-attribute of the select field
     * @param string HTML string containing all options of the select field
     *
     * @return string HTML of the wizards select field
     */
    public function getSelectfield($fieldName, $collide, $JSonchange, $options)
    {
        $content = '<select onchange="' . $JSonchange . '"' .
            ' name="_WIZARD' . $fieldName . '"' .
            ' class="tceforms-select tceforms-wizardselect"' .
            ' id="tceforms-select-' . $collide . '">' .
            $options .
            '</select>';

        return $content;
    }

    /**
     * Hide the field image width if TSconfig option »hideFieldOnMatch« equals true and
     * the current image width equals one of the preconfigured sizes
     *
     * @param string The distinct name of the image width field in the current content element
     * @param bool Hide the image width field if a given value/width is selected (TSconfig)
     *
     * @return string Returns the JavaScript for TYPO3 $additionalJS_post (appended to form)
     */
    public function getJSpost($fieldName, $hideField)
    {
        $content = '';
        if ($hideField && $this->fieldsMatch) {
            $content = 'document.getElementsByName(\'' . $fieldName . '\')[0].parentNode.style.display = \'none\';';
        }

        return $content;
    }

    /**
     * Get the label for each option - if string starts with 'LLL' it will be translated with LOCAL_LANG
     *
     * The string to use for translation will not be translated if it does not start with 'LLL',
     * so use a full LLL scheme like 'LLL:EXT:imagewidthspecificationwizard/locallang.xml:tt_content.tx_imagewidthspecificationwizard.ownValueLabel'
     *
     * @param string The string to use for translation
     * @param bool Flag to use alternative string if no translation is found
     *
     * @return string The label string - either the original string or the translated value (translated / alternative string if no translation is found but an alternativ string is given / FALSE if no translation is found & an alternativ string isn't given)
     */
    public function getLabel($label, $labelAlternative = false)
    {
        $content = $label;
        if (0 === strpos($label, 'LLL')) {
            $content = LocalizationUtility::translate($label, $this->extKey);
            if (false === $content && !empty($labelAlternative)) {
                $content = $labelAlternative;
            }
        }

        return $content;
    }
}
